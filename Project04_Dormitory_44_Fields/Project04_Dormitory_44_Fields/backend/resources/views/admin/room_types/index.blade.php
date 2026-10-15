@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">จัดการประเภทห้องพักและอัตราค่าเช่า</h1>
            <p class="text-xs text-slate-500 mt-1">กำหนดประเภทห้องพักและอัตราค่าเช่ารายเดือนในระบบ</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-xs font-semibold rounded-xl transition flex items-center gap-1 shadow-sm w-fit">
            <span class="material-icons text-sm">arrow_back</span> กลับสู่แดชบอร์ด
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Add Room Type Form -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 h-fit">
            <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-1">
                <span class="material-icons text-indigo-600 text-lg">add_circle</span> เพิ่มประเภทห้องพักใหม่
            </h2>
            <form action="{{ route('admin.room_types.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">ชื่อประเภทห้องพัก</label>
                    <input type="text" name="name" required placeholder="เช่น ห้องแอร์พรีเมียมเตียงเดี่ยว"
                        class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">อัตราค่าเช่ารายเดือน (บาท)</label>
                    <input type="number" step="0.01" name="monthly_rate" required placeholder="3500.00"
                        class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
<label class="block text-xs font-semibold text-slate-700">เงินประกัน (บาท)<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="number" name="deposit_amount" value="{{ old('deposit_amount') }}" min="0" step="0.01"></label>
<label class="block text-xs font-semibold text-slate-700">รองรับผู้พัก (คน)<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="number" name="capacity" value="{{ old('capacity') }}" min="1" max="65535"></label>
<label class="block text-xs font-semibold text-slate-700">รายละเอียดประเภทห้อง<input class="w-full border rounded-xl p-2 mt-1 mb-3" type="text" name="description" value="{{ old('description') }}" maxlength="5000"></label>
                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl transition shadow-md shadow-indigo-100 flex items-center justify-center gap-1 mt-2">
                    <span class="material-icons text-sm">save</span> บันทึกประเภทห้อง
                </button>
            </form>
        </div>

        <!-- Room Types Table -->
        <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-200">
                รายการประเภทห้องพักทั้งหมด ({{ $roomTypes->count() }} ประเภท)
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="p-3">รหัส (ID)</th>
                            <th class="p-3">ชื่อประเภทห้องพัก</th>
                            <th class="p-3">อัตราค่าเช่าต่อเดือน</th>
                            <th class="p-3">จำนวนห้องพัก</th>
                            <th class="p-3">วันที่บันทึก</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($roomTypes as $type)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3 font-mono font-bold text-slate-400">#{{ $type->id }}</td>
                                <td class="p-3 font-bold text-slate-900">{{ $type->name }}<small class="block font-normal">รองรับ {{ $type->capacity ?? 'ไม่ระบุ' }} คน • เงินประกัน {{ $type->deposit_amount !== null ? number_format($type->deposit_amount, 2) : 'ไม่ระบุ' }} บาท<br>{{ $type->description }}</small></td>
                                <td class="p-3 font-bold text-emerald-600">฿{{ number_format($type->monthly_rate, 2) }}</td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                        {{ $type->rooms_count ?? 0 }} ห้อง
                                    </span>
                                </td>
                                <td class="p-3 text-slate-400">{{ $type->created_at }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-400">ยังไม่มีข้อมูลประเภทห้องพัก</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
