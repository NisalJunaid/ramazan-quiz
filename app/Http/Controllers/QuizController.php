<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\QuizDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function showTodayQuiz(Request $request): View
    {
        $now = Carbon::now();

        $quizDay = QuizDay::query()
            ->where('is_published', true)
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->first();

        $attempt = null;
        $remainingSeconds = null;

        if ($quizDay) {
            $attempt = Attempt::query()
                ->where('quiz_day_id', $quizDay->id)
                ->where('user_id', $request->user()->id)
                ->first();

            if ($attempt && $attempt->status === 'in_progress') {
                $expiresAt = Carbon::parse($attempt->expires_at);
                if ($now->lessThan($expiresAt)) {
                    $remainingSeconds = $now->diffInSeconds($expiresAt);
                }
            }
        }

        return view('quiz.today', [
            'quizDay' => $quizDay,
            'attempt' => $attempt,
            'remainingSeconds' => $remainingSeconds,
            'now' => $now,
        ]);
    }

    public function startAttempt(): Response
    {
        return response('Not implemented.', 501);
    }
}
