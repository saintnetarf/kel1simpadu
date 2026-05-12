<?php

namespace App\Http\Controllers\Web\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PegawaiController extends Controller
{
    public function index(Request $request): View
    {
        $search = (string) $request->string('search');

        $pegawai = Pegawai::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('employee_number', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            })
            ->orderBy('employee_number')
            ->paginate(10)
            ->withQueryString();

        return view('master-data.index', [
            'title' => 'Master Pegawai',
            'subtitle' => 'Kelola data pegawai untuk kebutuhan relasi peserta kelas.',
            'createUrl' => route('master-data.pegawai.create'),
            'canCreate' => Gate::allows('create', \App\Models\Pegawai::class),
            'headers' => ['NIP/NIK', 'Nama', 'Jabatan', 'Status'],
            'rows' => $pegawai->getCollection()->map(function (Pegawai $item) {
                return [
                    'values' => [
                        $item->employee_number,
                        $item->name,
                        $item->position ?? '-',
                        $item->is_active ? 'Aktif' : 'Nonaktif',
                    ],
                    'editUrl' => route('master-data.pegawai.edit', $item),
                    'deleteUrl' => route('master-data.pegawai.destroy', $item),
                ];
            })->all(),
            'paginator' => $pegawai,
            'search' => $search,
            'searchPlaceholder' => 'Cari NIP/NIK, nama, atau jabatan...',
        ]);
    }

    public function show(Pegawai $pegawai): View
    {
        $this->authorize('view', $pegawai);

        return view('master-data.show', [
            'title' => 'Detail Pegawai',
            'pegawai' => $pegawai,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Pegawai::class);

        return view('master-data.form', [
            'title' => 'Tambah Pegawai',
            'actionUrl' => route('master-data.pegawai.store'),
            'method' => 'POST',
            'cancelUrl' => route('master-data.pegawai.index'),
            'submitLabel' => 'Simpan',
            'fields' => $this->formFields(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Pegawai::class);

        $data = $request->validate($this->rules());

        Pegawai::create($data);

        return redirect()->route('master-data.pegawai.index')->with('success', 'Pegawai berhasil dibuat.');
    }

    public function edit(Pegawai $pegawai): View
    {
        $this->authorize('update', $pegawai);

        return view('master-data.form', [
            'title' => 'Edit Pegawai',
            'actionUrl' => route('master-data.pegawai.update', $pegawai),
            'method' => 'PUT',
            'cancelUrl' => route('master-data.pegawai.index'),
            'submitLabel' => 'Update',
            'fields' => $this->formFields($pegawai),
        ]);
    }

    public function update(Request $request, Pegawai $pegawai): RedirectResponse
    {
        $this->authorize('update', $pegawai);

        $data = $request->validate($this->rules($pegawai->id));

        $pegawai->update($data);

        return redirect()->route('master-data.pegawai.index')->with('success', 'Pegawai berhasil diupdate.');
    }

    public function destroy(Pegawai $pegawai): RedirectResponse
    {
        $this->authorize('delete', $pegawai);

        $pegawai->delete();

        return redirect()->route('master-data.pegawai.index')->with('success', 'Pegawai berhasil dihapus.');
    }

    private function rules(?int $ignoreId = null): array
    {
        return [
            'employee_number' => ['required', 'string', 'max:50', Rule::unique('pegawai', 'employee_number')->ignore($ignoreId)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'position' => ['nullable', 'string', 'max:150'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    private function formFields(?Pegawai $pegawai = null): array
    {
        return [
            ['name' => 'employee_number', 'label' => 'NIP/NIK', 'type' => 'text', 'value' => old('employee_number', $pegawai?->employee_number), 'required' => true],
            ['name' => 'name', 'label' => 'Nama Pegawai', 'type' => 'text', 'value' => old('name', $pegawai?->name), 'required' => true],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'value' => old('email', $pegawai?->email)],
            ['name' => 'phone', 'label' => 'No. HP', 'type' => 'text', 'value' => old('phone', $pegawai?->phone)],
            ['name' => 'position', 'label' => 'Jabatan', 'type' => 'text', 'value' => old('position', $pegawai?->position)],
            ['name' => 'is_active', 'label' => 'Status Aktif', 'type' => 'select', 'value' => old('is_active', $pegawai?->is_active ?? true), 'required' => true, 'options' => [
                ['value' => 1, 'label' => 'Aktif'],
                ['value' => 0, 'label' => 'Nonaktif'],
            ]],
        ];
    }
}
