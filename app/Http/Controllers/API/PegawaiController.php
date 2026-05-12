<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $query = Pegawai::query();

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where('employee_number', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%")->orWhere('position', 'like', "%{$search}%");
        }

        $pegawai = $query->orderBy('employee_number')->paginate((int) $request->get('per_page', 15));

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $pegawai]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_number' => ['required', 'string', 'max:50', Rule::unique('pegawai', 'employee_number')],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'position' => ['nullable', 'string', 'max:150'],
            'is_active' => ['required', 'boolean'],
        ]);

        $p = Pegawai::create($data);

        return response()->json(['success' => true, 'message' => 'Pegawai dibuat', 'data' => $p], 201);
    }

    public function show(Pegawai $pegawai)
    {
        return response()->json(['success' => true, 'message' => 'OK', 'data' => $pegawai]);
    }

    public function update(Request $request, Pegawai $pegawai)
    {
        $data = $request->validate([
            'employee_number' => ['required', 'string', 'max:50', Rule::unique('pegawai', 'employee_number')->ignore($pegawai->id)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'position' => ['nullable', 'string', 'max:150'],
            'is_active' => ['required', 'boolean'],
        ]);

        $pegawai->update($data);

        return response()->json(['success' => true, 'message' => 'Pegawai diupdate', 'data' => $pegawai->fresh()]);
    }

    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();

        return response()->json(['success' => true, 'message' => 'Pegawai dihapus']);
    }
}
