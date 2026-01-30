<?php

namespace App\Http\Controllers\Admin;

use App\Events\QuizDayChanged;
use App\Models\Question;
use App\Models\QuizDay;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function save(Request $request, QuizDay $quizDay): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1'],
            'order_index' => ['required', 'integer', 'min:1'],
            'answers' => ['required', 'array', 'size:4'],
            'answers.*.text' => ['required', 'string'],
            'correct_answer' => ['required', 'integer', 'between:0,3'],
        ]);

        $hasSubmittedAttempts = $quizDay->attempts()
            ->where('status', 'submitted')
            ->exists();

        if ($hasSubmittedAttempts) {
            return back()
                ->withErrors(['quiz_day' => 'Cannot edit a quiz after users submit attempts.'])
                ->withInput();
        }

        DB::transaction(function () use ($quizDay, $validated) {
            $questionData = [
                'question_text' => $validated['question_text'],
                'points' => $validated['points'],
                'order_index' => $validated['order_index'],
            ];

            $question = $quizDay->question;

            if ($question) {
                $question->update($questionData);
            } else {
                $question = $quizDay->question()->create($questionData);
            }

            $question->choices()->delete();

            foreach ($validated['answers'] as $index => $answer) {
                $question->choices()->create([
                    'choice_text' => $answer['text'],
                    'order_index' => $index + 1,
                    'is_correct' => $index === (int) $validated['correct_answer'],
                ]);
            }
        });

        event(new QuizDayChanged($quizDay->id, $quizDay->quiz_date));

        return back()->with('status', 'Question and answers saved.');
    }
}
