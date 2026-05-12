<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebUserStoreRequest;
use App\Http\Requests\WebUserUpdateRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = (string) $request->get('search', '');

        $users = User::with('roles')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'search' => $search,
            'canManage' => $this->isSuperAdmin($request->user()),
        ]);
    }

    public function create(Request $request)
    {
        $this->ensureSuperAdmin($request->user());

        return view('users.create', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(WebUserStoreRequest $request)
    {
        $this->ensureSuperAdmin($request->user());

        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->roles()->sync($data['roles']);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(Request $request, User $user)
    {
        $this->ensureSuperAdmin($request->user());

        return view('users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(WebUserUpdateRequest $request, User $user)
    {
        $this->ensureSuperAdmin($request->user());

        $data = $request->validated();

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        $user->roles()->sync($data['roles']);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(Request $request, User $user)
    {
        $this->ensureSuperAdmin($request->user());

        if ($request->user()->id === $user->id) {
            return back()->withErrors(['delete' => 'Tidak bisa menghapus akun sendiri.']);
        }

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    private function ensureSuperAdmin(?User $user): void
    {
        abort_if(!$this->isSuperAdmin($user), 403, 'Akses khusus super_admin.');
    }

    private function isSuperAdmin(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $user->roles()->where('name', 'super_admin')->exists();
    }
}
