<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdSlot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdSlotController extends Controller
{
    public function editHomeTop(): View
    {
        $slot = AdSlot::query()->firstOrCreate(
            ['key' => 'home_top'],
            [
                'mode' => 'fixed',
                'rotation_strategy' => 'random',
                'rotation_seconds' => null,
            ]
        );

        return view('admin.ad-slots.home-top', [
            'slot' => $slot,
            'ads' => Ad::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->orderBy('id')
                ->get(),
            'slotAds' => $slot->ads()->withPivot(['weight', 'sort_order', 'is_enabled'])->get()->keyBy('id'),
        ]);
    }

    public function updateHomeTop(Request $request): RedirectResponse
    {
        $slot = AdSlot::query()->firstOrCreate(
            ['key' => 'home_top'],
            [
                'mode' => 'fixed',
                'rotation_strategy' => 'random',
                'rotation_seconds' => null,
            ]
        );

        $validated = $request->validate([
            'mode' => ['required', Rule::in(['fixed', 'rotating'])],
            'fixed_ad_id' => [
                'nullable',
                Rule::requiredIf(fn () => $request->string('mode')->toString() === 'fixed'),
                Rule::exists('ads', 'id')->where(fn ($query) => $query->where('is_active', true)),
            ],
            'rotation_strategy' => ['required', Rule::in(['random', 'sequential'])],
            'rotation_seconds' => ['nullable', 'integer', 'min:1'],
            'ads' => ['nullable', 'array'],
            'ads.*.weight' => ['nullable', 'integer', 'min:1'],
            'ads.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'ads.*.is_enabled' => ['nullable', 'boolean'],
        ]);

        $slot->update([
            'mode' => $validated['mode'],
            'fixed_ad_id' => $validated['mode'] === 'fixed' ? ($validated['fixed_ad_id'] ?? null) : null,
            'rotation_strategy' => $validated['rotation_strategy'],
            'rotation_seconds' => $validated['rotation_seconds'] ?? null,
        ]);

        if ($validated['mode'] === 'rotating') {
            $submittedAds = $validated['ads'] ?? [];
            $syncData = [];

            foreach ($submittedAds as $adId => $payload) {
                $syncData[$adId] = [
                    'weight' => isset($payload['weight']) ? (int) $payload['weight'] : 1,
                    'sort_order' => isset($payload['sort_order']) ? (int) $payload['sort_order'] : 0,
                    'is_enabled' => isset($payload['is_enabled']) ? (bool) $payload['is_enabled'] : false,
                ];
            }

            $slot->ads()->sync($syncData);
        }

        return redirect()
            ->route('admin.ad-slots.home-top.edit')
            ->with('status', text('admin.ad_slots.updated', 'Homepage ad slot updated.'));
    }
}
