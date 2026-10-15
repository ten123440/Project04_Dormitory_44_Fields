@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-8">
    <!-- Card Container -->
    <div class="bg-white p-8 rounded-2xl shadow-xl border border-slate-200">
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                <span class="material-icons text-3xl">lock</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900">เข้าสู่ระบบ</h1>
            <p class="text-sm text-slate-500 mt-1">ระบบบริหารจัดการหอพักนักศึกษา</p>
        </div>

        <!-- 1-Click Demo Fill Section -->
        <div class="mb-6 p-4 rounded-xl bg-slate-50 border border-slate-200">
            <div class="text-xs font-semibold text-slate-600 mb-2 flex items-center gap-1">
                <span class="material-icons text-sm text-indigo-500">touch_app</span> ปุ่มคลิกเดียวเติมรหัส Demo:
            </div>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="fillCredentials('admin@dormitory.com', 'admin1234')"
                    class="px-3 py-2 text-xs font-medium bg-white hover:bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-lg shadow-sm transition flex flex-col items-center">
                    <span class="font-bold">Demo Admin</span>
                    <span class="text-[10px] text-slate-400">admin@dormitory.com</span>
                </button>
                <button type="button" onclick="fillCredentials('user@dormitory.com', 'user1234')"
                    class="px-3 py-2 text-xs font-medium bg-white hover:bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg shadow-sm transition flex flex-col items-center">
                    <span class="font-bold">Demo Student</span>
                    <span class="text-[10px] text-slate-400">user@dormitory.com</span>
                </button>
            </div>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">อีเมลผู้ใช้งาน</label>
                <div class="relative">
                    <span class="material-icons absolute left-3 top-2.5 text-slate-400 text-lg">email</span>
                    <input type="email" name="email" id="email" required
                        class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                        placeholder="you@dormitory.com" value="{{ old('email') }}">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">รหัสผ่าน</label>
                <div class="relative">
                    <span class="material-icons absolute left-3 top-2.5 text-slate-400 text-lg">key</span>
                    <input type="password" name="password" id="password" required
                        class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                        placeholder="••••••••">
                </div>
            </div>

            <button type="submit"
                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl shadow-md shadow-indigo-200 transition flex items-center justify-center gap-2">
                <span class="material-icons text-sm">login</span> เข้าสู่ระบบ
            </button>
        </form>

        <div class="text-center mt-6 text-xs text-slate-500">
            ยังไม่มีบัญชีนักศึกษา?
            <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">ลงทะเบียนผู้เช่าใหม่</a>
        </div>
    </div>
</div>

<script>
    function fillCredentials(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection
