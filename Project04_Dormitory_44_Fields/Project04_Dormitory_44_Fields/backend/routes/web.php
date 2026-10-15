<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\AdminDormitoryController;
use App\Http\Controllers\Web\UserDormitoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home & Public Tracking
Route::get('/', [UserDormitoryController::class, 'track'])->name('home');
Route::get('/track', [UserDormitoryController::class, 'track'])->name('track');

// Authentication Routes (Laravel Web Blade)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Student / Customer Protected Routes
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/rooms', [UserDormitoryController::class, 'rooms'])->name('user.rooms');
    Route::post('/book', [UserDormitoryController::class, 'storeBooking'])->name('user.book');
    Route::get('/my-bookings', [UserDormitoryController::class, 'myBookings'])->name('user.my_bookings');
});

// Admin Protected Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDormitoryController::class, 'dashboard'])->name('dashboard');
    Route::post('/buildings', [AdminDormitoryController::class, 'storeBuilding'])->name('buildings.store');
    Route::post('/rooms', [AdminDormitoryController::class, 'storeRoom'])->name('rooms.store');
    Route::post('/rooms/{id}/status', [AdminDormitoryController::class, 'updateRoomStatus'])->name('rooms.status');
    Route::get('/room-types', [AdminDormitoryController::class, 'roomTypesIndex'])->name('room_types.index');
    Route::post('/room-types', [AdminDormitoryController::class, 'storeRoomType'])->name('room_types.store');
    Route::post('/bookings/{id}/status', [AdminDormitoryController::class, 'updateBookingStatus'])->name('bookings.status');
});

// Flutter Web Redirect Helper
Route::get('/flutter', function () {
    return redirect('/flutter/index.html');
});

