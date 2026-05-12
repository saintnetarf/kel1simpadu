<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @group Master Data - Students
 */
class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::with('studyProgram')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where('student_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('studyProgram', fn ($programQuery) => $programQuery->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
            })
            ->orderBy('student_number')
            ->paginate((int) $request->get('per_page', 15));

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $students]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
            'student_number' => ['required', 'string', 'max:50', Rule::unique('students', 'student_number')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $student = Student::create($data);

        return response()->json(['success' => true, 'message' => 'Mahasiswa dibuat', 'data' => $student], 201);
    }

    public function show(Student $student)
    {
        return response()->json(['success' => true, 'message' => 'OK', 'data' => $student->load('studyProgram')]);
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
            'student_number' => ['required', 'string', 'max:50', Rule::unique('students', 'student_number')->ignore($student->id)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $student->update($data);

        return response()->json(['success' => true, 'message' => 'Mahasiswa diupdate', 'data' => $student->fresh()->load('studyProgram')]);
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json(['success' => true, 'message' => 'Mahasiswa dihapus']);
    }
}

