<?php

namespace App\Http\Controllers\Admin;

use App\Events\QuizRangeChanged;
use App\Models\QuizDay;
use App\Models\QuizRange;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminQuizController extends Controller
{
    public function index(): View
    {
        $quizRanges = QuizRange::query()
            ->withCount('quizDays')
            ->orderByDesc('start_date')
            ->get();

        return view('admin.quizzes.index', [
            'quizRanges' => $quizRanges,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'duration_seconds' => ['required', 'integer', 'min:1'],
            'is_published' => ['nullable', 'boolean'],
            'is_visible' => ['nullable', 'boolean'],
            'leaderboard_is_public' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_visible'] = $request->boolean('is_visible', true);
        $validated['leaderboard_is_public'] = $request->boolean('leaderboard_is_public', true);

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->startOfDay();
        $validated['days_count'] = $startDate->diffInDays($endDate) + 1;

        DB::transaction(function () use ($validated, $startDate, $endDate) {
            $quizRange = QuizRange::create([
                'title' => $validated['title'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'days_count' => $validated['days_count'],
                'start_at' => $startDate->copy()->startOfDay(),
                'end_at' => $endDate->copy()->endOfDay(),
                'duration_seconds' => $validated['duration_seconds'],
                'is_published' => $validated['is_published'],
                'is_visible' => $validated['is_visible'],
                'leaderboard_is_public' => $validated['leaderboard_is_public'],
            ]);

            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $quizDay = QuizDay::create([
                    'quiz_range_id' => $quizRange->id,
                    'quiz_date' => $currentDate->toDateString(),
                    'title' => $quizRange->title,
                    'start_at' => $currentDate->copy()->startOfDay(),
                    'end_at' => $currentDate->copy()->endOfDay(),
                    'duration_seconds' => $quizRange->duration_seconds,
                    'is_published' => $quizRange->is_published,
                ]);

                $quizDay->question()->create([
                    'question_text' => '',
                    'points' => 1,
                    'order_index' => 1,
                ]);

                $currentDate->addDay();
            }
        });

        event(new QuizRangeChanged());

        return redirect()
            ->route('admin.quizzes.index')
            ->with('status', 'Quiz range created.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $quizRange = QuizRange::findOrFail($id);
        $validated = $request->validate([
            'title' => ['required', 'string'],
            'duration_seconds' => ['required', 'integer', 'min:1'],
            'is_published' => ['nullable', 'boolean'],
            'is_visible' => ['nullable', 'boolean'],
            'leaderboard_is_public' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_visible'] = $request->boolean('is_visible', true);
        $validated['leaderboard_is_public'] = $request->boolean('leaderboard_is_public');

        DB::transaction(function () use ($quizRange, $validated) {
            $quizRange->update([
                'title' => $validated['title'],
                'duration_seconds' => $validated['duration_seconds'],
                'is_published' => $validated['is_published'],
                'is_visible' => $validated['is_visible'],
                'leaderboard_is_public' => $validated['leaderboard_is_public'],
            ]);

            $quizRange->quizDays()->update([
                'title' => $validated['title'],
                'duration_seconds' => $validated['duration_seconds'],
                'is_published' => $validated['is_published'],
            ]);
        });

        event(new QuizRangeChanged($quizRange->id));

        return redirect()
            ->route('admin.quizzes.index')
            ->with('status', 'Quiz range updated.');
    }

    public function showDays(QuizRange $quizRange): View
    {
        $quizRange->load(['quizDays' => function ($query) {
            $query->orderBy('quiz_date');
        }, 'quizDays.question']);

        return view('admin.quizzes.days', [
            'quizRange' => $quizRange,
        ]);
    }

    public function editDay(QuizRange $quizRange, QuizDay $quizDay): View
    {
        abort_unless($quizDay->quiz_range_id === $quizRange->id, 404);

        $quizDay->load(['question.choices' => function ($query) {
            $query->orderBy('order_index');
        }]);

        $hasSubmittedAttempts = $quizDay->attempts()
            ->where('status', 'submitted')
            ->exists();

        return view('admin.quizzes.day-edit', [
            'quizRange' => $quizRange,
            'quizDay' => $quizDay,
            'hasSubmittedAttempts' => $hasSubmittedAttempts,
        ]);
    }
}
