@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="bg-white rounded-xl shadow p-5 max-w-2xl">
    <h1 class="text-xl font-bold text-slate-800 mb-4">Edit User</h1>

    <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Password Baru (opsional)</label>
            <input type="password" name="password" class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-2">Hak Akses (Role)</label>
            @php($userRoleIds = old('roles', $user->roles->pluck('id')->all()))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach($roles as $role)
                    <label class="flex items-center gap-2 border rounded-lg px-3 py-2">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" @checked(in_array($role->id, $userRoleIds))>
                        <span>{{ $role->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('users.index') }}" class="px-4 py-2 rounded-lg bg-slate-300">Batal</a>
            <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white">Update</button>
        </div>
    </form>
</div>
@endsection
