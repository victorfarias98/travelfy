<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TravelRequestController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/me', [AuthController::class, 'me']);
Route::post('/refresh-token', [AuthController::class, 'refresh']);
Route::post('/logout', [AuthController::class, 'logout']);


Route::middleware(['auth'])->group(function () {
    Route::prefix('travel-requests')->name('travel-requests.')->group(function () {
        Route::get('/', [TravelRequestController::class, 'index'])->name('index');
        Route::post('/', [TravelRequestController::class, 'store'])->name('store');
        Route::get('/my-requests', [TravelRequestController::class, 'myRequests'])->name('my-requests');
        Route::get('/{id}', [TravelRequestController::class, 'show'])->name('show');

        Route::middleware(['role'])->group(function () {
            Route::patch('/{id}/status', [TravelRequestController::class, 'updateStatus'])->name('update-status');
            Route::patch('/{id}/cancel-approved', [TravelRequestController::class, 'cancelApproved'])->name('cancel-approved');
        });
    });
});