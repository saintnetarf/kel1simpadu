<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('search');

        $courses = Course::query()
            ->with('studyProgram')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('studyProgram', function ($programQuery) use ($search) {
                        $programQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            })
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('master-data.index', [
            'title' => 'Master Mata Kuliah',
            'subtitle' => 'Kelola mata kuliah per program studi.',
            'createUrl' => route('master-data.courses.create'),
            'canCreate' => true,
            'headers' => ['Kode', 'Nama', 'SKS', 'Semester', 'Program Studi', 'Status'],
            'rows' => $courses->getCollection()->map(function (Course $course) {
                return [
                    'values' => [
                        $course->code,
                        $course->name,
                        (string) $course->credits,
                        $course->semester !== null ? (string) $course->semester : '-',
                        $course->studyProgram?->name ?? '-',
                        $course->is_active ? 'Aktif' : 'Nonaktif',
                    ],
                    'editUrl' => route('master-data.courses.edit', $course),
                    'deleteUrl' => route('master-data.courses.destroy', $course),
                ];
            })->all(),
            'paginator' => $courses,
            'search' => $search,
            'searchPlaceholder' => 'Cari kode, nama, atau prodi...',
        ]);
    }

    public function create(): View
    {
        return view('master-data.form', [
            'title' => 'Tambah Mata Kuliah',
            'actionUrl' => route('master-data.courses.store'),
            'method' => 'POST',
            'cancelUrl' => route('master-data.courses.index'),
            'submitLabel' => 'Simpan',
            'fields' => $this->formFields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        Course::create($data);

        return redirect()->route('master-data.courses.index')->with('success', 'Mata kuliah berhasil dibuat.');
    }

    public function edit(Course $course): View
    {
        return view('master-data.form', [
            'title' => 'Edit Mata Kuliah',
            'actionUrl' => route('master-data.courses.update', $course),
            'method' => 'PUT',
            'cancelUrl' => route('master-data.courses.index'),
            'submitLabel' => 'Update',
            'fields' => $this->formFields($course),
        ]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $data = $request->validate($this->rules($course->id));

        $course->update($data);

        return redirect()->route('master-data.courses.index')->with('success', 'Mata kuliah berhasil diupdate.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return redirect()->route('master-data.courses.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
            'code' => ['required', 'string', 'max:50', Rule::unique('courses', 'code')->ignore($ignoreId)],
            'name' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'integer', 'min:0', 'max:20'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    private function formFields(?Course $course = null): array
    {
        $studyPrograms = StudyProgram::orderBy('name')->get();

        return [
            ['name' => 'study_program_id', 'label' => 'Program Studi', 'type' => 'select', 'value' => old('study_program_id', $course?->study_program_id), 'required' => true, 'options' => $studyPrograms->map(fn (StudyProgram $program) => ['value' => $program->id, 'label' => $program->code.' - '.$program->name])->all()],
            ['name' => 'code', 'label' => 'Kode MK', 'type' => 'text', 'value' => old('code', $course?->code), 'required' => true],
            ['name' => 'name', 'label' => 'Nama Mata Kuliah', 'type' => 'text', 'value' => old('name', $course?->name), 'required' => true],
            ['name' => 'credits', 'label' => 'SKS', 'type' => 'number', 'value' => old('credits', $course?->credits ?? 0), 'required' => true],
            ['name' => 'semester', 'label' => 'Semester', 'type' => 'number', 'value' => old('semester', $course?->semester)],
            ['name' => 'is_active', 'label' => 'Status Aktif', 'type' => 'select', 'value' => old('is_active', $course?->is_active ?? true), 'required' => true, 'options' => [
                ['value' => 1, 'label' => 'Aktif'],
                ['value' => 0, 'label' => 'Nonaktif'],
            ]],
            ['name' => 'description', 'label' => 'Keterangan', 'type' => 'textarea', 'value' => old('description', $course?->description)],
        ];
    }
}
