<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\QuizDay;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function todayLeaderboard(): View|RedirectResponse
    {
        $today = Carbon::today();

        $quizDay = QuizDay::query()
            ->whereDate('quiz_date', $today)
            ->whereHas('quizRange', function ($query) {
                $query->where('is_published', true)
                    ->where('is_visible', true);
            })
            ->with('quizRange')
            ->first();

        $attempts = collect();

        $isAdmin = auth()->user()?->role === 'admin';

        if ($quizDay && ! $quizDay->quizRange?->leaderboard_is_public && ! $isAdmin) {
            // Leaderboard visibility is controlled per quiz range; non-admins are redirected when hidden.
            return redirect()
                ->route('home')
                ->with('status', 'Leaderboard is currently hidden.');
        }

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
