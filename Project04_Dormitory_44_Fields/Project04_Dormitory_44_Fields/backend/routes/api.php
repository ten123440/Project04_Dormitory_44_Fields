<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\DormitoryApiController;

/*
|--------------------------------------------------------------------------
| API Routes (Laravel Sanctum-Compatible Token Authenticated)
|--------------------------------------------------------------------------
*/

// Public Authentication Endpoints
Route::post('/register', [AuthApiController::class, 'register']);
Route::post('/login', [AuthApiController::class, 'login']);

// Public Room & Contract Information
Route::get('/room-types', [DormitoryApiController::class, 'getRoomTypes']);
Route::get('/rooms', [DormitoryApiController::class, 'getRooms']);
Route::get('/rooms/{id}', [DormitoryApiController::class, 'getRoom']);
Route::get('/bookings/{tracking_code}', [DormitoryApiController::class, 'getBookingByCode']);
Route::get('/stats', [DormitoryApiController::class, 'getStats']);

// Authenticated Endpoints (Bearer Token)
Route::middleware('auth.token')->group(function () {
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);

    // Tenant / User Operations
    Route::get('/my-bookings', [DormitoryApiController::class, 'getMyBookings']);
    Route::post('/bookings', [DormitoryApiController::class, 'createBooking']);

    // Admin Only Operations
    Route::middleware('role:admin')->group(function () {
        Route::post('/room-types', [DormitoryApiController::class, 'storeRoomType']);
        Route::post('/rooms', [DormitoryApiController::class, 'storeRoom']);
        Route::put('/rooms/{id}', [DormitoryApiController::class, 'updateRoom']);
        Route::post('/bookings/{id}/status', [DormitoryApiController::class, 'updateBookingStatus']);
    });
});
