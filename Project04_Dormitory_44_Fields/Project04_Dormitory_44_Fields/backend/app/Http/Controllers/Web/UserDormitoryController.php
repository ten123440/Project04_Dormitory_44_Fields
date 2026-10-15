<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDormitoryController extends Controller
{
    public function track(Request $request)
    {
        $trackingCode = $request->input('code');
        $booking = null;

        if ($trackingCode) {
            $booking = Booking::with(['room.roomType', 'user'])
                ->where('tracking_code', trim($trackingCode))
                ->first();
        }

        $availableRooms = Room::with(['roomType', 'building:id,building_code,name'])
            ->where('status', 'available')
            ->orderBy('floor', 'asc')
            ->take(6)
            ->get();

        return view('user.track', compact('booking', 'trackingCode', 'availableRooms'));
    }

    public function rooms(Request $request)
    {
        $query = Room::with(['roomType', 'building:id,building_code,name']);

        if ($request->filled('room_type_id')) {
            $query->where('room_type_id', $request->room_type_id);
        }
        if ($request->filled('floor')) {
            $query->where('floor', $request->floor);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $rooms = $query->orderBy('room_number', 'asc')->get();
        $roomTypes = RoomType::all();

        return view('user.rooms', compact('rooms', 'roomTypes'));
    }

    public function myBookings()
    {
        $user = Auth::user();
        $bookings = Booking::with('room.roomType')
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('user.my_bookings', compact('bookings'));
    }

    public function storeBooking(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'nullable|date|after_or_equal:check_in_date',
            'notes' => 'nullable|string|max:2000',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        if ($room->status !== 'available') {
            return back()->with('error', 'ห้องพักนี้ไม่ว่างในขณะนี้ กรุณาเลือกห้องพักอื่น');
        }

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

        return redirect()->route('user.my_bookings')->with('success', 'ยื่นจองห้องพัก ' . $room->room_number . ' สำเร็จ รหัสสัญญาของคุณคือ: ' . $trackingCode);
    }
}
