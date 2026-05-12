<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\ClassParticipant;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;

/**
 * @group Master Data - Class Participants
 */
class ClassParticipantController extends Controller
{
    public function index(Request $request)
    {
        $participants = ClassParticipant::with(['academicYear', 'studyProgram', 'academicClass', 'course', 'student'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('name', 'like', "%{$search}%")->orWhere('student_number', 'like', "%{$search}%"))
                    ->orWhereHas('course', fn ($courseQuery) => $courseQuery->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"))
                    ->orWhereHas('academicClass', fn ($classQuery) => $classQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('academicYear', fn ($yearQuery) => $yearQuery->where('name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate((int) $request->get('per_page', 15));

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $participants]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'participant_status' => ['required', 'integer', 'in:0,1,2'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->assertRelatedStudyPrograms($data);

        $participant = ClassParticipant::create($data);

        return response()->json(['success' => true, 'message' => 'Peserta kelas dibuat', 'data' => $participant], 201);
    }

    public function show(ClassParticipant $classParticipant)
    {
        return response()->json(['success' => true, 'message' => 'OK', 'data' => $classParticipant->load(['academicYear', 'studyProgram', 'academicClass', 'course', 'student'])]);
    }

    public function update(Request $request, ClassParticipant $classParticipant)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'participant_status' => ['required', 'integer', 'in:0,1,2'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->assertRelatedStudyPrograms($data);

        $classParticipant->update($data);

        return response()->json(['success' => true, 'message' => 'Peserta kelas diupdate', 'data' => $classParticipant->fresh()->load(['academicYear', 'studyProgram', 'academicClass', 'course', 'student'])]);
    }

    public function destroy(ClassParticipant $classParticipant)
    {
        $classParticipant->delete();

        return response()->json(['success' => true, 'message' => 'Peserta kelas dihapus']);
    }

    private function assertRelatedStudyPrograms(array $data): void
    {
        $class = AcademicClass::findOrFail($data['class_id']);
        $course = Course::findOrFail($data['course_id']);
        $student = Student::findOrFail($data['student_id']);

        abort_if(
            $class->study_program_id !== (int) $data['study_program_id'] ||
            $course->study_program_id !== (int) $data['study_program_id'] ||
            $student->study_program_id !== (int) $data['study_program_id'],
            422,
            'Program studi peserta kelas harus sama dengan kelas, mata kuliah, dan mahasiswa.'
        );
    }
}

