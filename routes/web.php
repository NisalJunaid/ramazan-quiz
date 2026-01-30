<?php

use App\Http\Controllers\AdminChoiceController;
use App\Http\Controllers\AdminQuestionController;
use App\Http\Controllers\AdminQuizController;
use App\Http\Controllers\AttemptController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QuizController::class, 'home'])->name('home');
Route::get('/leaderboard', [LeaderboardController::class, 'todayLeaderboard'])->name('leaderboard');

Route::middleware(['auth', 'not_banned'])->group(function () {
    Route::get('/quiz/today', [QuizController::class, 'showTodayQuiz'])->name('quiz.today');
    Route::post('/quiz/{quizDay}/start', [QuizController::class, 'startAttempt'])->name('quiz.start');
    Route::post('/attempt/{attempt}/submit', [AttemptController::class, 'submitAttempt'])->name('attempt.submit');
});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->group(function () {
        Route::view('/', 'admin.index')->name('admin.dashboard');
        Route::get('/quizzes', [AdminQuizController::class, 'index'])->name('admin.quizzes.index');
        Route::post('/quizzes', [AdminQuizController::class, 'store'])->name('admin.quizzes.store');
        Route::put('/quizzes/{id}', [AdminQuizController::class, 'update'])->name('admin.quizzes.update');
        Route::post('/questions', [AdminQuestionController::class, 'store'])->name('admin.questions.store');
        Route::post('/choices', [AdminChoiceController::class, 'store'])->name('admin.choices.store');
    });

require __DIR__.'/auth.php';
