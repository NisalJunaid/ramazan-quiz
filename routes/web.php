<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\AttemptController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminQuizController;
use App\Http\Controllers\Admin\AdminQuestionController;
use App\Http\Controllers\Admin\AdminChoiceController;
use App\Http\Controllers\Admin\AppTextController;
use App\Http\Controllers\Admin\FontController;
use App\Http\Controllers\Admin\ThemeSettingsController;

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

        Route::get('/quizzes/{quizRange}/days', [AdminQuizController::class, 'showDays'])
            ->name('quizzes.days');

        Route::get('/quizzes/{quizRange}/days/{quizDay}/edit', [AdminQuizController::class, 'editDay'])
            ->name('quizzes.days.edit');

        // Questions
        Route::post('/questions', [AdminQuestionController::class, 'store'])
            ->name('questions.store');

        Route::put('/questions/{quizDay}', [AdminQuestionController::class, 'update'])
            ->name('questions.update');

        Route::put('/quizzes/days/{quizDay}/question', [AdminQuestionController::class, 'save'])
            ->name('questions.save');

        // Choices
        Route::post('/choices', [AdminChoiceController::class, 'store'])
            ->name('choices.store');

        Route::put('/choices/{choice}', [AdminChoiceController::class, 'update'])
            ->name('choices.update');

        // Text management
        Route::get('/texts', [AppTextController::class, 'index'])
            ->name('texts.index');

        Route::post('/texts', [AppTextController::class, 'store'])
            ->name('texts.store');

        Route::post('/texts/bulk-update', [AppTextController::class, 'bulkUpdate'])
            ->name('texts.bulkUpdate');

        Route::put('/texts/{text}', [AppTextController::class, 'update'])
            ->name('texts.update');

        Route::delete('/texts/{text}', [AppTextController::class, 'destroy'])
            ->name('texts.destroy');

        Route::put('/settings/rtl', [AppTextController::class, 'updateSettings'])
            ->name('settings.rtl');

        // Font management
        Route::get('/fonts', [FontController::class, 'index'])
            ->name('fonts.index');

        Route::post('/fonts', [FontController::class, 'store'])
            ->name('fonts.store');

        Route::delete('/fonts/{font}', [FontController::class, 'destroy'])
            ->name('fonts.destroy');

        // Theme settings
        Route::get('/theme', [ThemeSettingsController::class, 'index'])
            ->name('theme.index');

        Route::put('/theme', [ThemeSettingsController::class, 'update'])
            ->name('theme.update');
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
