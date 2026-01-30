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
            ->whereHas('quizRange', function ($query) {
                $query->where('is_published', true);
            })
            ->first();

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

        return view('leaderboard.today', [
            'quizDay' => $quizDay,
            'attempts' => $attempts,
        ]);
    }
}
