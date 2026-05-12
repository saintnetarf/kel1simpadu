<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Auth Center')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 py-6">
        @auth
            <div class="bg-white rounded-xl shadow p-4 mb-4 flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Login sebagai</p>
                    <p class="font-semibold text-slate-800">{{ auth()->user()->name }} ({{ auth()->user()->email }})</p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <div class="flex flex-wrap gap-2 justify-end">
                        <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm">Manajemen User</a>
                        <div class="flex flex-wrap gap-2 rounded-lg border border-slate-200 bg-slate-50 p-2">
                            <a href="{{ route('master-data.academic-years.index') }}" class="px-3 py-2 rounded-lg bg-teal-700 text-white text-xs font-medium">Tahun Akademik</a>
                            <a href="{{ route('master-data.study-programs.index') }}" class="px-3 py-2 rounded-lg bg-teal-700 text-white text-xs font-medium">Program Studi</a>
                            <a href="{{ route('master-data.classes.index') }}" class="px-3 py-2 rounded-lg bg-teal-700 text-white text-xs font-medium">Kelas</a>
                            <a href="{{ route('master-data.courses.index') }}" class="px-3 py-2 rounded-lg bg-teal-700 text-white text-xs font-medium">Mata Kuliah</a>
                            <a href="{{ route('master-data.students.index') }}" class="px-3 py-2 rounded-lg bg-teal-700 text-white text-xs font-medium">Mahasiswa</a>
                            <a href="{{ route('master-data.pegawai.index') }}" class="px-3 py-2 rounded-lg bg-teal-700 text-white text-xs font-medium">Pegawai</a>
                            <a href="{{ route('master-data.class-participants.index') }}" class="px-3 py-2 rounded-lg bg-teal-700 text-white text-xs font-medium">Peserta Kelas</a>
                        </div>
                        <a href="/docs/api" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm">API Docs</a>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm">Logout</button>
                    </form>
                </div>
            </div>
        @endauth

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-emerald-100 text-emerald-800 px-4 py-3">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-lg bg-rose-100 text-rose-800 px-4 py-3">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>
