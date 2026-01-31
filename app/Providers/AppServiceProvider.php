<?php

namespace App\Providers;

use App\Models\Font;
use App\Models\QuizDay;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
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

            $view->with([
                'leaderboardIsPublic' => $leaderboardIsPublic,
                'canViewLeaderboard' => $leaderboardIsPublic || $isAdmin,
                'fonts' => $fonts,
                'themeSettings' => $themeSettings,
            ]);
        });
    }
}
