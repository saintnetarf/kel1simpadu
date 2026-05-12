@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="bg-white rounded-xl shadow p-5">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold text-slate-800">Manajemen User</h1>
        @if($canManage)
            <a href="{{ route('users.create') }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm">Tambah User</a>
        @endif
    </div>

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..." class="w-full md:w-96 rounded-lg border border-slate-300 px-3 py-2">
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left border-b">
                    <th class="py-2 pr-4">Nama</th>
                    <th class="py-2 pr-4">Email</th>
                    <th class="py-2 pr-4">Role</th>
                    <th class="py-2 pr-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-b">
                        <td class="py-2 pr-4">{{ $user->name }}</td>
                        <td class="py-2 pr-4">{{ $user->email }}</td>
                        <td class="py-2 pr-4">
                            {{ $user->roles->pluck('name')->implode(', ') ?: '-' }}
                        </td>
                        <td class="py-2 pr-4">
                            @if($canManage)
                                <div class="flex gap-2">
                                    <a href="{{ route('users.edit', $user) }}" class="px-3 py-1 rounded bg-amber-500 text-white">Edit</a>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 rounded bg-rose-600 text-white">Hapus</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-slate-500">Read Only</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-slate-500">Tidak ada data user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection
