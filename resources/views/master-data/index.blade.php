@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="bg-white rounded-xl shadow p-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">{{ $title }}</h1>
            @if(!empty($subtitle))
                <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>

        @if($canCreate ?? false)
            <a href="{{ $createUrl }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium">
                Tambah Data
            </a>
        @endif
    </div>

    <form method="GET" class="mb-4">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ $searchPlaceholder ?? 'Cari data...' }}" class="w-full md:w-96 rounded-lg border border-slate-300 px-3 py-2">
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left border-b border-slate-200 text-slate-600">
                    @foreach($headers as $header)
                        <th class="py-3 pr-4 font-semibold">{{ $header }}</th>
                    @endforeach
                    <th class="py-3 pr-4 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr class="border-b border-slate-100 align-top">
                        @foreach($row['values'] as $value)
                            <td class="py-3 pr-4 text-slate-700">{!! $value !!}</td>
                        @endforeach
                        <td class="py-3 pr-4">
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ $row['editUrl'] }}" class="px-3 py-1.5 rounded-lg bg-amber-500 text-white text-xs font-medium">Edit</a>
                                <form action="{{ $row['deleteUrl'] }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) + 1 }}" class="py-6 text-slate-500">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $paginator->links() }}
    </div>
</div>
@endsection

