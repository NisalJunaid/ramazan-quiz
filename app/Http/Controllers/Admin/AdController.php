<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdController extends Controller
{
    public function index(): View
    {
        return view('admin.ads.index', [
            'ads' => Ad::query()->latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.ads.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'target_url' => ['nullable', 'url', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $imagePath = $request->file('image')->store('ads', 'public');

        Ad::query()->create([
            'name' => $validated['name'] ?? null,
            'image_path' => $imagePath,
            'target_url' => $validated['target_url'] ?? null,
            'alt_text' => $validated['alt_text'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
        ]);

        return redirect()
            ->route('admin.ads.index')
            ->with('status', text('admin.ads.created', 'Ad created successfully.'));
    }

    public function edit(Ad $ad): View
    {
        return view('admin.ads.edit', [
            'ad' => $ad,
        ]);
    }

    public function update(Request $request, Ad $ad): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'target_url' => ['nullable', 'url', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $imagePath = $ad->image_path;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('ads', 'public');

            if ($ad->image_path) {
                Storage::disk('public')->delete($ad->image_path);
            }
        }

        $ad->update([
            'name' => $validated['name'] ?? null,
            'image_path' => $imagePath,
            'target_url' => $validated['target_url'] ?? null,
            'alt_text' => $validated['alt_text'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
        ]);

        return redirect()
            ->route('admin.ads.index')
            ->with('status', text('admin.ads.updated', 'Ad updated successfully.'));
    }

    public function destroy(Ad $ad): RedirectResponse
    {
        if ($ad->image_path) {
            Storage::disk('public')->delete($ad->image_path);
        }

        $ad->delete();

        return redirect()
            ->route('admin.ads.index')
            ->with('status', text('admin.ads.deleted', 'Ad deleted successfully.'));
    }
}
