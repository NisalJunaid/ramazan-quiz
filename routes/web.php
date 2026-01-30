<?php

use App\Http\Controllers\AdminChoiceController;
use App\Http\Controllers\AdminQuestionController;
use App\Http\Controllers\AdminQuizController;
use App\Http\Controllers\AttemptController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'home')->name('home');
Route::get('/leaderboard', [LeaderboardController::class, 'todayLeaderboard'])->name('leaderboard');

Route::middleware(['auth', 'not_banned'])->group(function () {
    Route::get('/quiz/today', [QuizController::class, 'showTodayQuiz'])->name('quiz.today');
    Route::post('/quiz/{quizDay}/start', [QuizController::class, 'startAttempt'])->name('quiz.start');
    Route::post('/attempt/{attempt}/submit', [AttemptController::class, 'submitAttempt'])->name('attempt.submit');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::view('/admin', 'admin.index')->name('admin.dashboard');
    Route::get('/admin/quizzes', [AdminQuizController::class, 'index'])->name('admin.quizzes.index');
    Route::post('/admin/quizzes', [AdminQuizController::class, 'store'])->name('admin.quizzes.store');
    Route::put('/admin/quizzes/{id}', [AdminQuizController::class, 'update'])->name('admin.quizzes.update');
    Route::post('/admin/questions', [AdminQuestionController::class, 'store'])->name('admin.questions.store');
    Route::post('/admin/choices', [AdminChoiceController::class, 'store'])->name('admin.choices.store');
});
