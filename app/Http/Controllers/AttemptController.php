<?php

namespace App\Http\Controllers;

use App\Events\LeaderboardChanged;
use App\Models\Answer;
use App\Models\Attempt;
use App\Models\QuizDay;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttemptController extends Controller
{
    public function submitAttempt(Request $request, Attempt $attempt): RedirectResponse
    {
        $user = $request->user();
        if (! $user || $attempt->user_id !== $user->id) {
            return redirect('/quiz/today')->with('status', 'Unauthorized');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect('/quiz/today')->with('status', 'Attempt not in progress');
        }

        $now = Carbon::now();
        $expiresAt = Carbon::parse($attempt->expires_at);
        if ($now->greaterThan($expiresAt)) {
            $attempt->update(['status' => 'expired']);

            return redirect('/quiz/today')->with('status', 'Attempt expired');
        }

        $quizDay = QuizDay::query()
            ->with([
                'question.choices' => function ($query) {
                    $query->orderBy('order_index');
                },
            ])
            ->find($attempt->quiz_day_id);

        if (! $quizDay) {
            return redirect('/quiz/today')->with('status', 'Quiz not available');
        }

        $question = $quizDay->question;
        if (! $question) {
            return redirect('/quiz/today')->with('status', 'Quiz not available');
        }

        $submittedAnswers = $request->input('answers', []);
        $score = 0;

        DB::transaction(function () use ($attempt, $question, $submittedAnswers, &$score, $now) {
            $choiceId = $submittedAnswers[$question->id] ?? null;
            $choice = null;
            if ($choiceId) {
                $choice = $question->choices->firstWhere('id', (int) $choiceId);
            }

            $isCorrect = $choice ? (bool) $choice->is_correct : false;
            $pointsAwarded = $isCorrect ? $question->points : 0;

            Answer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'choice_id' => $choice?->id,
                'is_correct' => $isCorrect,
                'points_awarded' => $pointsAwarded,
            ]);

            $score += $pointsAwarded;

            $attempt->update([
                'submitted_at' => $now,
                'score' => $score,
                'status' => 'submitted',
            ]);
        });

        event(new LeaderboardChanged($attempt->quiz_day_id));

        return redirect('/quiz/today')
            ->with('status', 'Submitted')
            ->with('score', $score);
    }
}
