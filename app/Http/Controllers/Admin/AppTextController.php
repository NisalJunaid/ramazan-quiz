<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppText;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppTextController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $texts = AppText::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('key', 'like', "%{$search}%");
            })
            ->orderBy('key')
            ->orderBy('locale')
            ->get();

        $fallbacks = AppText::query()
            ->whereNull('locale')
            ->pluck('value', 'key');

        $groupedTexts = $texts
            ->groupBy(fn (AppText $text) => $this->groupLabelForKey($text->key))
            ->map(function ($group) use ($fallbacks) {
                return $group->map(function (AppText $text) use ($fallbacks) {
                    $text->fallback = $text->locale ? ($fallbacks[$text->key] ?? null) : null;

                    return $text;
                });
            });

        return view('admin.texts.index', [
            'groupedTexts' => $groupedTexts,
            'search' => $search,
            'settings' => Setting::current(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string'],
            'locale' => ['nullable', 'string', 'max:20'],
        ]);

        $locale = $validated['locale'] ?: null;

        $request->validate([
            'key' => [
                Rule::unique('app_texts')->where(function ($query) use ($locale) {
                    return $query->where('locale', $locale);
                }),
            ],
        ]);

        AppText::query()->create([
            'key' => $validated['key'],
            'value' => $validated['value'],
            'locale' => $locale,
        ]);

        $this->forgetCache($validated['key'], $locale);

        return redirect()
            ->route('admin.texts.index')
            ->with('status', text('admin.texts.created', 'Text entry created.'));
    }

    public function update(Request $request, AppText $text): RedirectResponse
    {
        $validated = $request->validate([
            'value' => ['required', 'string'],
        ]);

        $text->update([
            'value' => $validated['value'],
        ]);

        $this->forgetCache($text->key, $text->locale);

        return redirect()
            ->route('admin.texts.index')
            ->with('status', text('admin.texts.updated', 'Text entry updated.'));
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'texts' => ['required', 'array'],
            'texts.*.value' => ['nullable', 'string'],
        ]);

        $submittedTexts = $validated['texts'];
        $texts = AppText::query()
            ->whereIn('id', array_map('intval', array_keys($submittedTexts)))
            ->get()
            ->keyBy('id');

        foreach ($submittedTexts as $textId => $payload) {
            $text = $texts->get((int) $textId);

            if (! $text || ! array_key_exists('value', $payload)) {
                continue;
            }

            $value = (string) $payload['value'];

            if ($value === $text->value) {
                continue;
            }

            $text->update([
                'value' => $value,
            ]);

            $this->forgetCache($text->key, $text->locale);
        }

        return redirect()
            ->route('admin.texts.index')
            ->with('status', text('admin.texts.updated', 'Text content updated successfully.'));
    }

    public function destroy(AppText $text): RedirectResponse
    {
        $this->forgetCache($text->key, $text->locale);

        $text->delete();

        return redirect()
            ->route('admin.texts.index')
            ->with('status', text('admin.texts.deleted', 'Text entry deleted.'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $setting = Setting::current();
        $setting->update([
            'is_rtl' => $request->boolean('is_rtl'),
        ]);

        return redirect()
            ->route('admin.texts.index')
            ->with('status', text('admin.texts.rtl_updated', 'RTL setting updated.'));
    }

    private function forgetCache(string $key, ?string $locale): void
    {
        if ($locale) {
            Cache::forget("app_texts.{$locale}.{$key}");
        }

        Cache::forget("app_texts.default.{$key}");
    }

    private function groupLabelForKey(string $key): string
    {
        $groupKey = str_contains($key, '.') ? explode('.', $key, 2)[0] : 'general';
        $normalized = strtolower($groupKey);

        return match ($normalized) {
            'home' => 'Home',
            'quiz' => 'Quiz',
            'admin' => 'Admin',
            'leaderboard' => 'Leaderboard',
            'general' => 'General',
            default => ucfirst($normalized),
        };
    }
}
