<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @group Master Data - Classes
 */
class AcademicClassController extends Controller
{
    public function index(Request $request)
    {
        $classes = AcademicClass::with('studyProgram')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('studyProgram', fn ($programQuery) => $programQuery->where('name', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
            })
            ->orderBy('name')
            ->paginate((int) $request->get('per_page', 15));

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $classes]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('classes', 'name')->where(fn ($query) => $query->where('study_program_id', $request->integer('study_program_id'))),
            ],
            'level' => ['nullable', 'integer', 'min:1', 'max:20'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $class = AcademicClass::create($data);

        return response()->json(['success' => true, 'message' => 'Kelas dibuat', 'data' => $class], 201);
    }

    public function show(AcademicClass $class)
    {
        return response()->json(['success' => true, 'message' => 'OK', 'data' => $class->load('studyProgram')]);
    }

    public function update(Request $request, AcademicClass $class)
    {
        $data = $request->validate([
            'study_program_id' => ['required', 'integer', 'exists:study_programs,id'],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('classes', 'name')->where(fn ($query) => $query->where('study_program_id', $request->integer('study_program_id')))->ignore($class->id),
            ],
            'level' => ['nullable', 'integer', 'min:1', 'max:20'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $class->update($data);

        return response()->json(['success' => true, 'message' => 'Kelas diupdate', 'data' => $class->fresh()->load('studyProgram')]);
    }

    public function destroy(AcademicClass $class)
    {
        $class->delete();

        return response()->json(['success' => true, 'message' => 'Kelas dihapus']);
    }
}

