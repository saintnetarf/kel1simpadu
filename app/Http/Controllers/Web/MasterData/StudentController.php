<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('search');

        $students = Student::query()
            ->with('studyProgram')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('student_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('studyProgram', function ($programQuery) use ($search) {
                        $programQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            })
            ->orderBy('student_number')
            ->paginate(10)
            ->withQueryString();

        return view('master-data.index', [
            'title' => 'Master Mahasiswa',
            'subtitle' => 'Kelola data mahasiswa per program studi.',
            'createUrl' => route('master-data.students.create'),
            'canCreate' => true,
            'headers' => ['NIM', 'Nama', 'Program Studi', 'Status'],
            'rows' => $students->getCollection()->map(function (Student $student) {
                return [
                    'values' => [
                        $student->student_number,
                        $student->name,
                        $student->studyProgram?->name ?? '-',
                        $student->is_active ? 'Aktif' : 'Nonaktif',
                    ],
                    'editUrl' => route('master-data.students.edit', $student),
                    'deleteUrl' => route('master-data.students.destroy', $student),
                ];
            })->all(),
            'paginator' => $students,
            'search' => $search,
            'searchPlaceholder' => 'Cari NIM, nama, atau prodi...',
        ]);
    }

    public function create(): View
    {
        return view('master-data.form', [
            'title' => 'Tambah Mahasiswa',
            'actionUrl' => route('master-data.students.store'),
            'method' => 'POST',
            'cancelUrl' => route('master-data.students.index'),
            'submitLabel' => 'Simpan',
            'fields' => $this->formFields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        Student::create($data);

        return redirect()->route('master-data.students.index')->with('success', 'Mahasiswa berhasil dibuat.');
    }

    public function edit(Student $student): View
    {
        return view('master-data.form', [
            'title' => 'Edit Mahasiswa',
            'actionUrl' => route('master-data.students.update', $student),
            'method' => 'PUT',
            'cancelUrl' => route('master-data.students.index'),
            'submitLabel' => 'Update',
            'fields' => $this->formFields($student),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate($this->rules($student->id));

        $student->update($data);

        return redirect()->route('master-data.students.index')->with('success', 'Mahasiswa berhasil diupdate.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()->route('master-data.students.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
            'student_number' => ['required', 'string', 'max:50', Rule::unique('students', 'student_number')->ignore($ignoreId)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    private function formFields(?Student $student = null): array
    {
        $studyPrograms = StudyProgram::orderBy('name')->get();

        return [
            ['name' => 'study_program_id', 'label' => 'Program Studi', 'type' => 'select', 'value' => old('study_program_id', $student?->study_program_id), 'required' => true, 'options' => $studyPrograms->map(fn (StudyProgram $program) => ['value' => $program->id, 'label' => $program->code.' - '.$program->name])->all()],
            ['name' => 'student_number', 'label' => 'NIM', 'type' => 'text', 'value' => old('student_number', $student?->student_number), 'required' => true],
            ['name' => 'name', 'label' => 'Nama Mahasiswa', 'type' => 'text', 'value' => old('name', $student?->name), 'required' => true],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'value' => old('email', $student?->email)],
            ['name' => 'phone', 'label' => 'No. HP', 'type' => 'text', 'value' => old('phone', $student?->phone)],
            ['name' => 'is_active', 'label' => 'Status Aktif', 'type' => 'select', 'value' => old('is_active', $student?->is_active ?? true), 'required' => true, 'options' => [
                ['value' => 1, 'label' => 'Aktif'],
                ['value' => 0, 'label' => 'Nonaktif'],
            ]],
            ['name' => 'address', 'label' => 'Alamat', 'type' => 'textarea', 'value' => old('address', $student?->address)],
        ];
    }
}
