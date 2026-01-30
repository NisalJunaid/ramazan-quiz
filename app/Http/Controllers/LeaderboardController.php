<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\QuizDay;
use Carbon\Carbon;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function todayLeaderboard(): View
    {
        $today = Carbon::today();

        $quizDay = QuizDay::query()
            ->whereDate('quiz_date', $today)
            ->first();

        if (! $quizDay) {
            $quizDay = QuizDay::query()
                ->where('is_published', true)
                ->orderByDesc('quiz_date')
                ->first();
        }

        $attempts = collect();

        if ($quizDay) {
            $attempts = Attempt::query()
                ->with('user')
                ->where('quiz_day_id', $quizDay->id)
                ->where('status', 'submitted')
                ->orderByDesc('score')
                ->orderBy('submitted_at')
                ->get();
        }

        return view('leaderboard', [
            'quizDay' => $quizDay,
            'attempts' => $attempts,
        ]);
    }
}
