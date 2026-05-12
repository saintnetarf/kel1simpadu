<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @group Master Data - Courses
 */
class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::with('studyProgram')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhereHas('studyProgram', fn ($programQuery) => $programQuery->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
            })
            ->orderBy('code')
            ->paginate((int) $request->get('per_page', 15));

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $courses]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
            'code' => ['required', 'string', 'max:50', Rule::unique('courses', 'code')],
            'name' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'integer', 'min:0', 'max:20'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $course = Course::create($data);

        return response()->json(['success' => true, 'message' => 'Mata kuliah dibuat', 'data' => $course], 201);
    }

    public function show(Course $course)
    {
        return response()->json(['success' => true, 'message' => 'OK', 'data' => $course->load('studyProgram')]);
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
            'code' => ['required', 'string', 'max:50', Rule::unique('courses', 'code')->ignore($course->id)],
            'name' => ['required', 'string', 'max:255'],
            'credits' => ['required', 'integer', 'min:0', 'max:20'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $course->update($data);

        return response()->json(['success' => true, 'message' => 'Mata kuliah diupdate', 'data' => $course->fresh()->load('studyProgram')]);
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return response()->json(['success' => true, 'message' => 'Mata kuliah dihapus']);
    }
}

