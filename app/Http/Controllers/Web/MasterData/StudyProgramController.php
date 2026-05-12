<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Http\Controllers\Controller;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudyProgramController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('search');

        $studyPrograms = StudyProgram::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('degree_level', 'like', "%{$search}%");
            })
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('master-data.index', [
            'title' => 'Master Program Studi',
            'subtitle' => 'Kelola kode dan nama program studi.',
            'createUrl' => route('master-data.study-programs.create'),
            'canCreate' => true,
            'headers' => ['Kode', 'Nama', 'Jenjang', 'Status'],
            'rows' => $studyPrograms->getCollection()->map(function (StudyProgram $studyProgram) {
                return [
                    'values' => [
                        $studyProgram->code,
                        $studyProgram->name,
                        $studyProgram->degree_level ?: '-',
                        $studyProgram->is_active ? 'Aktif' : 'Nonaktif',
                    ],
                    'editUrl' => route('master-data.study-programs.edit', $studyProgram),
                    'deleteUrl' => route('master-data.study-programs.destroy', $studyProgram),
                ];
            })->all(),
            'paginator' => $studyPrograms,
            'search' => $search,
            'searchPlaceholder' => 'Cari kode atau nama prodi...',
        ]);
    }

    public function create(): View
    {
        return view('master-data.form', [
            'title' => 'Tambah Program Studi',
            'actionUrl' => route('master-data.study-programs.store'),
            'method' => 'POST',
            'cancelUrl' => route('master-data.study-programs.index'),
            'submitLabel' => 'Simpan',
            'fields' => $this->formFields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        StudyProgram::create($data);

        return redirect()->route('master-data.study-programs.index')->with('success', 'Program studi berhasil dibuat.');
    }

    public function edit(StudyProgram $studyProgram): View
    {
        return view('master-data.form', [
            'title' => 'Edit Program Studi',
            'actionUrl' => route('master-data.study-programs.update', $studyProgram),
            'method' => 'PUT',
            'cancelUrl' => route('master-data.study-programs.index'),
            'submitLabel' => 'Update',
            'fields' => $this->formFields($studyProgram),
        ]);
    }

    public function update(Request $request, StudyProgram $studyProgram): RedirectResponse
    {
        $data = $request->validate($this->rules($studyProgram->id));

        $studyProgram->update($data);

        return redirect()->route('master-data.study-programs.index')->with('success', 'Program studi berhasil diupdate.');
    }

    public function destroy(StudyProgram $studyProgram): RedirectResponse
    {
        $studyProgram->delete();

        return redirect()->route('master-data.study-programs.index')->with('success', 'Program studi berhasil dihapus.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('study_programs', 'code')->ignore($ignoreId)],
            'name' => ['required', 'string', 'max:255'],
            'degree_level' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    private function formFields(?StudyProgram $studyProgram = null): array
    {
        return [
            ['name' => 'code', 'label' => 'Kode Prodi', 'type' => 'text', 'value' => old('code', $studyProgram?->code), 'required' => true],
            ['name' => 'name', 'label' => 'Nama Prodi', 'type' => 'text', 'value' => old('name', $studyProgram?->name), 'required' => true],
            ['name' => 'degree_level', 'label' => 'Jenjang', 'type' => 'text', 'value' => old('degree_level', $studyProgram?->degree_level)],
            ['name' => 'is_active', 'label' => 'Status Aktif', 'type' => 'select', 'value' => old('is_active', $studyProgram?->is_active ?? true), 'required' => true, 'options' => [
                ['value' => 1, 'label' => 'Aktif'],
                ['value' => 0, 'label' => 'Nonaktif'],
            ]],
            ['name' => 'description', 'label' => 'Keterangan', 'type' => 'textarea', 'value' => old('description', $studyProgram?->description)],
        ];
    }
}
