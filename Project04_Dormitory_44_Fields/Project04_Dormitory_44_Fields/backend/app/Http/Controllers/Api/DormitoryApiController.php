<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DormitoryApiController extends Controller
{
    /**
     * GET /api/room-types
     */
    public function getRoomTypes(): JsonResponse
    {
        $roomTypes = RoomType::withCount('rooms')->get();

        return response()->json([
            'success' => true,
            'count' => $roomTypes->count(),
            'data' => $roomTypes,
        ], 200);
    }

    /**
     * GET /api/rooms
     */
    public function getRooms(Request $request): JsonResponse
    {
        $query = Room::with(['roomType', 'building:id,building_code,name']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('floor')) {
            $query->where('floor', $request->floor);
        }
        if ($request->filled('room_type_id')) {
            $query->where('room_type_id', $request->room_type_id);
        }

        $rooms = $query->orderBy('room_number', 'asc')->get();

        return response()->json([
            'success' => true,
            'count' => $rooms->count(),
            'data' => $rooms,
        ], 200);
    }

    /**
     * GET /api/rooms/{id}
     */
    public function getRoom(int $id): JsonResponse
    {
        $room = Room::with(['roomType', 'building:id,building_code,name'])->find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลห้องพัก',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $room,
        ], 200);
    }

    /**
     * GET /api/bookings/{tracking_code}
     */
    public function getBookingByCode(string $code): JsonResponse
    {
        $booking = Booking::with([
            'room.roomType',
            'user:id,name,phone,email',
        ])->where('tracking_code', $code)->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลสัญญาเช่าหรือการจองด้วยรหัสนี้',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $booking->makeHidden(['notes']),
        ], 200);
    }

    /**
     * GET /api/stats
     */
    public function getStats(): JsonResponse
    {
        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $maintenanceRooms = Room::where('status', 'maintenance')->count();

        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $activeBookings = Booking::where('status', 'active')->count();
        $completedBookings = Booking::where('status', 'completed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_rooms' => $totalRooms,
                'available_rooms' => $availableRooms,
                'occupied_rooms' => $occupiedRooms,
                'maintenance_rooms' => $maintenanceRooms,
                'total_bookings' => $totalBookings,
                'pending_bookings' => $pendingBookings,
                'active_bookings' => $activeBookings,
                'completed_bookings' => $completedBookings,
                'cancelled_bookings' => $cancelledBookings,
                'total_room_types' => RoomType::count(),
            ],
        ], 200);
    }

    /**
     * GET /api/my-bookings
     */
    public function getMyBookings(Request $request): JsonResponse
    {
        $user = $request->user();
        $bookings = Booking::with('room.roomType')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $bookings->count(),
            'data' => $bookings,
        ], 200);
    }

    /**
     * POST /api/bookings
     */
    public function createBooking(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'nullable|date|after_or_equal:check_in_date',
            'notes' => 'nullable|string|max:2000',
        ]);

        $room = Room::find($validated['room_id']);
        if ($room->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'ห้องพักนี้ไม่ว่างในขณะนี้ กรุณาเลือกห้องอื่น',
            ], 422);
        }

        // Generate tracking code: DORM-YYYYxxx
        $trackingCode = 'DORM-' . date('Y') . sprintf('%03d', rand(100, 999));
        while (Booking::where('tracking_code', $trackingCode)->exists()) {
            $trackingCode = 'DORM-' . date('Y') . sprintf('%03d', rand(100, 999));
        }

        $booking = Booking::create([
            'tracking_code' => $trackingCode,
            'user_id' => $user->id,
            'room_id' => $room->id,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'monthly_rate' => $room->roomType->monthly_rate,
            'deposit_amount' => $room->roomType->deposit_amount,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ยื่นจองห้องพักสำเร็จ รหัสสัญญาของคุณคือ ' . $trackingCode,
            'data' => $booking->load('room.roomType'),
        ], 201);
    }

    /**
     * POST /api/room-types (Admin Only)
     */
    public function storeRoomType(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'monthly_rate' => 'required|numeric|min:0|max:99999999.99',
            'deposit_amount' => 'nullable|numeric|min:0|max:99999999.99',
            'capacity' => 'nullable|integer|min:1|max:65535',
            'description' => 'nullable|string|max:5000',
        ]);

        $roomType = RoomType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'เพิ่มประเภทห้องพักสำเร็จ',
            'data' => $roomType,
        ], 201);
    }

    /**
     * POST /api/rooms (Admin Only)
     */
    public function storeRoom(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number|max:50',
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string|max:255',
            'building_id' => 'nullable|integer|exists:buildings,id',
        ]);

        $room = Room::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'เพิ่มข้อมูลห้องพักสำเร็จ',
            'data' => $room->load('roomType'),
        ], 201);
    }

    /**
     * PUT /api/rooms/{id} (Admin Only)
     */
    public function updateRoom(Request $request, int $id): JsonResponse
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'ไม่พบห้องพัก'], 404);
        }

        $validated = $request->validate([
            'room_type_id' => 'sometimes|required|exists:room_types,id',
            'floor' => 'sometimes|required|integer|min:1',
            'status' => 'sometimes|required|in:available,occupied,maintenance',
            'description' => 'nullable|string|max:255',
            'building_id' => 'nullable|integer|exists:buildings,id',
        ]);

        $room->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตข้อมูลห้องพักสำเร็จ',
            'data' => $room->load('roomType'),
        ], 200);
    }

    /**
     * POST /api/bookings/{id}/status (Admin Only)
     */
    public function updateBookingStatus(Request $request, int $id): JsonResponse
    {
        $booking = Booking::with('room')->find($id);
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลสัญญาเช่า'], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,active,completed,cancelled',
        ]);

        $booking->status = $validated['status'];
        $booking->save();

        // Update corresponding room status
        if ($booking->room) {
            if ($validated['status'] === 'active') {
                $booking->room->status = 'occupied';
                $booking->room->save();
            } elseif (in_array($validated['status'], ['completed', 'cancelled'])) {
                // If no other active booking on this room, release it
                $activeCount = Booking::where('room_id', $booking->room_id)
                    ->where('status', 'active')
                    ->count();
                if ($activeCount === 0) {
                    $booking->room->status = 'available';
                    $booking->room->save();
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตสถานะสัญญาเรียบร้อยแล้ว',
            'data' => $booking->load(['room.roomType', 'user:id,name,phone,email']),
        ], 200);
    }
}
