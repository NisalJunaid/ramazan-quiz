<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ThemeSettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.theme.index', [
            'settings' => Setting::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $hexColorRule = ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'];
        $validated = $request->validate([
            'body_background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'body_background_fit' => ['required', Rule::in(['cover', 'contain', 'fill'])],
            'body_background_overlay_opacity' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'home_hero_background_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'home_hero_background_remove' => ['nullable', 'boolean'],
            'home_hero_background_opacity' => ['nullable', 'numeric', 'min:0', 'max:1'],
            'home_hero_background_fit' => ['required', Rule::in(['cover', 'contain', 'fill', 'none', 'scale-down'])],
            'home_hero_background_position' => ['required', Rule::in(['center', 'top', 'bottom', 'left', 'right'])],
            'home_hero_background_repeat' => ['required', Rule::in(['no-repeat', 'repeat', 'repeat-x', 'repeat-y'])],
            'home_hero_content_offset' => ['nullable', 'integer', 'min:0'],
            'home_hero_content_offset_unit' => ['required', Rule::in(['px', 'rem', '%'])],
            'app_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'logo_width' => ['nullable', 'numeric', 'min:0'],
            'logo_width_unit' => ['required', Rule::in(['px', '%', 'rem', 'vw'])],
            'logo_height' => ['nullable', 'numeric', 'min:0'],
            'logo_height_unit' => ['nullable', Rule::in(['px', '%', 'rem', 'vw'])],
            'home_logo_width' => ['nullable', 'numeric', 'min:0'],
            'home_logo_width_unit' => ['required', Rule::in(['px', '%', 'rem', 'vw'])],
            'home_logo_height' => ['nullable', 'numeric', 'min:0'],
            'home_logo_height_unit' => ['nullable', Rule::in(['px', '%', 'rem', 'vw'])],
            'primary_color' => $hexColorRule,
            'primary_hover_color' => $hexColorRule,
            'accent_color' => $hexColorRule,
            'text_color' => $hexColorRule,
            'border_color' => $hexColorRule,
            'ring_color' => ['nullable', 'string', 'max:32'],
            'focus_ring_color' => ['nullable', 'string', 'max:32'],
            'button_radius' => ['nullable', 'string', 'max:32'],
            'card_radius' => ['nullable', 'string', 'max:32'],
        ]);

        $setting = Setting::current();

        $payload = [
            'body_background_fit' => $validated['body_background_fit'],
            'body_background_overlay_opacity' => $validated['body_background_overlay_opacity'] ?? null,
            'home_hero_background_opacity' => $validated['home_hero_background_opacity'] ?? null,
            'home_hero_background_fit' => $validated['home_hero_background_fit'],
            'home_hero_background_position' => $validated['home_hero_background_position'],
            'home_hero_background_repeat' => $validated['home_hero_background_repeat'],
            'home_hero_content_offset' => $validated['home_hero_content_offset'] ?? null,
            'home_hero_content_offset_unit' => $validated['home_hero_content_offset_unit'],
            'logo_width' => $validated['logo_width'],
            'logo_width_unit' => $validated['logo_width_unit'],
            'logo_height' => $validated['logo_height'],
            'logo_height_unit' => $validated['logo_height_unit'] ?: null,
            'home_logo_width' => $validated['home_logo_width'],
            'home_logo_width_unit' => $validated['home_logo_width_unit'],
            'home_logo_height' => $validated['home_logo_height'],
            'home_logo_height_unit' => $validated['home_logo_height_unit'] ?: null,
            'primary_color' => $validated['primary_color'] ?? null,
            'primary_hover_color' => $validated['primary_hover_color'] ?? null,
            'accent_color' => $validated['accent_color'] ?? null,
            'text_color' => $validated['text_color'] ?? null,
            'border_color' => $validated['border_color'] ?? null,
            'ring_color' => $validated['ring_color'] ?? null,
            'focus_ring_color' => $validated['focus_ring_color'] ?? null,
            'button_radius' => $validated['button_radius'] ?? null,
            'card_radius' => $validated['card_radius'] ?? null,
        ];

        if ($request->hasFile('body_background_image')) {
            if ($setting->body_background_image) {
                Storage::disk('public')->delete($setting->body_background_image);
            }

            $payload['body_background_image'] = $request->file('body_background_image')
                ->store('theme', 'public');
        }

        if ($request->hasFile('home_hero_background_image')) {
            if ($setting->home_hero_background_image) {
                Storage::disk('public')->delete($setting->home_hero_background_image);
            }

            $payload['home_hero_background_image'] = $request->file('home_hero_background_image')
                ->store('theme', 'public');
        } elseif ($request->boolean('home_hero_background_remove')) {
            if ($setting->home_hero_background_image) {
                Storage::disk('public')->delete($setting->home_hero_background_image);
            }

            $payload['home_hero_background_image'] = null;
        }

        if ($request->hasFile('app_logo')) {
            if ($setting->app_logo) {
                Storage::disk('public')->delete($setting->app_logo);
            }

            $payload['app_logo'] = $request->file('app_logo')->store('theme', 'public');
        }

        $setting->update($payload);

        return redirect()
            ->route('admin.theme.index')
            ->with('status', text('admin.theme.updated', 'Theme settings updated.'));
    }
}
