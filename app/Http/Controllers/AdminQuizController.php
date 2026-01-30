<?php

namespace App\Http\Controllers;

use App\Models\QuizDay;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminQuizController extends Controller
{
    public function index(): View
    {
        $quizDays = QuizDay::query()
            ->orderByDesc('quiz_date')
            ->get();

        $questions = Question::query()
            ->with('quizDay')
            ->orderByDesc('quiz_day_id')
            ->orderBy('order_index')
            ->get();

        return view('admin.quizzes.index', [
            'quizDays' => $quizDays,
            'questions' => $questions,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'quiz_date' => ['required', 'date', Rule::unique('quiz_days', 'quiz_date')],
            'title' => ['required', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'duration_seconds' => ['required', 'integer', 'min:1'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        QuizDay::create($validated);

        return redirect()
            ->route('admin.quizzes.index')
            ->with('status', 'Quiz day created.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $quizDay = QuizDay::findOrFail($id);

        $hasSubmittedAttempts = $quizDay->attempts()
            ->where('status', 'submitted')
            ->exists();

        if ($hasSubmittedAttempts) {
            return back()
                ->withErrors(['quiz_day' => 'Cannot edit a quiz after users submit attempts.'])
                ->withInput();
        }

        $validated = $request->validate([
            'quiz_date' => ['required', 'date'],
            'title' => ['required', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'duration_seconds' => ['required', 'integer', 'min:1'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        $quizDay->update($validated);

        return redirect()
            ->route('admin.quizzes.index')
            ->with('status', 'Quiz day updated.');
    }
}
