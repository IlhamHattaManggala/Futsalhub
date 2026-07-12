<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TacticController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\CoachController;
use App\Http\Controllers\Api\FinanceController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\MatchController;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/login', [AuthController::class, 'login']);

    // Protected Auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Protected Operational Routes (requires tenant validation)
        Route::middleware('tenant')->prefix('{slug}')->group(function () {
            
            // Player-specific action routes
            Route::middleware('role:player')->group(function () {
                Route::post('/schedules/{id}/scan', [ScheduleController::class, 'scanAttendance']);
                Route::post('/schedules/{id}/receipt', [ScheduleController::class, 'uploadReceipt']);
                Route::post('/tasks/{id}/complete', [TaskController::class, 'complete']);
            });

            // Coach-specific routes
            Route::middleware('role:coach')->group(function () {
                Route::post('/schedules/{id}/attendance', [ScheduleController::class, 'saveAttendance']);
                Route::get('/tactical-board', [TacticController::class, 'index']);
                Route::post('/tactical-board/save', [TacticController::class, 'save']);
                Route::get('/matches/{id}/stats', [MatchController::class, 'stats']);
                Route::post('/matches/{id}/stats', [MatchController::class, 'saveStats']);
            });

            // Management-specific routes
            Route::middleware('role:management')->group(function () {
                Route::get('/players', [PlayerController::class, 'index']);
                Route::post('/players', [PlayerController::class, 'store']);
                Route::delete('/players/{id}', [PlayerController::class, 'destroy']);

                Route::get('/coaches', [CoachController::class, 'index']);
                Route::post('/coaches', [CoachController::class, 'store']);
                Route::delete('/coaches/{id}', [CoachController::class, 'destroy']);

                Route::post('/finances/qris', [FinanceController::class, 'updateQris']);
            });

            // Shared Finance routes (Management and Coach)
            Route::middleware('role:management,coach')->group(function () {
                Route::get('/finances', [FinanceController::class, 'index']);
                Route::post('/finances', [FinanceController::class, 'store']);
            });

            // Shared schedules / matching / task / announcement / player profile routes
            // (Note: controller methods have internal checks/role-based formatting if needed)
            Route::get('/schedules', [ScheduleController::class, 'index']);
            Route::post('/schedules', [ScheduleController::class, 'store']);
            Route::get('/schedules/{id}/attendance', [ScheduleController::class, 'attendance']);
            
            Route::get('/tasks', [TaskController::class, 'index']);
            Route::post('/tasks', [TaskController::class, 'store']);
            Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);

            Route::get('/announcements', [AnnouncementController::class, 'index']);
            Route::post('/announcements', [AnnouncementController::class, 'store']);

            Route::get('/players/{id}', [PlayerController::class, 'show']);

            Route::get('/matches', [MatchController::class, 'index']);
            Route::post('/matches', [MatchController::class, 'store']);
        });
    });
});
