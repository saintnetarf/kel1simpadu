<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Http\Controllers\Controller;
use App\Models\AcademicClass;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademicClassController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('search');

        $classes = AcademicClass::query()
            ->with('studyProgram')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('studyProgram', function ($programQuery) use ($search) {
                        $programQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('master-data.index', [
            'title' => 'Master Kelas',
            'subtitle' => 'Kelola kelas per program studi.',
            'createUrl' => route('master-data.classes.create'),
            'canCreate' => true,
            'headers' => ['Nama', 'Program Studi', 'Level', 'Status'],
            'rows' => $classes->getCollection()->map(function (AcademicClass $class) {
                return [
                    'values' => [
                        $class->name,
                        $class->studyProgram?->name ?? '-',
                        $class->level !== null ? (string) $class->level : '-',
                        $class->is_active ? 'Aktif' : 'Nonaktif',
                    ],
                    'editUrl' => route('master-data.classes.edit', $class),
                    'deleteUrl' => route('master-data.classes.destroy', $class),
                ];
            })->all(),
            'paginator' => $classes,
            'search' => $search,
            'searchPlaceholder' => 'Cari nama kelas atau prodi...',
        ]);
    }

    public function create(): View
    {
        return view('master-data.form', [
            'title' => 'Tambah Kelas',
            'actionUrl' => route('master-data.classes.store'),
            'method' => 'POST',
            'cancelUrl' => route('master-data.classes.index'),
            'submitLabel' => 'Simpan',
            'fields' => $this->formFields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        AcademicClass::create($data);

        return redirect()->route('master-data.classes.index')->with('success', 'Kelas berhasil dibuat.');
    }

    public function edit(AcademicClass $class): View
    {
        return view('master-data.form', [
            'title' => 'Edit Kelas',
            'actionUrl' => route('master-data.classes.update', $class),
            'method' => 'PUT',
            'cancelUrl' => route('master-data.classes.index'),
            'submitLabel' => 'Update',
            'fields' => $this->formFields($class),
        ]);
    }

    public function update(Request $request, AcademicClass $class): RedirectResponse
    {
        $data = $request->validate($this->rules($class->id));

        $class->update($data);

        return redirect()->route('master-data.classes.index')->with('success', 'Kelas berhasil diupdate.');
    }

    public function destroy(AcademicClass $class): RedirectResponse
    {
        $class->delete();

        return redirect()->route('master-data.classes.index')->with('success', 'Kelas berhasil dihapus.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes', 'name')->where(function ($query) {
                    return $query->where('study_program_id', request()->integer('study_program_id'));
                })->ignore($ignoreId),
            ],
            'level' => ['nullable', 'integer', 'min:1', 'max:20'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    private function formFields(?AcademicClass $class = null): array
    {
        $studyPrograms = StudyProgram::orderBy('name')->get();

        return [
            ['name' => 'study_program_id', 'label' => 'Program Studi', 'type' => 'select', 'value' => old('study_program_id', $class?->study_program_id), 'required' => true, 'options' => $studyPrograms->map(fn (StudyProgram $program) => ['value' => $program->id, 'label' => $program->code.' - '.$program->name])->all()],
            ['name' => 'name', 'label' => 'Nama Kelas', 'type' => 'text', 'value' => old('name', $class?->name), 'required' => true],
            ['name' => 'level', 'label' => 'Level', 'type' => 'number', 'value' => old('level', $class?->level)],
            ['name' => 'is_active', 'label' => 'Status Aktif', 'type' => 'select', 'value' => old('is_active', $class?->is_active ?? true), 'required' => true, 'options' => [
                ['value' => 1, 'label' => 'Aktif'],
                ['value' => 0, 'label' => 'Nonaktif'],
            ]],
            ['name' => 'description', 'label' => 'Keterangan', 'type' => 'textarea', 'value' => old('description', $class?->description)],
        ];
    }
}
