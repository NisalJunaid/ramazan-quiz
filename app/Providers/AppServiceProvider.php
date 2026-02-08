<?php

namespace App\Providers;

use App\Models\Ad;
use App\Models\AdSlot;
use App\Models\Font;
use App\Models\QuizDay;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $now = Carbon::now();
            $today = Carbon::today();

            $quizDay = QuizDay::query()
                ->whereDate('quiz_date', $today)
                ->where('start_at', '<=', $now)
                ->where('end_at', '>=', $now)
                ->whereHas('quizRange', function ($query) {
                    $query->where('is_published', true)
                        ->where('is_visible', true);
                })
                ->with('quizRange')
                ->first();

            $leaderboardIsPublic = $quizDay?->quizRange?->leaderboard_is_public ?? false;
            $isAdmin = auth()->user()?->role === 'admin';
            $fonts = Schema::hasTable('fonts')
                ? Font::query()->orderBy('name')->get()
                : collect();
            $themeSettings = Schema::hasTable('settings')
                ? Setting::current()
                : null;
            $homeTopAd = $this->resolveHomeTopAd($now);

            $view->with([
                'leaderboardIsPublic' => $leaderboardIsPublic,
                'canViewLeaderboard' => $leaderboardIsPublic || $isAdmin,
                'fonts' => $fonts,
                'themeSettings' => $themeSettings,
                'homeTopAd' => $homeTopAd,
            ]);
        });
    }

    private function resolveHomeTopAd(Carbon $now): ?array
    {
        if (
            ! Schema::hasTable('ads')
            || ! Schema::hasTable('ad_slots')
            || ! Schema::hasTable('ad_slot_ads')
        ) {
            return null;
        }

        $slot = AdSlot::query()
            ->where('key', 'home_top')
            ->with(['fixedAd', 'ads'])
            ->first();

        if (! $slot) {
            return null;
        }

        $selectedAd = $this->resolveSlotAd($slot, $now);

        if (! $selectedAd) {
            return null;
        }

        return [
            'image_url' => Storage::disk('public')->url($selectedAd->image_path),
            'url' => $selectedAd->target_url,
            'alt' => $selectedAd->alt_text ?: ($selectedAd->name ?: 'Advertisement'),
        ];
    }

    private function resolveSlotAd(AdSlot $slot, Carbon $now): ?Ad
    {
        if ($slot->mode === 'fixed') {
            $fixedAd = $slot->fixedAd;

            return $fixedAd && $fixedAd->isActiveAt($now) ? $fixedAd : null;
        }

        $eligibleAds = $slot->ads
            ->filter(fn (Ad $ad) => ($ad->pivot?->is_enabled ?? false) && $ad->isActiveAt($now))
            ->sortBy(fn (Ad $ad) => [$ad->pivot?->sort_order ?? 0, $ad->id])
            ->values();

        if ($eligibleAds->isEmpty()) {
            return null;
        }

        $rotationSeconds = $slot->rotation_seconds;

        if ($rotationSeconds && $rotationSeconds > 0) {
            $cacheKey = "ad_slots.{$slot->id}.rotation";
            $cachedAdId = Cache::get($cacheKey);
            $cachedAd = $cachedAdId ? $eligibleAds->firstWhere('id', $cachedAdId) : null;

            if ($cachedAd) {
                return $cachedAd;
            }

            $selectedAd = $this->selectRotatingAd($eligibleAds, $slot);

            if ($selectedAd) {
                Cache::put($cacheKey, $selectedAd->id, now()->addSeconds($rotationSeconds));
            }

            return $selectedAd;
        }

        return $this->selectRotatingAd($eligibleAds, $slot);
    }

    private function selectRotatingAd(Collection $eligibleAds, AdSlot $slot): ?Ad
    {
        if ($eligibleAds->isEmpty()) {
            return null;
        }

        if ($slot->rotation_strategy === 'sequential') {
            return $this->selectSequentialAd($eligibleAds, $slot);
        }

        return $this->selectWeightedRandomAd($eligibleAds);
    }

    private function selectSequentialAd(Collection $eligibleAds, AdSlot $slot): Ad
    {
        $cacheKey = "ad_slots.{$slot->id}.last_index";
        $lastIndex = (int) Cache::get($cacheKey, -1);
        $nextIndex = ($lastIndex + 1) % $eligibleAds->count();

        Cache::put($cacheKey, $nextIndex, now()->addDay());

        return $eligibleAds->get($nextIndex);
    }

    private function selectWeightedRandomAd(Collection $eligibleAds): Ad
    {
        $totalWeight = $eligibleAds->sum(function (Ad $ad) {
            return max(1, (int) ($ad->pivot?->weight ?? 1));
        });

        $pick = random_int(1, $totalWeight);
        $running = 0;

        foreach ($eligibleAds as $ad) {
            $running += max(1, (int) ($ad->pivot?->weight ?? 1));

            if ($pick <= $running) {
                return $ad;
            }
        }

        return $eligibleAds->first();
    }
}
