<!DOCTYPE html>
<html lang="th" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'ระบบบริหารจัดการหอพักนักศึกษา') }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        },
                    },
                    fontFamily: {
                        prompt: ['Prompt', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts & Material Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body { font-family: 'Prompt', sans-serif; }
    </style>
</head>
<body class="flex flex-col min-h-screen text-slate-800">
    <!-- Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-200 group-hover:bg-indigo-700 transition">
                            <span class="material-icons text-2xl">apartment</span>
                        </div>
                        <div>
                            <span class="font-bold text-lg text-slate-900 tracking-tight block leading-tight">Dormitory Hub</span>
                            <span class="text-xs text-indigo-600 font-medium">ระบบบริหารจัดการหอพักนักศึกษา</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Nav Links -->
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                    <a href="{{ route('track') }}" class="hover:text-indigo-600 transition flex items-center gap-1">
                        <span class="material-icons text-lg">search</span> ติดตามสัญญา
                    </a>
                    <a href="{{ url('/flutter') }}" target="_blank" class="hover:text-indigo-600 transition flex items-center gap-1 bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full text-xs font-semibold">
                        <span class="material-icons text-sm">devices</span> Flutter Web App
                    </a>
                    <a href="{{ url('/api-docs') }}" target="_blank" class="hover:text-indigo-600 transition flex items-center gap-1 text-slate-500">
                        <span class="material-icons text-sm">code</span> Raw API Docs
                    </a>

                    @auth
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 font-semibold flex items-center gap-1">
                                <span class="material-icons text-lg">dashboard</span> แผงควบคุมผู้ดูแล
                            </a>
                            <a href="{{ route('admin.room_types.index') }}" class="hover:text-indigo-600 transition flex items-center gap-1">
                                <span class="material-icons text-lg">category</span> ประเภทห้อง
                            </a>
                        @else
                            <a href="{{ route('user.rooms') }}" class="hover:text-indigo-600 transition flex items-center gap-1">
                                <span class="material-icons text-lg">meeting_room</span> ค้นหาห้องพัก
                            </a>
                            <a href="{{ route('user.my_bookings') }}" class="hover:text-indigo-600 transition flex items-center gap-1">
                                <span class="material-icons text-lg">receipt_long</span> สัญญาของฉัน
                            </a>
                        @endif
                    @endauth
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    @guest
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-slate-700 hover:text-indigo-600 transition">เข้าสู่ระบบ</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg shadow-sm hover:bg-indigo-700 transition flex items-center gap-1">
                            <span class="material-icons text-sm">person_add</span> สมัครสมาชิก
                        </a>
                    @else
                        <div class="flex items-center gap-3">
                            <div class="text-right hidden sm:block">
                                <span class="block text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</span>
                                <span class="block text-xs text-indigo-600 uppercase font-bold">{{ Auth::user()->role }}</span>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition flex items-center gap-1">
                                    <span class="material-icons text-sm">logout</span> ออกจากระบบ
                                </button>
                            </form>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3 shadow-sm mb-4">
                <span class="material-icons text-emerald-600">check_circle</span>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center gap-3 shadow-sm mb-4">
                <span class="material-icons text-rose-600">error</span>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 shadow-sm mb-4">
                <div class="flex items-center gap-2 font-semibold text-sm mb-1">
                    <span class="material-icons text-rose-600 text-base">warning</span> พบข้อผิดพลาด:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12 text-slate-500 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>© 2026 ระบบบริหารจัดการหอพักนักศึกษา (Dormitory Management System) | Laravel 11.x + MySQL + Flutter Web</p>
            <div class="flex items-center gap-4 font-medium">
                <a href="{{ url('/flutter') }}" class="hover:text-indigo-600">Flutter Client</a>
                <a href="{{ url('/api-docs') }}" class="hover:text-indigo-600">JSON API Docs</a>
            </div>
        </div>
    </footer>
</body>
</html>
