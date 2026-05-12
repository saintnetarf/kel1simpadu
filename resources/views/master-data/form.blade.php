@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="bg-white rounded-xl shadow p-6 max-w-3xl">
    <h1 class="text-2xl font-bold text-slate-800 mb-1">{{ $title }}</h1>
    <p class="text-sm text-slate-500 mb-6">Lengkapi data master dengan teliti sebelum disimpan.</p>

    <form action="{{ $actionUrl }}" method="POST" class="space-y-4">
        @csrf
        @if(($method ?? 'POST') !== 'POST')
            @method($method)
        @endif

        @foreach($fields as $field)
            @php
                $currentValue = old($field['name'], $field['value'] ?? '');
                $fieldType = $field['type'] ?? 'text';
                $fieldId = 'field-' . $field['name'];
            @endphp

            <div>
                <label for="{{ $fieldId }}" class="block text-sm font-medium text-slate-700 mb-1">
                    {{ $field['label'] }}
                    @if(!empty($field['required']))
                        <span class="text-rose-500">*</span>
                    @endif
                </label>

                @if($fieldType === 'textarea')
                    <textarea
                        id="{{ $fieldId }}"
                        name="{{ $field['name'] }}"
                        rows="{{ $field['rows'] ?? 4 }}"
                        placeholder="{{ $field['placeholder'] ?? '' }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2"
                    >{{ $currentValue }}</textarea>
                @elseif($fieldType === 'select')
                    <select id="{{ $fieldId }}" name="{{ $field['name'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                        <option value="">Pilih {{ strtolower($field['label']) }}</option>
                        @foreach($field['options'] ?? [] as $option)
                            <option value="{{ $option['value'] }}" @selected((string) $currentValue === (string) $option['value'])>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                @else
                    <input
                        id="{{ $fieldId }}"
                        type="{{ $fieldType }}"
                        name="{{ $field['name'] }}"
                        value="{{ $currentValue }}"
                        placeholder="{{ $field['placeholder'] ?? '' }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2"
                    >
                @endif

                @if(!empty($field['help']))
                    <p class="mt-1 text-xs text-slate-500">{{ $field['help'] }}</p>
                @endif
            </div>
        @endforeach

        <div class="flex flex-wrap gap-2 pt-2">
            <a href="{{ $cancelUrl }}" class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 font-medium">Batal</a>
            <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium">{{ $submitLabel }}</button>
        </div>
    </form>
</div>
@endsection

