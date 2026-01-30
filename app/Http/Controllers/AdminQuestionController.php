<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuizDay;
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

        return redirect()
            ->route('admin.quizzes.index')
            ->with('status', 'Question added.');
    }
}
