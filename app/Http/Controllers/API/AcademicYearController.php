<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

/**
 * @group Master Data - Academic Years
 */
class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('start_year', 'like', "%{$search}%")
                    ->orWhere('end_year', 'like', "%{$search}%");
            })
            ->orderByDesc('is_active')
            ->orderByDesc('start_year')
            ->paginate((int) $request->get('per_page', 15));

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $academicYears]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_year' => ['required', 'integer', 'between:1900,2100'],
            'end_year' => ['required', 'integer', 'between:1900,2100', 'gte:start_year'],
            'is_active' => ['required', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $academicYear = AcademicYear::create($data);

        return response()->json(['success' => true, 'message' => 'Tahun akademik dibuat', 'data' => $academicYear], 201);
    }

    public function show(AcademicYear $academicYear)
    {
        return response()->json(['success' => true, 'message' => 'OK', 'data' => $academicYear]);
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_year' => ['required', 'integer', 'between:1900,2100'],
            'end_year' => ['required', 'integer', 'between:1900,2100', 'gte:start_year'],
            'is_active' => ['required', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        $academicYear->update($data);

        return response()->json(['success' => true, 'message' => 'Tahun akademik diupdate', 'data' => $academicYear->fresh()]);
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();

        return response()->json(['success' => true, 'message' => 'Tahun akademik dihapus']);
    }
}

