<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\QuizDay;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function home(): View
    {
        return view('home');
    }

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
        $questions = collect();

        if ($quizDay) {
            $attempt = Attempt::query()
                ->where('quiz_day_id', $quizDay->id)
                ->where('user_id', $request->user()->id)
                ->first();

            if ($attempt && $attempt->status === 'in_progress') {
                $expiresAt = Carbon::parse($attempt->expires_at);
                if ($now->lessThan($expiresAt)) {
                    $remainingSeconds = $now->diffInSeconds($expiresAt);
                    $questions = $quizDay->questions()
                        ->with(['choices' => function ($query) {
                            $query->orderBy('order_index');
                        }])
                        ->orderBy('order_index')
                        ->get();
                }
            }
        }

        return view('quiz.today', [
            'quizDay' => $quizDay,
            'attempt' => $attempt,
            'remainingSeconds' => $remainingSeconds,
            'questions' => $questions,
            'now' => $now,
        ]);
    }

    public function startAttempt(Request $request, QuizDay $quizDay): RedirectResponse
    {
        $now = Carbon::now();

        if (! $quizDay->is_published || $quizDay->start_at > $now || $quizDay->end_at < $now) {
            return redirect('/quiz/today')->with('status', 'Quiz not available');
        }

        $user = $request->user();
        if (! $user) {
            return redirect('/quiz/today')->with('status', 'Unauthorized');
        }

        $created = false;

        try {
            $attempt = DB::transaction(function () use ($quizDay, $user, $now, &$created) {
                $existing = Attempt::query()
                    ->where('quiz_day_id', $quizDay->id)
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    return $existing;
                }

                $created = true;

                return Attempt::query()->create([
                    'quiz_day_id' => $quizDay->id,
                    'user_id' => $user->id,
                    'started_at' => $now,
                    'expires_at' => $now->copy()->addSeconds($quizDay->duration_seconds),
                    'status' => 'in_progress',
                    'score' => 0,
                ]);
            });
        } catch (QueryException $exception) {
            $attempt = null;
            if (in_array($exception->getCode(), ['23000', '23505'], true)) {
                $attempt = Attempt::query()
                    ->where('quiz_day_id', $quizDay->id)
                    ->where('user_id', $user->id)
                    ->first();
            }

            if (! $attempt) {
                throw $exception;
            }
        }

        if (! $created) {
            if ($attempt->status === 'in_progress') {
                $expiresAt = Carbon::parse($attempt->expires_at);
                if ($now->lessThan($expiresAt)) {
                    return redirect('/quiz/today')->with('status', 'Attempt already started');
                }

                $attempt->update(['status' => 'expired']);

                return redirect('/quiz/today')->with('status', 'Attempt expired');
            }

            if ($attempt->status === 'submitted') {
                return redirect('/quiz/today')->with('status', 'Already attempted');
            }

            if ($attempt->status === 'expired') {
                return redirect('/quiz/today')->with('status', 'Attempt expired');
            }
        }

        return redirect('/quiz/today')->with('status', 'Attempt started');
    }
}
