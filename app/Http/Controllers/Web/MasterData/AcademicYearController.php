<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('search');

        $academicYears = AcademicYear::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('start_year', 'like', "%{$search}%")
                    ->orWhere('end_year', 'like', "%{$search}%");
            })
            ->orderByDesc('is_active')
            ->orderByDesc('start_year')
            ->paginate(10)
            ->withQueryString();

        return view('master-data.index', [
            'title' => 'Master Tahun Akademik',
            'subtitle' => 'Kelola periode akademik aktif dan arsip tahun akademik.',
            'createUrl' => route('master-data.academic-years.create'),
            'canCreate' => true,
            'headers' => ['Nama', 'Tahun Mulai', 'Tahun Selesai', 'Status'],
            'rows' => $academicYears->getCollection()->map(function (AcademicYear $academicYear) {
                return [
                    'values' => [
                        $academicYear->name,
                        (string) $academicYear->start_year,
                        (string) $academicYear->end_year,
                        $academicYear->is_active ? 'Aktif' : 'Nonaktif',
                    ],
                    'editUrl' => route('master-data.academic-years.edit', $academicYear),
                    'deleteUrl' => route('master-data.academic-years.destroy', $academicYear),
                ];
            })->all(),
            'paginator' => $academicYears,
            'search' => $search,
            'searchPlaceholder' => 'Cari nama atau tahun...',
        ]);
    }

    public function create(): View
    {
        return view('master-data.form', [
            'title' => 'Tambah Tahun Akademik',
            'actionUrl' => route('master-data.academic-years.store'),
            'method' => 'POST',
            'cancelUrl' => route('master-data.academic-years.index'),
            'submitLabel' => 'Simpan',
            'fields' => $this->formFields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        AcademicYear::create($data);

        return redirect()->route('master-data.academic-years.index')->with('success', 'Tahun akademik berhasil dibuat.');
    }

    public function edit(AcademicYear $academicYear): View
    {
        return view('master-data.form', [
            'title' => 'Edit Tahun Akademik',
            'actionUrl' => route('master-data.academic-years.update', $academicYear),
            'method' => 'PUT',
            'cancelUrl' => route('master-data.academic-years.index'),
            'submitLabel' => 'Update',
            'fields' => $this->formFields($academicYear),
        ]);
    }

    public function update(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $data = $request->validate($this->rules($academicYear->id));

        $academicYear->update($data);

        return redirect()->route('master-data.academic-years.index')->with('success', 'Tahun akademik berhasil diupdate.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        $academicYear->delete();

        return redirect()->route('master-data.academic-years.index')->with('success', 'Tahun akademik berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'start_year' => ['required', 'integer', 'between:1900,2100'],
            'end_year' => ['required', 'integer', 'between:1900,2100', 'gte:start_year'],
            'is_active' => ['required', 'boolean'],
            'description' => ['nullable', 'string'],
        ];
    }

    private function formFields(?AcademicYear $academicYear = null): array
    {
        return [
            ['name' => 'name', 'label' => 'Nama Tahun Akademik', 'type' => 'text', 'value' => old('name', $academicYear?->name), 'required' => true],
            ['name' => 'start_year', 'label' => 'Tahun Mulai', 'type' => 'number', 'value' => old('start_year', $academicYear?->start_year), 'required' => true],
            ['name' => 'end_year', 'label' => 'Tahun Selesai', 'type' => 'number', 'value' => old('end_year', $academicYear?->end_year), 'required' => true],
            ['name' => 'is_active', 'label' => 'Status Aktif', 'type' => 'select', 'value' => old('is_active', $academicYear?->is_active ?? true), 'required' => true, 'options' => [
                ['value' => 1, 'label' => 'Aktif'],
                ['value' => 0, 'label' => 'Nonaktif'],
            ]],
            ['name' => 'description', 'label' => 'Keterangan', 'type' => 'textarea', 'value' => old('description', $academicYear?->description)],
        ];
    }
}

