@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">ค้นหาและจองห้องพักนักศึกษา</h1>
            <p class="text-xs text-slate-500 mt-1">เลือกห้องพักที่ต้องการแล้วกดยื่นจองห้องพักได้ทันที</p>
        </div>
        <div>
            <a href="{{ route('user.my_bookings') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-indigo-600 border border-indigo-200 text-xs font-semibold rounded-xl transition flex items-center gap-1 shadow-sm">
                <span class="material-icons text-sm">receipt_long</span> สัญญาเช่าของฉัน
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="{{ route('user.rooms') }}" class="p-4 bg-white rounded-2xl border border-slate-200 shadow-sm grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">ประเภทห้องพัก</label>
            <select name="room_type_id" class="w-full text-xs border border-slate-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">ทั้งหมด</option>
                @foreach($roomTypes as $type)
                    <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }} (฿{{ number_format($type->monthly_rate, 0) }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">ชั้น</label>
            <select name="floor" class="w-full text-xs border border-slate-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">ทุกชั้น</option>
                <option value="1" {{ request('floor') == '1' ? 'selected' : '' }}>ชั้น 1</option>
                <option value="2" {{ request('floor') == '2' ? 'selected' : '' }}>ชั้น 2</option>
                <option value="3" {{ request('floor') == '3' ? 'selected' : '' }}>ชั้น 3</option>
                <option value="4" {{ request('floor') == '4' ? 'selected' : '' }}>ชั้น 4</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1">สถานะห้องพัก</label>
            <select name="status" class="w-full text-xs border border-slate-300 rounded-lg p-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">ทั้งหมด</option>
                <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>ว่าง (Available)</option>
                <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>มีผู้พักแล้ว (Occupied)</option>
                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>ปิดปรับปรุง (Maintenance)</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-grow py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition shadow-sm flex items-center justify-center gap-1">
                <span class="material-icons text-sm">filter_alt</span> คัดกรอง
            </button>
            <a href="{{ route('user.rooms') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition">
                รีเซ็ต
            </a>
        </div>
    </form>

    <!-- Rooms Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($rooms as $room)
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="p-5 border-b border-slate-100 flex items-start justify-between">
                        <div>
                            <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">ชั้น {{ $room->floor }}</span>
                            <h3 class="text-lg font-bold text-slate-900 mt-0.5">ห้อง {{ $room->room_number }}</h3>
                        </div>
                        <div>
                            @if($room->status === 'available')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">ว่าง</span>
                            @elseif($room->status === 'occupied')
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">มีผู้เช่าแล้ว</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">ซ่อมบำรุง</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-5 space-y-3">
                        <div class="text-sm">
                            <span class="text-slate-500 block text-xs">ประเภทห้อง:</span>
                            <span class="font-semibold text-slate-800">{{ $room->roomType->name }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-slate-500 block text-xs">อัตราค่าเช่า:</span>
                            <span class="text-base font-bold text-indigo-600">฿{{ number_format($room->roomType->monthly_rate, 2) }} <span class="text-xs text-slate-400 font-normal">/ เดือน</span></span>
                        </div>
                        <div class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                            อาคาร {{ $room->building?->name ?? 'ยังไม่ระบุ' }} • รองรับ {{ $room->roomType->capacity ?? 'ไม่ระบุ' }} คน • เงินประกัน {{ $room->roomType->deposit_amount ?? 'ไม่ระบุ' }} บาท<br>{{ $room->description ?? 'ห้องพักมาตรฐาน ครบครันด้วยสุขอนามัยและความปลอดภัย' }}
                        </div>
                    </div>
                </div>

                <div class="p-5 pt-0">
                    @if($room->status === 'available')
                        <button type="button" onclick="openBookingModal('{{ $room->id }}', '{{ $room->room_number }}', '{{ $room->roomType->name }}', '{{ number_format($room->roomType->monthly_rate, 2) }}')"
                            class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-md shadow-indigo-100 flex items-center justify-center gap-1">
                            <span class="material-icons text-sm">bookmark_add</span> ยื่นจองห้องพักนี้
                        </button>
                    @else
                        <button disabled class="w-full py-2.5 bg-slate-100 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed">
                            ไม่สามารถจองได้
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center p-12 bg-white rounded-2xl border border-slate-200 text-slate-500 text-sm">
                ไม่พบห้องพักตามเงื่อนไขที่ค้นหา
            </div>
        @endforelse
    </div>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200">
            <h3 class="font-bold text-slate-900 text-base flex items-center gap-1">
                <span class="material-icons text-indigo-600 text-lg">edit_calendar</span> ยืนยันการจองห้องพัก
            </h3>
            <button type="button" onclick="closeBookingModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-icons text-xl">close</span>
            </button>
        </div>

        <form action="{{ route('user.book') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="room_id" id="modalRoomId">

            <div class="p-3 bg-indigo-50 rounded-xl text-xs space-y-1 text-indigo-900">
                <div><span class="font-semibold">ห้องพัก:</span> <span id="modalRoomNumber" class="font-bold"></span></div>
                <div><span class="font-semibold">ประเภท:</span> <span id="modalRoomType"></span></div>
                <div><span class="font-semibold">ค่าเช่า:</span> <span id="modalRoomRate" class="font-bold text-emerald-700"></span> บาท/เดือน</div>
            </div>

            <div>
                <label for="check_in_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">
                    วันที่ต้องการเริ่มเข้าพัก
                </label>
                <input type="date" name="check_in_date" id="check_in_date" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                    class="w-full px-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

<label class="block text-xs font-semibold text-slate-700">วันที่คาดว่าจะสิ้นสุดการพัก (ไม่บังคับ)<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="date" name="check_out_date" value="{{ old('check_out_date') }}" ></label>
<label class="block text-xs font-semibold text-slate-700">หมายเหตุการจอง<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="text" name="notes" value="{{ old('notes') }}" maxlength="2000"></label>
            <div class="text-[11px] text-slate-500">
                * เมื่อกดยืนยัน ระบบจะสร้างรหัสสัญญาติดตาม (Tracking Code) ให้คุณโดยอัตโนมัติ
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="closeBookingModal()" class="px-4 py-2 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl">ยกเลิก</button>
                <button type="submit" class="px-5 py-2 text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-md transition">ยืนยันการจอง</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBookingModal(roomId, roomNumber, roomType, rate) {
        document.getElementById('modalRoomId').value = roomId;
        document.getElementById('modalRoomNumber').innerText = 'ห้อง ' + roomNumber;
        document.getElementById('modalRoomType').innerText = roomType;
        document.getElementById('modalRoomRate').innerText = '฿' + rate;
        document.getElementById('bookingModal').classList.remove('hidden');
    }
    function closeBookingModal() {
        document.getElementById('bookingModal').classList.add('hidden');
    }
</script>
@endsection
