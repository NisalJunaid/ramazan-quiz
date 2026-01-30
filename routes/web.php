<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AttemptController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminQuizController;
use App\Http\Controllers\Admin\AdminQuestionController;
use App\Http\Controllers\Admin\AdminChoiceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Ramazan Daily Quiz Portal
| Laravel Breeze authentication (NO dashboard)
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [QuizController::class, 'home'])->name('home');

Route::get('/leaderboard', [LeaderboardController::class, 'todayLeaderboard'])
    ->name('leaderboard');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'not_banned'])->group(function () {

    // View today's quiz
    Route::get('/quiz/today', [QuizController::class, 'showTodayQuiz'])
        ->name('quiz.today');

    // Start quiz attempt
    Route::post('/quiz/{quizDay}/start', [QuizController::class, 'startAttempt'])
        ->name('quiz.start');

    // Submit quiz attempt
    Route::post('/attempt/{attempt}/submit', [AttemptController::class, 'submitAttempt'])
        ->name('attempt.submit');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', function () {
            return view('admin.index');
        })->name('index');

        // Quiz management
        Route::get('/quizzes', [AdminQuizController::class, 'index'])
            ->name('quizzes.index');

        Route::post('/quizzes', [AdminQuizController::class, 'store'])
            ->name('quizzes.store');

        Route::put('/quizzes/{id}', [AdminQuizController::class, 'update'])
            ->name('quizzes.update');

        // Questions
        Route::post('/questions', [AdminQuestionController::class, 'store'])
            ->name('questions.store');

        // Choices
        Route::post('/choices', [AdminChoiceController::class, 'store'])
            ->name('choices.store');
    });

/*
|--------------------------------------------------------------------------
| Profile Routes (Laravel Breeze default)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