/*
|--------------------------------------------------------------------------
| Raw JSON API Explorer (Strict Requirement: 100% Pure JSON, NO HTML/CSS)
|--------------------------------------------------------------------------
*/
Route::get('/api-docs', function () {
    $docs = [
        'system' => 'ระบบบริหารจัดการหอพักนักศึกษา (Dormitory Management System)',
        'version' => '1.0.0',
        'framework' => 'Laravel 12.x',
        'database_name' => 'dormitory_db',
        'base_url' => url('/api'),
        'authentication' => [
            'type' => 'Bearer Token (Cache-backed Sanctum Middleware)',
            'header' => 'Authorization: Bearer <token>',
            'notes' => 'Token ได้รับจากการเรียก POST /api/login หรือ POST /api/register',
            'test_tokens' => [
                'admin' => 'admin-test-token-2026',
                'user' => 'user-test-token-2026',
            ],
        ],
        'database_schema' => json_decode(file_get_contents(database_path('schema_manifest.json')), true),
        'endpoints' => [
            [
                'group' => 'Authentication',
                'method' => 'POST',
                'uri' => '/api/register',
                'description' => 'สมัครสมาชิกผู้ใช้งานใหม่สำหรับนักศึกษา/ผู้เช่า (Default role: user)',
                'access' => 'Public',
                'request_body' => [
                    'name' => 'string (required, e.g. สมศรี ใจดี)',
                    'email' => 'string (required, unique, e.g. somsri@dormitory.com)',
                    'phone' => 'string (required, unique, e.g. 0891234567)',
                    'password' => 'string (required, min 6, e.g. user1234)',
                ],
                'response_example' => [
                    'success' => true,
                    'message' => 'สมัครสมาชิกสำเร็จ ยินดีต้อนรับเข้าสู่ระบบจัดการหอพักนักศึกษา',
                    'token' => '2|f89a7b6c5d...',
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => 2,
                        'name' => 'สมศรี ใจดี',
                        'email' => 'somsri@dormitory.com',
                        'phone' => '0891234567',
                        'role' => 'user',
                    ],
                ],
            ],
            [
                'group' => 'Authentication',
                'method' => 'POST',
                'uri' => '/api/login',
                'description' => 'เข้าสู่ระบบเพื่อขอรับ Bearer Token ยืนยันตัวตน',
                'access' => 'Public',
                'request_body' => [
                    'email' => 'string (required, e.g. admin@dormitory.com)',
                    'password' => 'string (required, e.g. admin1234)',
                ],
                'response_example' => [
                    'success' => true,
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'token' => '1|9b8c7d6e5a...',
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => 1,
                        'name' => 'ผู้ดูแลหอพัก (Admin Manager)',
                        'email' => 'admin@dormitory.com',
                        'phone' => '0812345678',
                        'role' => 'admin',
                    ],
                ],
            ],
            [
                'group' => 'Authentication',
                'method' => 'POST',
                'uri' => '/api/logout',
                'description' => 'ออกจากระบบและยกเลิก Bearer Token ในระบบแคช',
                'access' => 'Authenticated (Bearer Token)',
                'request_body' => [],
                'response_example' => [
                    'success' => true,
                    'message' => 'ออกจากระบบเรียบร้อยแล้ว (Logged out successfully)',
                ],
            ],
            [
                'group' => 'Authentication',
                'method' => 'GET',
                'uri' => '/api/me',
                'description' => 'ดึงข้อมูลโปรไฟล์ผู้ใช้งานปัจจุบัน',
                'access' => 'Authenticated (Bearer Token)',
                'response_example' => [
                    'success' => true,
                    'user' => [
                        'id' => 1,
                        'name' => 'ผู้ดูแลหอพัก (Admin Manager)',
                        'email' => 'admin@dormitory.com',
                        'phone' => '0812345678',
                        'role' => 'admin',
                    ],
                ],
            ],
            [
                'group' => 'Room Types',
                'method' => 'GET',
                'uri' => '/api/room-types',
                'description' => 'รายการประเภทห้องพักพร้อมอัตราค่าเช่ารายเดือนและจำนวนห้องพัก',
                'access' => 'Public',
                'response_example' => [
                    'success' => true,
                    'count' => 4,
                    'data' => [
                        [
                            'id' => 1,
                            'name' => 'ห้องพัดลม (Standard Fan)',
                            'monthly_rate' => '2500.00',
                            'rooms_count' => 2,
                            'created_at' => '2026-09-01T00:00:00.000000Z',
                        ],
                    ],
                ],
            ],
            [
                'group' => 'Room Types',
                'method' => 'POST',
                'uri' => '/api/room-types',
                'description' => 'เพิ่มประเภทห้องพักใหม่ (สำหรับผู้ดูแลหอพัก)',
                'access' => 'Admin Only (Bearer Token)',
                'request_body' => [
                    'name' => 'string (required, e.g. ห้องสวีทหรูพร้อมครัว)',
                    'monthly_rate' => 'number (required, e.g. 6000.00)',
                ],
                'response_example' => [
                    'success' => true,
                    'message' => 'เพิ่มประเภทห้องพักสำเร็จ',
                    'data' => [
                        'id' => 5,
                        'name' => 'ห้องสวีทหรูพร้อมครัว',
                        'monthly_rate' => '6000.00',
                    ],
                ],
            ],
            [
                'group' => 'Rooms',
                'method' => 'GET',
                'uri' => '/api/rooms',
                'description' => 'รายการห้องพักทั้งหมด พร้อมฟิลเตอร์ค้นหาตามสถานะ ชั้น หรือประเภทห้องพัก',
                'access' => 'Public',
                'query_params' => [
                    'status' => 'optional enum: available, occupied, maintenance',
                    'floor' => 'optional integer: 1, 2, 3...',
                    'room_type_id' => 'optional integer: references room_types.id',
                ],
                'response_example' => [
                    'success' => true,
                    'count' => 7,
                    'data' => [
                        [
                            'id' => 1,
                            'room_number' => '101',
                            'room_type_id' => 1,
                            'floor' => 1,
                            'status' => 'available',
                            'description' => 'ห้องพัดลม ชั้น 1 ระเบียงกว้าง ใกล้ทางเข้าอาคาร',
                            'created_at' => '2026-09-01T00:00:00.000000Z',
                            'room_type' => [
                                'id' => 1,
                                'name' => 'ห้องพัดลม (Standard Fan)',
                                'monthly_rate' => '2500.00',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'group' => 'Rooms',
                'method' => 'POST',
                'uri' => '/api/rooms',
                'description' => 'เพิ่มห้องพักใหม่เข้าสู่ระบบ (สำหรับผู้ดูแลหอพัก)',
                'access' => 'Admin Only (Bearer Token)',
                'request_body' => [
                    'room_number' => 'string (required, unique, e.g. 402)',
                    'room_type_id' => 'integer (required, references room_types.id)',
                    'floor' => 'integer (required, e.g. 4)',
                    'status' => 'enum (required: available, occupied, maintenance)',
                    'description' => 'string (optional)',
                ],
                'response_example' => [
                    'success' => true,
                    'message' => 'เพิ่มข้อมูลห้องพักสำเร็จ',
                    'data' => [
                        'id' => 8,
                        'room_number' => '402',
                        'room_type_id' => 4,
                        'floor' => 4,
                        'status' => 'available',
                    ],
                ],
            ],
            [
                'group' => 'Bookings',
                'method' => 'GET',
                'uri' => '/api/bookings/{tracking_code}',
                'description' => 'ติดตามข้อมูลสัญญาเช่าและการจองห้องพักแบบเรียลไทม์ผ่านรหัสสัญญา',
                'access' => 'Public',
                'response_example' => [
                    'success' => true,
                    'data' => [
                        'id' => 1,
                        'tracking_code' => 'DORM-2026001',
                        'user_id' => 2,
                        'room_id' => 2,
                        'check_in_date' => '2026-08-01',
                        'status' => 'active',
                        'room' => [
                            'id' => 2,
                            'room_number' => '102',
                            'floor' => 1,
                            'status' => 'occupied',
                            'room_type' => [
                                'id' => 1,
                                'name' => 'ห้องพัดลม (Standard Fan)',
                                'monthly_rate' => '2500.00',
                            ],
                        ],
                        'user' => [
                            'id' => 2,
                            'name' => 'สมชาย นักศึกษา',
                            'email' => 'user@dormitory.com',
                            'phone' => '0898765432',
                        ],
                    ],
                ],
            ],
            [
                'group' => 'Bookings',
                'method' => 'POST',
                'uri' => '/api/bookings',
                'description' => 'ยื่นจองห้องพักและขอทำสัญญาเช่าใหม่ (สำหรับนักศึกษา/ผู้ใช้งาน)',
                'access' => 'Authenticated (Bearer Token)',
                'request_body' => [
                    'room_id' => 'integer (required, references rooms.id)',
                    'check_in_date' => 'date (required, e.g. 2026-10-01)',
                ],
                'response_example' => [
                    'success' => true,
                    'message' => 'ยื่นจองห้องพักสำเร็จ รหัสสัญญาของคุณคือ DORM-2026004',
                    'data' => [
                        'id' => 4,
                        'tracking_code' => 'DORM-2026004',
                        'user_id' => 2,
                        'room_id' => 1,
                        'check_in_date' => '2026-10-01',
                        'status' => 'pending',
                    ],
                ],
            ],
            [
                'group' => 'Bookings',
                'method' => 'GET',
                'uri' => '/api/my-bookings',
                'description' => 'รายการสัญญาเช่าและประวัติการจองของผู้ใช้งานปัจจุบัน',
                'access' => 'Authenticated (Bearer Token)',
                'response_example' => [
                    'success' => true,
                    'count' => 1,
                    'data' => [
                        [
                            'id' => 1,
                            'tracking_code' => 'DORM-2026001',
                            'status' => 'active',
                            'check_in_date' => '2026-08-01',
                            'room' => [
                                'room_number' => '102',
                                'floor' => 1,
                                'room_type' => [
                                    'name' => 'ห้องพัดลม (Standard Fan)',
                                    'monthly_rate' => '2500.00',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'group' => 'Bookings',
                'method' => 'POST',
                'uri' => '/api/bookings/{id}/status',
                'description' => 'ผู้ดูแลหอพักอัปเดตสถานะสัญญาเช่า (pending -> active -> completed -> cancelled)',
                'access' => 'Admin Only (Bearer Token)',
                'request_body' => [
                    'status' => 'enum (required: pending, active, completed, cancelled)',
                ],
                'response_example' => [
                    'success' => true,
                    'message' => 'อัปเดตสถานะสัญญาเรียบร้อยแล้ว',
                    'data' => [
                        'id' => 3,
                        'tracking_code' => 'DORM-2026003',
                        'status' => 'active',
                    ],
                ],
            ],
            [
                'group' => 'Statistics',
                'method' => 'GET',
                'uri' => '/api/stats',
                'description' => 'สรุปสถิติภาพรวมหอพักนักศึกษา (จำนวนห้องว่าง ห้องพักอยู่ สถานะสัญญา)',
                'access' => 'Public',
                'response_example' => [
                    'success' => true,
                    'data' => [
                        'total_rooms' => 7,
                        'available_rooms' => 4,
                        'occupied_rooms' => 2,
                        'maintenance_rooms' => 1,
                        'total_bookings' => 3,
                        'pending_bookings' => 1,
                        'active_bookings' => 2,
                        'completed_bookings' => 0,
                        'cancelled_bookings' => 0,
                        'total_room_types' => 4,
                    ],
                ],
            ],
        ],
    ];

    return response()->json($docs, 200, [
        'Content-Type' => 'application/json; charset=UTF-8'
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
});
