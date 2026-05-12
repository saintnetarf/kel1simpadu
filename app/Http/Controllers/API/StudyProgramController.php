<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StudyProgram;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @group Master Data - Study Programs
 */
class StudyProgramController extends Controller
{
    public function index(Request $request)
    {
        $studyPrograms = StudyProgram::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('degree_level', 'like', "%{$search}%");
            })
            ->orderBy('code')
            ->paginate((int) $request->get('per_page', 15));

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $studyPrograms]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('study_programs', 'code')],
            'name' => ['required', 'string', 'max:255'],
            'degree_level' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $studyProgram = StudyProgram::create($data);

        return response()->json(['success' => true, 'message' => 'Program studi dibuat', 'data' => $studyProgram], 201);
    }

    public function show(StudyProgram $studyProgram)
    {
        return response()->json(['success' => true, 'message' => 'OK', 'data' => $studyProgram]);
    }

    public function update(Request $request, StudyProgram $studyProgram)
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('study_programs', 'code')->ignore($studyProgram->id)],
            'name' => ['required', 'string', 'max:255'],
            'degree_level' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ]);

        $studyProgram->update($data);

        return response()->json(['success' => true, 'message' => 'Program studi diupdate', 'data' => $studyProgram->fresh()]);
    }

    public function destroy(StudyProgram $studyProgram)
    {
        $studyProgram->delete();

        return response()->json(['success' => true, 'message' => 'Program studi dihapus']);
    }
}

