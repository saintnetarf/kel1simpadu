@extends('layouts.app')

@section('title', $title ?? 'Detail Pegawai')

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-2xl">
    <h1 class="text-2xl font-bold text-slate-800 mb-2">{{ $pegawai->name }}</h1>

    <div class="grid grid-cols-2 gap-4">
        <div><strong>NIP/NIK</strong><div class="text-slate-700">{{ $pegawai->employee_number }}</div></div>
        <div><strong>Jabatan</strong><div class="text-slate-700">{{ $pegawai->position ?? '-' }}</div></div>
        <div><strong>Email</strong><div class="text-slate-700">{{ $pegawai->email ?? '-' }}</div></div>
        <div><strong>No. HP</strong><div class="text-slate-700">{{ $pegawai->phone ?? '-' }}</div></div>
        <div><strong>Status</strong><div class="text-slate-700">{{ $pegawai->is_active ? 'Aktif' : 'Nonaktif' }}</div></div>
    </div>

    <div class="mt-6">
        <a href="{{ route('master-data.class-participants.index') }}" class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700">Kembali</a>
        @can('update', $pegawai)
            <a href="{{ route('master-data.pegawai.edit', $pegawai) }}" class="px-4 py-2 rounded-lg bg-amber-500 text-white">Edit</a>
        @endcan
    </div>
</div>
@endsection
