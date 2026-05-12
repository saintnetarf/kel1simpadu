<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Http\Controllers\Controller;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\ClassParticipant;
use App\Models\Course;
use App\Models\Pegawai;
use App\Models\Student;
use App\Models\StudyProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassParticipantController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('search');

        $participants = ClassParticipant::query()
            ->with(['academicYear', 'studyProgram', 'academicClass', 'course', 'student', 'pegawai'])
            ->when($search !== '', function ($query) use ($search) {
                $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('name', 'like', "%{$search}%")->orWhere('student_number', 'like', "%{$search}%"))
                    ->orWhereHas('course', fn ($courseQuery) => $courseQuery->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
                    ->orWhereHas('academicClass', fn ($classQuery) => $classQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('academicYear', fn ($yearQuery) => $yearQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('pegawai', fn ($pegawaiQuery) => $pegawaiQuery->where('name', 'like', "%{$search}%")->orWhere('employee_number', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('master-data.index', [
            'title' => 'Master Peserta Kelas',
            'subtitle' => 'Relasi peserta dengan tahun akademik, prodi, kelas, mata kuliah, mahasiswa, dan pegawai.',
            'createUrl' => route('master-data.class-participants.create'),
            'canCreate' => true,
            'headers' => ['Tahun Akademik', 'Prodi', 'Kelas', 'Mata Kuliah', 'Mahasiswa', 'Pegawai', 'Status'],
            'rows' => $participants->getCollection()->map(function (ClassParticipant $participant) {
                return [
                    'values' => [
                        $participant->academicYear?->name ?? '-',
                        $participant->studyProgram?->name ?? '-',
                        $participant->academicClass?->name ?? '-',
                        $participant->course?->name ?? '-',
                        $participant->student?->name ?? '-',
                        $participant->pegawai
                            ? '<a href="'.route('master-data.pegawai.show', $participant->pegawai->id).'" class="text-indigo-600">'.e($participant->pegawai->name).'</a>'
                            : '-',
                        $this->statusLabel($participant->participant_status),
                    ],
                    'editUrl' => route('master-data.class-participants.edit', $participant),
                    'deleteUrl' => route('master-data.class-participants.destroy', $participant),
                ];
            })->all(),
            'paginator' => $participants,
            'search' => $search,
            'searchPlaceholder' => 'Cari mahasiswa, pegawai, kelas, atau MK...',
        ]);
    }

    public function create(): View
    {
        return view('master-data.form', [
            'title' => 'Tambah Peserta Kelas',
            'actionUrl' => route('master-data.class-participants.store'),
            'method' => 'POST',
            'cancelUrl' => route('master-data.class-participants.index'),
            'submitLabel' => 'Simpan',
            'fields' => $this->formFields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $this->assertRelatedStudyPrograms($data);

        ClassParticipant::create($data);

        return redirect()->route('master-data.class-participants.index')->with('success', 'Peserta kelas berhasil dibuat.');
    }

    public function edit(ClassParticipant $classParticipant): View
    {
        return view('master-data.form', [
            'title' => 'Edit Peserta Kelas',
            'actionUrl' => route('master-data.class-participants.update', $classParticipant),
            'method' => 'PUT',
            'cancelUrl' => route('master-data.class-participants.index'),
            'submitLabel' => 'Update',
            'fields' => $this->formFields($classParticipant),
        ]);
    }

    public function update(Request $request, ClassParticipant $classParticipant): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $this->assertRelatedStudyPrograms($data);

        $classParticipant->update($data);

        return redirect()->route('master-data.class-participants.index')->with('success', 'Peserta kelas berhasil diupdate.');
    }

    public function destroy(ClassParticipant $classParticipant): RedirectResponse
    {
        $classParticipant->delete();

        return redirect()->route('master-data.class-participants.index')->with('success', 'Peserta kelas berhasil dihapus.');
    }

    private function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'pegawai_id' => ['required', 'integer', 'exists:pegawai,id'],
            'participant_status' => ['required', 'integer', 'in:0,1,2'],
            'notes' => ['nullable', 'string'],
        ];
    }

    private function formFields(?ClassParticipant $participant = null): array
    {
        $academicYears = AcademicYear::orderByDesc('start_year')->get();
        $studyPrograms = StudyProgram::orderBy('name')->get();
        $classes = AcademicClass::with('studyProgram')->orderBy('name')->get();
        $courses = Course::with('studyProgram')->orderBy('name')->get();
        $students = Student::with('studyProgram')->orderBy('name')->get();
        $pegawai = Pegawai::where('is_active', true)->orderBy('name')->get();

        return [
            ['name' => 'academic_year_id', 'label' => 'Tahun Akademik', 'type' => 'select', 'value' => old('academic_year_id', $participant?->academic_year_id), 'required' => true, 'options' => $academicYears->map(fn (AcademicYear $year) => ['value' => $year->id, 'label' => $year->name])->all()],
            ['name' => 'study_program_id', 'label' => 'Program Studi', 'type' => 'select', 'value' => old('study_program_id', $participant?->study_program_id), 'required' => true, 'options' => $studyPrograms->map(fn (StudyProgram $program) => ['value' => $program->id, 'label' => $program->code.' - '.$program->name])->all()],
            ['name' => 'class_id', 'label' => 'Kelas', 'type' => 'select', 'value' => old('class_id', $participant?->class_id), 'required' => true, 'options' => $classes->map(fn (AcademicClass $class) => ['value' => $class->id, 'label' => ($class->studyProgram?->code ?? '-').' - '.$class->name])->all()],
            ['name' => 'course_id', 'label' => 'Mata Kuliah', 'type' => 'select', 'value' => old('course_id', $participant?->course_id), 'required' => true, 'options' => $courses->map(fn (Course $course) => ['value' => $course->id, 'label' => ($course->studyProgram?->code ?? '-').' - '.$course->name])->all()],
            ['name' => 'student_id', 'label' => 'Mahasiswa', 'type' => 'select', 'value' => old('student_id', $participant?->student_id), 'required' => true, 'options' => $students->map(fn (Student $student) => ['value' => $student->id, 'label' => $student->student_number.' - '.$student->name])->all()],
            ['name' => 'pegawai_id', 'label' => 'Pegawai', 'type' => 'select', 'value' => old('pegawai_id', $participant?->pegawai_id), 'required' => true, 'options' => $pegawai->map(fn (Pegawai $item) => ['value' => $item->id, 'label' => $item->employee_number.' - '.$item->name])->all()],
            ['name' => 'participant_status', 'label' => 'Status Peserta', 'type' => 'select', 'value' => old('participant_status', $participant?->participant_status ?? 1), 'required' => true, 'options' => [
                ['value' => 1, 'label' => 'Aktif'],
                ['value' => 0, 'label' => 'Nonaktif'],
                ['value' => 2, 'label' => 'Keluar'],
            ]],
            ['name' => 'notes', 'label' => 'Catatan', 'type' => 'textarea', 'value' => old('notes', $participant?->notes)],
        ];
    }

    private function assertRelatedStudyPrograms(array $data): void
    {
        $class = AcademicClass::with('studyProgram')->findOrFail($data['class_id']);
        $course = Course::with('studyProgram')->findOrFail($data['course_id']);
        $student = Student::with('studyProgram')->findOrFail($data['student_id']);

        abort_if(
            $class->study_program_id !== (int) $data['study_program_id'] ||
            $course->study_program_id !== (int) $data['study_program_id'] ||
            $student->study_program_id !== (int) $data['study_program_id'],
            422,
            'Program studi peserta kelas harus sama dengan kelas, mata kuliah, dan mahasiswa.'
        );
    }

    private function statusLabel(int $status): string
    {
        return match ($status) {
            0 => 'Nonaktif',
            2 => 'Keluar',
            default => 'Aktif',
        };
    }
}

