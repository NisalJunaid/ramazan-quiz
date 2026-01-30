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
                $query->where('is_published', true)
                    ->where('is_visible', true);
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

        $activeQuizDays = QuizDay::query()
            ->whereDate('quiz_date', $today)
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->whereHas('quizRange', function ($query) {
                $query->where('is_published', true)
                    ->where('is_visible', true);
            })
            ->with('quizRange')
            ->get();

        $activeQuizRanges = $activeQuizDays
            ->map(fn (QuizDay $day) => $day->quizRange)
            ->filter()
            ->unique('id')
            ->values();

        // Pick the selected range (query param wins) and scope everything to that range only.
        $selectedQuizRangeId = $request->query('quiz_range_id');
        $selectedQuizRange = $selectedQuizRangeId
            ? $activeQuizRanges->firstWhere('id', (int) $selectedQuizRangeId)
            : $activeQuizRanges->first();

        $quizDay = $selectedQuizRange
            ? $activeQuizDays->firstWhere('quiz_range_id', $selectedQuizRange->id)
            : null;

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

        if ($quizDay && $selectedQuizRange) {
            $quizDays = $selectedQuizRange->quizDays()
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
            'activeQuizRanges' => $activeQuizRanges,
            'selectedQuizRange' => $selectedQuizRange,
            'selectedQuizDay' => $quizDay,
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

        $redirect = redirect()->route('quiz.today', [
            'quiz_range_id' => $quizDay->quiz_range_id,
        ]);

        if (
            ! $quizDay->quizRange?->is_published
            || ! $quizDay->quizRange?->is_visible
            || $quizDay->start_at > $now
            || $quizDay->end_at < $now
        ) {
            return $redirect->with('status', 'Quiz not available');
        }

        $user = $request->user();
        if (! $user) {
            return $redirect->with('status', 'Unauthorized');
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
                    return $redirect->with('status', 'Attempt already started');
                }

                $attempt->update(['status' => 'expired']);

                return $redirect->with('status', 'Attempt expired');
            }

            if ($attempt->status === 'submitted') {
                return $redirect->with('status', 'Already attempted');
            }

            if ($attempt->status === 'expired') {
                return $redirect->with('status', 'Attempt expired');
            }
        }

        return $redirect->with('status', 'Attempt started');
    }
}
