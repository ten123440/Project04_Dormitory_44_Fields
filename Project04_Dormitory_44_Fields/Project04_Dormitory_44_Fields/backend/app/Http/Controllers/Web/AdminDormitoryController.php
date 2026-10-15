<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Building;
use App\Models\RoomType;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminDormitoryController extends Controller
{
    public function dashboard()
    {
        $totalRooms = Room::count();
        $availableRooms = Room::where('status', 'available')->count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $maintenanceRooms = Room::where('status', 'maintenance')->count();

        $recentBookings = Booking::with(['room.roomType', 'user'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        $rooms = Room::with(['roomType', 'building:id,building_code,name'])->orderBy('room_number', 'asc')->get();
        $roomTypes = RoomType::all();
        $buildings = Building::orderBy('building_code')->get();

        return view('admin.dashboard', compact(
            'totalRooms',
            'availableRooms',
            'occupiedRooms',
            'maintenanceRooms',
            'recentBookings',
            'rooms',
            'roomTypes',
            'buildings'
        ));
    }

    public function storeBuilding(Request $request)
    {
        $validated = $request->validate([
            'building_code' => 'required|string|max:20|unique:buildings,building_code',
            'name' => 'required|string|max:100',
            'address' => 'nullable|string|max:2000',
            'total_floors' => 'required|integer|min:1|max:65535',
            'contact_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:5000',
        ]);
        Building::create($validated);
        return back()->with('success', 'บันทึกอาคารแล้ว');
    }

    public function storeRoom(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number|max:50',
            'room_type_id' => 'required|exists:room_types,id',
            'floor' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string|max:255',
            'building_id' => 'nullable|integer|exists:buildings,id',
        ]);

        Room::create($validated);

        return redirect()->route('admin.dashboard')->with('success', 'เพิ่มห้องพักหมายเลข ' . $validated['room_number'] . ' สำเร็จ');
    }

    public function updateRoomStatus(Request $request, int $id)
    {
        $room = Room::findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        $room->status = $validated['status'];
        $room->save();

        return redirect()->route('admin.dashboard')->with('success', 'อัปเดตสถานะห้อง ' . $room->room_number . ' เป็น ' . $room->status . ' สำเร็จ');
    }

    public function roomTypesIndex()
    {
        $roomTypes = RoomType::withCount('rooms')->get();
        return view('admin.room_types.index', compact('roomTypes'));
    }

    public function storeRoomType(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'monthly_rate' => 'required|numeric|min:0|max:99999999.99',
            'deposit_amount' => 'nullable|numeric|min:0|max:99999999.99',
            'capacity' => 'nullable|integer|min:1|max:65535',
            'description' => 'nullable|string|max:5000',
        ]);

        RoomType::create($validated);

        return redirect()->route('admin.room_types.index')->with('success', 'เพิ่มประเภทห้องพักสำเร็จ');
    }

    public function updateBookingStatus(Request $request, int $id)
    {
        $booking = Booking::with('room')->findOrFail($id);
        $validated = $request->validate([
            'status' => 'required|in:pending,active,completed,cancelled',
        ]);

        $booking->status = $validated['status'];
        $booking->save();

        if ($booking->room) {
            if ($validated['status'] === 'active') {
                $booking->room->status = 'occupied';
                $booking->room->save();
            } elseif (in_array($validated['status'], ['completed', 'cancelled'])) {
                $activeCount = Booking::where('room_id', $booking->room_id)
                    ->where('status', 'active')
                    ->count();
                if ($activeCount === 0) {
                    $booking->room->status = 'available';
                    $booking->room->save();
                }
            }
        }

        return redirect()->route('admin.dashboard')->with('success', 'อัปเดตสถานะสัญญา ' . $booking->tracking_code . ' สำเร็จ');
    }
}
