@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">สัญญาเช่าและประวัติการจองของฉัน</h1>
            <p class="text-xs text-slate-500 mt-1">รายการห้องพักที่คุณได้ทำสัญญาหรือยื่นคำขอจองไว้</p>
        </div>
        <a href="{{ route('user.rooms') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition flex items-center gap-1 shadow-sm w-fit">
            <span class="material-icons text-sm">add</span> ค้นหาและจองห้องพักเพิ่ม
        </a>
    </div>

    <!-- Bookings List Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="p-4">รหัสสัญญา</th>
                        <th class="p-4">ห้องพัก</th>
                        <th class="p-4">ประเภทห้อง</th>
                        <th class="p-4">ค่าเช่ารายเดือน</th>
                        <th class="p-4">วันเริ่มเข้าพัก</th>
                        <th class="p-4">สถานะสัญญา</th>
                        <th class="p-4 text-center">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 font-mono font-bold text-indigo-700">{{ $booking->tracking_code }}</td>
                            <td class="p-4 font-bold text-slate-900">ห้อง {{ $booking->room->room_number }} (ชั้น {{ $booking->room->floor }})</td>
                            <td class="p-4">{{ $booking->room->roomType->name }}</td>
                            <td class="p-4 font-semibold text-emerald-600">{{ $booking->monthly_rate !== null ? '฿'.number_format($booking->monthly_rate, 2) : 'รายการเดิม: ไม่ระบุราคา ณ วันจอง' }}<small class="block">เงินประกัน {{ $booking->deposit_amount ?? 'ไม่ระบุ' }} บาท</small></td>
                            <td class="p-4">{{ $booking->check_in_date }}<small class="block">ถึง {{ $booking->check_out_date ?? 'ไม่ระบุ' }}<br>บันทึก {{ $booking->created_at ?? 'ไม่ทราบวันเดิม' }}<br>{{ $booking->notes }}</small></td>
                            <td class="p-4">
                                @if($booking->status === 'active')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">เข้าพักอยู่ (Active)</span>
                                @elseif($booking->status === 'pending')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">รออนุมัติ (Pending)</span>
                                @elseif($booking->status === 'completed')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">สิ้นสุดสัญญา (Completed)</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">ยกเลิก (Cancelled)</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <a href="{{ route('track', ['code' => $booking->tracking_code]) }}"
                                    class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-medium rounded-lg transition inline-flex items-center gap-1">
                                    <span class="material-icons text-xs">visibility</span> ติดตามสัญญา
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                คุณยังไม่มีประวัติการจองหรือสัญญาเช่าในระบบ
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
