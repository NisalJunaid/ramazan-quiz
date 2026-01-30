<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\QuizDay;
use App\Models\Question;
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
        $today = Carbon::today();
        $now = Carbon::now();

        $quizDay = QuizDay::query()
            ->whereDate('quiz_date', $today)
            ->whereHas('quizRange', function ($query) {
                $query->where('is_published', true);
            })
            ->first();

        $isActive = $quizDay
            && $quizDay->start_at <= $now
            && $quizDay->end_at >= $now;

        return view('quiz.home', [
            'quizDay' => $quizDay,
            'isActive' => $isActive,
        ]);
    }

    public function showTodayQuiz(Request $request): View
    {
        $now = Carbon::now();
        $today = Carbon::today();

        $quizDay = QuizDay::query()
            ->whereDate('quiz_date', $today)
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->whereHas('quizRange', function ($query) {
                $query->where('is_published', true);
            })
            ->first();

        $attempt = null;
        $remainingSeconds = null;
        $question = null;
        $selectedChoiceId = null;
        $correctChoiceId = null;
        $daysProgress = collect();
        $totalDays = 0;
        $answeredCorrectCount = 0;
        $answeredWrongCount = 0;
        $missedCount = 0;
        $remainingCount = 0;
        $currentScore = 0;
        $maxPossibleScore = null;
        $currentDayNumber = null;

        if ($quizDay) {
            $quizDay->load('quizRange');
            $quizRange = $quizDay->quizRange;

            if ($quizRange) {
                $quizDays = $quizRange->quizDays()
                    ->orderBy('quiz_date')
                    ->get();
                $totalDays = $quizDays->count();

                $attemptsByDay = Attempt::query()
                    ->where('user_id', $request->user()->id)
                    ->whereIn('quiz_day_id', $quizDays->pluck('id'))
                    ->get()
                    ->keyBy('quiz_day_id');

                $currentScore = $attemptsByDay
                    ->where('status', 'submitted')
                    ->sum('score');

                $maxPossibleScore = Question::query()
                    ->whereIn('quiz_day_id', $quizDays->pluck('id'))
                    ->sum('points');

                $daysProgress = $quizDays->map(function (QuizDay $day, int $index) use (
                    $now,
                    $today,
                    $attemptsByDay,
                    &$answeredCorrectCount,
                    &$answeredWrongCount,
                    &$missedCount,
                    &$remainingCount
                ) {
                    // Status mapping: treat expired or missing attempts as missed; remaining counts only future days.
                    $dayDate = Carbon::parse($day->quiz_date);
                    $isToday = $dayDate->isSameDay($today);
                    $attempt = $attemptsByDay->get($day->id);
                    $isSubmitted = $attempt && $attempt->status === 'submitted';
                    $isExpired = $attempt && $attempt->status === 'expired';
                    $expiresAt = $attempt?->expires_at ? Carbon::parse($attempt->expires_at) : null;

                    if ($attempt && $attempt->status === 'in_progress' && $expiresAt && $now->greaterThanOrEqualTo($expiresAt)) {
                        $isExpired = true; // Safety: in-progress attempts past expiry are treated as expired.
                    }

                    $isCorrect = $isSubmitted && $attempt->score > 0;

                    if ($dayDate->lt($today)) {
                        $status = $isSubmitted ? ($isCorrect ? 'correct' : 'wrong') : 'missed';
                    } elseif ($isToday) {
                        $status = $isSubmitted ? ($isCorrect ? 'correct' : 'wrong') : ($isExpired ? 'missed' : 'today');
                    } else {
                        $status = 'upcoming';
                    }

                    if ($status === 'correct') {
                        $answeredCorrectCount += 1;
                    } elseif ($status === 'wrong') {
                        $answeredWrongCount += 1;
                    } elseif ($status === 'missed') {
                        $missedCount += 1;
                    } elseif ($status === 'upcoming') {
                        $remainingCount += 1; // Only future days count as remaining.
                    }

                    return [
                        'id' => $day->id,
                        'label' => $index + 1,
                        'date' => $day->quiz_date,
                        'status' => $status,
                        'is_today' => $isToday,
                    ];
                });

                $currentDayNumber = optional($daysProgress->firstWhere('is_today', true))['label'];
            }

            $attempt = Attempt::query()
                ->where('quiz_day_id', $quizDay->id)
                ->where('user_id', $request->user()->id)
                ->first();

            if ($attempt && $attempt->status === 'in_progress') {
                $expiresAt = Carbon::parse($attempt->expires_at);
                if ($now->lessThan($expiresAt)) {
                    $remainingSeconds = $now->diffInSeconds($expiresAt);
                    $question = $quizDay->question()
                        ->with(['choices' => function ($query) {
                            $query->orderBy('order_index');
                        }])
                        ->first();
                }
            }

            if ($attempt && $attempt->status === 'submitted') {
                $attempt->load(['answers' => function ($query) {
                    $query->with('choice');
                }]);

                $question = $question ?: $quizDay->question()
                    ->with(['choices' => function ($query) {
                        $query->orderBy('order_index');
                    }])
                    ->first();

                if ($question) {
                    $correctChoiceId = $question->choices
                        ->firstWhere('is_correct', true)
                        ?->id;
                    $selectedChoiceId = $attempt->answers->first()?->choice_id;
                }
            }
        }

        return view('quiz.today', [
            'quizDay' => $quizDay,
            'attempt' => $attempt,
            'remainingSeconds' => $remainingSeconds,
            'question' => $question,
            'now' => $now,
            'totalDays' => $totalDays,
            'daysProgress' => $daysProgress,
            'answeredCorrectCount' => $answeredCorrectCount,
            'answeredWrongCount' => $answeredWrongCount,
            'missedCount' => $missedCount,
            'remainingCount' => $remainingCount,
            'currentScore' => $currentScore,
            'maxPossibleScore' => $maxPossibleScore,
            'selectedChoiceId' => $selectedChoiceId,
            'correctChoiceId' => $correctChoiceId,
            'currentDayNumber' => $currentDayNumber,
        ]);
    }

    public function startAttempt(Request $request, QuizDay $quizDay): RedirectResponse
    {
        $now = Carbon::now();

        $quizDay->load('quizRange');

        if (! $quizDay->quizRange?->is_published || $quizDay->start_at > $now || $quizDay->end_at < $now) {
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
