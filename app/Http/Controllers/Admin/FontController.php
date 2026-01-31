<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppText;
use App\Models\Font;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FontController extends Controller
{
    public function index(): View
    {
        return view('admin.fonts.index', [
            'fonts' => Font::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'css_family' => ['required', 'string', 'max:255'],
            'css_class' => ['required', 'string', 'max:255', 'unique:fonts,css_class'],
            'source_type' => ['required', 'in:upload,google'],
            'is_rtl_optimized' => ['nullable', 'boolean'],
            'font_file' => ['nullable', 'file', 'mimes:woff,woff2,ttf'],
            'source_url' => ['nullable', 'url'],
        ]);

        if ($validated['source_type'] === 'upload') {
            $request->validate([
                'font_file' => ['required', 'file', 'mimes:woff,woff2,ttf'],
            ]);

            $sourcePath = $request->file('font_file')->store('fonts');
        } else {
            $request->validate([
                'source_url' => ['required', 'url'],
            ]);

            $sourcePath = $validated['source_url'];
        }

        Font::query()->create([
            'name' => $validated['name'],
            'css_family' => $validated['css_family'],
            'css_class' => $validated['css_class'],
            'source_type' => $validated['source_type'],
            'source_path' => $sourcePath,
            'is_rtl_optimized' => $request->boolean('is_rtl_optimized'),
        ]);

        return redirect()
            ->route('admin.fonts.index')
            ->with('status', text('admin.fonts.created', 'Font saved.'));
    }

    public function destroy(Font $font): RedirectResponse
    {
        $texts = AppText::query()
            ->where('font_id', $font->id)
            ->get(['key', 'locale']);

        if ($font->source_type === 'upload' && $font->source_path) {
            Storage::disk('local')->delete($font->source_path);
        }

        $font->delete();

        foreach ($texts as $text) {
            if ($text->locale) {
                Cache::forget("app_texts.{$text->locale}.{$text->key}");
            }

            Cache::forget("app_texts.default.{$text->key}");
        }

        return redirect()
            ->route('admin.fonts.index')
            ->with('status', text('admin.fonts.deleted', 'Font removed.'));
    }
}
