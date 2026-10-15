@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Hero / Search Section -->
    <div class="bg-gradient-to-br from-indigo-900 via-indigo-800 to-slate-900 text-white p-8 md:p-12 rounded-3xl shadow-xl relative overflow-hidden">
        <div class="max-w-3xl relative z-10">
            <span class="px-3 py-1 bg-indigo-500/30 border border-indigo-400/40 rounded-full text-xs font-semibold text-indigo-200 uppercase tracking-wide inline-block mb-3">
                Online Contract & Room Tracking
            </span>
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">ระบบติดตามสัญญาเช่าและห้องพักนักศึกษา</h1>
            <p class="text-indigo-200 mt-2 text-sm md:text-base leading-relaxed">
                ตรวจสอบสถานะการจองห้องพัก สัญญาเข้าพัก และรายละเอียดห้องพักได้อย่างรวดเร็วและแม่นยำตลอด 24 ชั่วโมง
            </p>

            <!-- Search Form -->
            <form action="{{ route('track') }}" method="GET" class="mt-6 flex flex-col sm:flex-row gap-2 max-w-xl">
                <div class="relative flex-grow">
                    <span class="material-icons absolute left-3 top-3 text-slate-400">search</span>
                    <input type="text" name="code" value="{{ $trackingCode ?? '' }}" placeholder="กรอกรหัสสัญญา เช่น DORM-2026001"
                        class="w-full pl-10 pr-4 py-3 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 shadow-md">
                </div>
                <button type="submit" class="px-6 py-3 bg-indigo-500 hover:bg-indigo-600 font-semibold text-sm rounded-xl transition shadow-md flex items-center justify-center gap-1">
                    <span class="material-icons text-sm">manage_search</span> ค้นหา
                </button>
            </form>

            <div class="mt-3 flex items-center gap-2 text-xs text-indigo-300">
                <span>รหัสตัวอย่างสำหรับทดสอบ:</span>
                <a href="{{ route('track', ['code' => 'DORM-2026001']) }}" class="underline hover:text-white font-mono">DORM-2026001</a>,
                <a href="{{ route('track', ['code' => 'DORM-2026002']) }}" class="underline hover:text-white font-mono">DORM-2026002</a>,
                <a href="{{ route('track', ['code' => 'DORM-2026003']) }}" class="underline hover:text-white font-mono">DORM-2026003</a>
            </div>
        </div>
    </div>

    <!-- Search Result Section -->
    @if($trackingCode)
        @if($booking)
            <div class="bg-white p-6 md:p-8 rounded-2xl shadow-lg border border-slate-200">
                <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-slate-200 gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="material-icons text-indigo-600">verified</span>
                            <h2 class="text-xl font-bold text-slate-900">รายละเอียดสัญญา: {{ $booking->tracking_code }}</h2>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">วันที่เริ่มเข้าพัก: {{ $booking->check_in_date }}</p>
                    </div>
                    <div>
                        @if($booking->status === 'active')
                            <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                <span class="material-icons text-sm">home</span> เข้าพักอยู่ (Active Lease)
                            </span>
                        @elseif($booking->status === 'pending')
                            <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1">
                                <span class="material-icons text-sm">schedule</span> รอการอนุมัติ (Pending)
                            </span>
                        @elseif($booking->status === 'completed')
                            <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300 flex items-center gap-1">
                                <span class="material-icons text-sm">task_alt</span> สิ้นสุดสัญญาแล้ว (Completed)
                            </span>
                        @else
                            <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 flex items-center gap-1">
                                <span class="material-icons text-sm">cancel</span> ยกเลิกสัญญา (Cancelled)
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Contract & Room Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Room Info -->
                    <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-1">
                            <span class="material-icons text-indigo-600 text-base">meeting_room</span> ข้อมูลห้องพัก
                        </h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between py-1 border-b border-slate-200">
                                <dt class="text-slate-500">หมายเลขห้อง:</dt>
                                <dd class="font-bold text-slate-900">ห้อง {{ $booking->room->room_number }}</dd>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200">
                                <dt class="text-slate-500">ประเภทห้องพัก:</dt>
                                <dd class="font-semibold text-indigo-700">{{ $booking->room->roomType->name }}</dd>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200">
                                <dt class="text-slate-500">ชั้นที่ตั้ง:</dt>
                                <dd class="text-slate-800">ชั้น {{ $booking->room->floor }}</dd>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200">
                                <dt class="text-slate-500">อัตราค่าเช่ารายเดือน:</dt>
                                <dd class="font-bold text-emerald-600">฿{{ number_format($booking->room->roomType->monthly_rate, 2) }} / เดือน</dd>
                            </div>
                            <div class="pt-1">
                                <dt class="text-slate-500 mb-1">รายละเอียดและสิ่งอำนวยความสะดวก:</dt>
                                <dd class="text-xs text-slate-700 bg-white p-2.5 rounded-lg border border-slate-200">
                                    {{ $booking->room->description ?? 'ไม่มีข้อมูลระบุเพิ่มเติม' }}
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Tenant Info -->
                    <div class="p-5 rounded-xl bg-slate-50 border border-slate-200">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 flex items-center gap-1">
                            <span class="material-icons text-indigo-600 text-base">badge</span> ข้อมูลผู้เช่า / นักศึกษา
                        </h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between py-1 border-b border-slate-200">
                                <dt class="text-slate-500">ชื่อผู้เช่า:</dt>
                                <dd class="font-bold text-slate-900">{{ $booking->user->name }}</dd>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200">
                                <dt class="text-slate-500">อีเมล:</dt>
                                <dd class="text-slate-800">{{ $booking->user->email }}</dd>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200">
                                <dt class="text-slate-500">เบอร์โทรศัพท์:</dt>
                                <dd class="font-semibold text-slate-900">{{ $booking->user->phone }}</dd>
                            </div>
                            <div class="flex justify-between py-1">
                                <dt class="text-slate-500">วันที่เริ่มเข้าพัก:</dt>
                                <dd class="font-semibold text-indigo-600">{{ $booking->check_in_date }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        @else
            <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center shadow-sm">
                <span class="material-icons text-4xl text-slate-400">search_off</span>
                <h3 class="text-base font-bold text-slate-800 mt-2">ไม่พบข้อมูลสัญญาเช่าด้วยรหัส "{{ $trackingCode }}"</h3>
                <p class="text-xs text-slate-500 mt-1">กรุณาตรวจสอบความถูกต้องของรหัสสัญญา แล้วลองค้นหาใหม่อีกครั้ง</p>
            </div>
        @endif
    @endif

    <!-- Available Rooms Showcase -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900">ห้องพักว่างที่พร้อมเปิดให้จอง</h2>
                <p class="text-xs text-slate-500">เลือกห้องพักที่ต้องการแล้วเข้าสู่ระบบเพื่อทำสัญญาจอง</p>
            </div>
            <a href="{{ route('user.rooms') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                ดูห้องทั้งหมด <span class="material-icons text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($availableRooms as $room)
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition">
                    <div class="p-5 border-b border-slate-100 flex items-start justify-between">
                        <div>
                            <span class="text-xs font-semibold text-indigo-600 uppercase tracking-wide">ชั้น {{ $room->floor }}</span>
                            <h3 class="text-lg font-bold text-slate-900 mt-0.5">ห้อง {{ $room->room_number }}</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            ว่าง (Available)
                        </span>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="text-sm">
                            <span class="text-slate-500 block text-xs">ประเภทห้องพัก:</span>
                            <span class="font-semibold text-slate-800">{{ $room->roomType->name }}</span>
                        </div>
                        <div class="text-sm">
                            <span class="text-slate-500 block text-xs">อัตราค่าเช่า:</span>
                            <span class="text-base font-bold text-indigo-600">฿{{ number_format($room->roomType->monthly_rate, 2) }} <span class="text-xs text-slate-400 font-normal">/ เดือน</span></span>
                        </div>
                        <p class="text-xs text-slate-600 line-clamp-2 bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                            {{ $room->description ?? 'ห้องพักมาตรฐาน พร้อมสิ่งอำนวยความสะดวกครบครัน' }}
                        </p>
                        <div class="pt-2">
                            <a href="{{ route('user.rooms') }}" class="w-full py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl transition flex items-center justify-center gap-1">
                                <span class="material-icons text-sm">bookmark_add</span> เข้าสู่ระบบเพื่อจองห้องนี้
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center p-8 bg-white rounded-2xl border border-slate-200 text-slate-500 text-sm">
                    ขณะนี้ยังไม่มีห้องพักว่างในระบบ
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
