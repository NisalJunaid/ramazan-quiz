<?php

namespace App\Providers;

use App\Models\QuizDay;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $today = Carbon::today();

            $quizDay = QuizDay::query()
                ->whereDate('quiz_date', $today)
                ->whereHas('quizRange', function ($query) {
                    $query->where('is_published', true)
                        ->where('is_visible', true);
                })
                ->with('quizRange')
                ->first();

            $leaderboardIsPublic = $quizDay?->quizRange?->leaderboard_is_public ?? true;
            $isAdmin = auth()->user()?->role === 'admin';

            $view->with([
                'leaderboardIsPublic' => $leaderboardIsPublic,
                'canViewLeaderboard' => $leaderboardIsPublic || $isAdmin,
            ]);
        });
    }
}
