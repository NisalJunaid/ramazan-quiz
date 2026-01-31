<?php

namespace App\Http\Controllers\Admin;

use App\Events\QuizDayChanged;
use App\Models\Choice;
use App\Models\Question;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminChoiceController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
            'choice_text' => ['required', 'string'],
            'is_correct' => ['nullable', 'boolean'],
            'order_index' => ['required', 'integer'],
        ]);

        $question = Question::with('quizDay')->findOrFail($validated['question_id']);
        $hasSubmittedAttempts = $question->quizDay
            ->attempts()
            ->where('status', 'submitted')
            ->exists();

        if ($hasSubmittedAttempts) {
            return back()
                ->withErrors(['question_id' => text('admin.choices.locked', 'Cannot edit a quiz after users submit attempts.')])
                ->withInput();
        }

        $isCorrect = $request->boolean('is_correct');

        DB::transaction(function () use ($question, $validated, $isCorrect) {
            if ($isCorrect) {
                Choice::query()
                    ->where('question_id', $question->id)
                    ->update(['is_correct' => false]);
            }

            Choice::create([
                'question_id' => $question->id,
                'choice_text' => $validated['choice_text'],
                'is_correct' => $isCorrect,
                'order_index' => $validated['order_index'],
            ]);
        });

        event(new QuizDayChanged($question->quiz_day_id, $question->quizDay?->quiz_date));

        return redirect()
            ->route('admin.quizzes.days.edit', [$question->quizDay?->quiz_range_id, $question->quiz_day_id])
            ->with('status', text('admin.choices.added', 'Choice added.'));
    }

    public function update(Request $request, Choice $choice): RedirectResponse
    {
        $validated = $request->validate([
            'choice_text' => ['required', 'string'],
            'order_index' => ['required', 'integer', 'min:1'],
            'is_correct' => ['nullable', 'boolean'],
        ]);

        $question = $choice->question()->with('quizDay')->firstOrFail();
        $hasSubmittedAttempts = $question->quizDay
            ->attempts()
            ->where('status', 'submitted')
            ->exists();

        if ($hasSubmittedAttempts) {
            return back()
                ->withErrors(['choice_id' => text('admin.choices.locked', 'Cannot edit a quiz after users submit attempts.')])
                ->withInput();
        }

        $isCorrect = $request->boolean('is_correct');

        DB::transaction(function () use ($choice, $question, $validated, $isCorrect) {
            if ($isCorrect) {
                Choice::query()
                    ->where('question_id', $question->id)
                    ->update(['is_correct' => false]);
            }

            $choice->update([
                'choice_text' => $validated['choice_text'],
                'order_index' => $validated['order_index'],
                'is_correct' => $isCorrect,
            ]);
        });

        event(new QuizDayChanged($question->quiz_day_id, $question->quizDay?->quiz_date));

        return redirect()
            ->route('admin.quizzes.days.edit', [$question->quizDay?->quiz_range_id, $question->quiz_day_id])
            ->with('status', text('admin.choices.updated', 'Choice updated.'));
    }
}
