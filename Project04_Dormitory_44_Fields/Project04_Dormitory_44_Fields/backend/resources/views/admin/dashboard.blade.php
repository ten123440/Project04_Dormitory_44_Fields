@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">แผงควบคุมผู้ดูแลหอพัก (Admin Dashboard)</h1>
            <p class="text-xs text-slate-500 mt-1">ภาพรวมการจัดการห้องพัก สัญญาเช่า และสถานะห้องพักนักศึกษา</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="document.getElementById('addRoomModal').classList.remove('hidden')"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition flex items-center gap-1 shadow-sm">
                <span class="material-icons text-sm">add_home</span> เพิ่มห้องพักใหม่
            </button>
            <a href="{{ route('admin.room_types.index') }}"
                class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-xs font-semibold rounded-xl transition flex items-center gap-1 shadow-sm">
                <span class="material-icons text-sm">category</span> จัดการประเภทห้อง
            </a>
        </div>
    </div>

    <!-- Metrics Stat Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 block">ห้องทั้งหมด</span>
                <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $totalRooms }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <span class="material-icons">apartment</span>
            </div>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-emerald-600 block">ห้องว่างพร้อมจอง</span>
                <span class="text-2xl font-extrabold text-emerald-700 mt-1 block">{{ $availableRooms }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <span class="material-icons">meeting_room</span>
            </div>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-rose-600 block">มีผู้เช่าพักอยู่</span>
                <span class="text-2xl font-extrabold text-rose-700 mt-1 block">{{ $occupiedRooms }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <span class="material-icons">people</span>
            </div>
        </div>
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-amber-600 block">ปิดปรับปรุง/ซ่อมแซม</span>
                <span class="text-2xl font-extrabold text-amber-700 mt-1 block">{{ $maintenanceRooms }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <span class="material-icons">build</span>
            </div>
        </div>
    </div>

    <!-- Booking & Contracts Management Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div>
                <h2 class="text-lg font-bold text-slate-900">จัดการคำขอจองและสัญญาเช่าห้องพัก</h2>
                <p class="text-xs text-slate-500">ตรวจสอบและอัปเดตสถานะสัญญาของนักศึกษา</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="p-3">รหัสสัญญา</th>
                        <th class="p-3">ห้องพัก</th>
                        <th class="p-3">ผู้เช่า (นักศึกษา)</th>
                        <th class="p-3">เบอร์ติดต่อ</th>
                        <th class="p-3">เริ่มเข้าพัก</th>
                        <th class="p-3">สถานะสัญญา</th>
                        <th class="p-3 text-center">ปรับสถานะสัญญา</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentBookings as $b)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 font-mono font-bold text-indigo-700">{{ $b->tracking_code }}</td>
                            <td class="p-3 font-bold text-slate-900">ห้อง {{ $b->room->room_number }}</td>
                            <td class="p-3">{{ $b->user->name }}</td>
                            <td class="p-3">{{ $b->user->phone }}</td>
                            <td class="p-3">{{ $b->check_in_date }}<small class="block">ถึง {{ $b->check_out_date ?? 'ไม่ระบุ' }}<br>ค่าเช่า {{ $b->monthly_rate ?? 'ไม่ระบุ' }} • ประกัน {{ $b->deposit_amount ?? 'ไม่ระบุ' }}<br>{{ $b->notes }}</small></td>
                            <td class="p-3">
                                @if($b->status === 'active')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">เข้าพักอยู่ (Active)</span>
                                @elseif($b->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">รออนุมัติ (Pending)</span>
                                @elseif($b->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">สิ้นสุดสัญญา (Completed)</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">ยกเลิก (Cancelled)</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <form action="{{ route('admin.bookings.status', $b->id) }}" method="POST" class="inline-flex items-center gap-1">
                                    @csrf
                                    <select name="status" class="text-xs border border-slate-300 rounded-lg px-2 py-1 bg-white focus:ring-1 focus:ring-indigo-500">
                                        <option value="pending" {{ $b->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="active" {{ $b->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="completed" {{ $b->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $b->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    <button type="submit" class="px-2 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold transition">
                                        บันทึก
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-slate-400">ยังไม่มีรายการสัญญาในระบบ</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rooms List Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div>
                <h2 class="text-lg font-bold text-slate-900">รายการห้องพักทั้งหมด</h2>
                <p class="text-xs text-slate-500">ตรวจสอบและแก้ไขสถานะห้องพัก</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="p-3">หมายเลขห้อง</th>
                        <th class="p-3">ประเภทห้อง</th>
                        <th class="p-3">ชั้น</th>
                        <th class="p-3">ค่าเช่ารายเดือน</th>
                        <th class="p-3">สถานะห้อง</th>
                        <th class="p-3">รายละเอียด</th>
                        <th class="p-3 text-center">แก้ไขสถานะห้อง</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($rooms as $r)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-3 font-bold text-slate-900">ห้อง {{ $r->room_number }}</td>
                            <td class="p-3 font-semibold text-indigo-700">{{ $r->roomType->name }}</td>
                            <td class="p-3">ชั้น {{ $r->floor }}</td>
                            <td class="p-3 font-bold text-emerald-600">฿{{ number_format($r->roomType->monthly_rate, 2) }}</td>
                            <td class="p-3">
                                @if($r->status === 'available')
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">ว่าง</span>
                                @elseif($r->status === 'occupied')
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">มีผู้เช่า</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">ซ่อมบำรุง</span>
                                @endif
                            </td>
                            <td class="p-3 text-slate-500 max-w-xs truncate">{{ $r->building?->name ?? 'ยังไม่ระบุอาคาร' }} • {{ $r->description ?? '-' }}</td>
                            <td class="p-3 text-center">
                                <form action="{{ route('admin.rooms.status', $r->id) }}" method="POST" class="inline-flex items-center gap-1">
                                    @csrf
                                    <select name="status" class="text-xs border border-slate-300 rounded-lg px-2 py-1 bg-white focus:ring-1 focus:ring-indigo-500">
                                        <option value="available" {{ $r->status === 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="occupied" {{ $r->status === 'occupied' ? 'selected' : '' }}>Occupied</option>
                                        <option value="maintenance" {{ $r->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    </select>
                                    <button type="submit" class="px-2 py-1 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold transition">
                                        อัปเดต
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Room Modal -->
<div id="addRoomModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200">
            <h3 class="font-bold text-slate-900 text-base flex items-center gap-1">
                <span class="material-icons text-indigo-600 text-lg">add_home</span> เพิ่มข้อมูลห้องพักใหม่
            </h3>
            <button type="button" onclick="document.getElementById('addRoomModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-icons text-xl">close</span>
            </button>
        </div>

        <form action="{{ route('admin.rooms.store') }}" method="POST" class="mt-4 space-y-3">
<label class="block text-xs">อาคาร
<select name="building_id" class="w-full border rounded-xl p-2"><option value="">ยังไม่ระบุอาคาร</option>
@foreach($buildings as $building)<option value="{{ $building->id }}">{{ $building->building_code }} — {{ $building->name }}</option>@endforeach
</select></label>

            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">หมายเลขห้องพัก (เช่น 103, 203)</label>
                <input type="text" name="room_number" required placeholder="เลขห้อง"
                    class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">ประเภทห้องพัก</label>
                <select name="room_type_id" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} (฿{{ number_format($type->monthly_rate, 2) }}/เดือน)</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">ชั้น</label>
                    <input type="number" name="floor" required min="1" max="20" value="1"
                        class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">สถานะเริ่มต้น</label>
                    <select name="status" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="available">ว่าง (Available)</option>
                        <option value="occupied">มีผู้พัก (Occupied)</option>
                        <option value="maintenance">ปิดปรับปรุง (Maintenance)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">รายละเอียดและสิ่งอำนวยความสะดวก</label>
                <textarea name="description" rows="3" placeholder="ระบุรายละเอียด เช่น เตียงคู่ เครื่องทำน้ำอุ่น ระเบียง..."
                    class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="document.getElementById('addRoomModal').classList.add('hidden')"
                    class="px-4 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl">ยกเลิก</button>
                <button type="submit" class="px-5 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md transition">บันทึกข้อมูลห้อง</button>
            </div>
        </form>
    </div>
</div>
<section class="bg-white border rounded-2xl p-6 mt-6">
<h2 class="font-bold mb-4">ข้อมูลอาคาร</h2>
<div class="grid md:grid-cols-2 gap-6"><div>
@forelse($buildings as $building)
<article class="border rounded-xl p-3 mb-3"><b>{{ $building->building_code }} — {{ $building->name }}</b>
<p>{{ $building->total_floors }} ชั้น • {{ $building->contact_phone ?? 'ไม่ระบุโทรศัพท์' }}</p><p>{{ $building->address }}</p><p>{{ $building->description }}</p></article>
@empty<p>ยังไม่มีอาคาร สามารถเพิ่มก่อนสร้างห้องใหม่</p>@endforelse</div>
<form action="{{ route('admin.buildings.store') }}" method="POST">@csrf
<label class="block text-xs font-semibold text-slate-700">รหัสอาคาร<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="text" name="building_code" value="{{ old('building_code') }}" required maxlength="20"></label>
<label class="block text-xs font-semibold text-slate-700">ชื่ออาคาร<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="text" name="name" value="{{ old('name') }}" required maxlength="100"></label>
<label class="block text-xs font-semibold text-slate-700">จำนวนชั้น<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="number" name="total_floors" value="{{ old('total_floors') }}" required min="1" max="65535"></label>
<label class="block text-xs font-semibold text-slate-700">ที่อยู่<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="text" name="address" value="{{ old('address') }}" maxlength="2000"></label>
<label class="block text-xs font-semibold text-slate-700">โทรศัพท์ติดต่อ<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="text" name="contact_phone" value="{{ old('contact_phone') }}" maxlength="20"></label>
<label class="block text-xs font-semibold text-slate-700">รายละเอียด<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="text" name="description" value="{{ old('description') }}" maxlength="5000"></label>
<button class="bg-indigo-600 text-white rounded-xl px-4 py-2">เพิ่มอาคาร</button></form></div></section>
@endsection
