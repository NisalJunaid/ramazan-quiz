<?php

namespace App\Http\Controllers\Admin;

use App\Events\QuizDayChanged;
use App\Models\Question;
use App\Models\QuizDay;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminQuestionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'quiz_day_id' => ['required', 'integer', 'exists:quiz_days,id'],
            'question_text' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1'],
            'order_index' => ['required', 'integer'],
        ]);

        $quizDay = QuizDay::findOrFail($validated['quiz_day_id']);
        $hasSubmittedAttempts = $quizDay->attempts()
            ->where('status', 'submitted')
            ->exists();

        if ($hasSubmittedAttempts) {
            return back()
                ->withErrors(['quiz_day_id' => 'Cannot edit a quiz after users submit attempts.'])
                ->withInput();
        }

        Question::create($validated);

        event(new QuizDayChanged($quizDay->id, $quizDay->quiz_date));

        return redirect()
            ->route('admin.quizzes.index')
            ->with('status', 'Question added.');
    }

    public function update(Request $request, QuizDay $quizDay): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1'],
            'order_index' => ['required', 'integer', 'min:1'],
        ]);

        $hasSubmittedAttempts = $quizDay->attempts()
            ->where('status', 'submitted')
            ->exists();

        if ($hasSubmittedAttempts) {
            return back()
                ->withErrors(['quiz_day' => 'Cannot edit a quiz after users submit attempts.'])
                ->withInput();
        }

        $question = $quizDay->question;

        if ($question) {
            $question->update($validated);
        } else {
            $quizDay->question()->create($validated);
        }

        event(new QuizDayChanged($quizDay->id, $quizDay->quiz_date));

        return redirect()
            ->route('admin.quizzes.days.edit', [$quizDay->quiz_range_id, $quizDay->id])
            ->with('status', 'Question updated.');
    }
}
