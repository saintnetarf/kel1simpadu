@extends('layouts.app')

@section('title', 'Login Auth Center')

@section('content')
<div class="max-w-md mx-auto mt-16 bg-white rounded-2xl shadow p-6">
    <h1 class="text-2xl font-bold text-slate-800 mb-1">Login Auth Center</h1>
    <p class="text-sm text-slate-500 mb-6">Masuk untuk mengelola user dan hak akses.</p>

    <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input name="password" type="password" required class="w-full rounded-lg border border-slate-300 px-3 py-2">
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" value="1"> Ingat saya
        </label>

        <button class="w-full rounded-lg bg-slate-900 text-white py-2.5 font-medium">Login</button>
    </form>
</div>
@endsection
