<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;

/**
 * @group Master Data - Roles
 *
 * Manage roles and their attached permissions.
 */
class RoleController extends Controller
{
    /**
     * List roles with attached permissions.
     */
    public function index(Request $request)
    {
        $roles = Role::with('permissions')->paginate((int) $request->get('per_page', 15));
        return response()->json(['success' => true, 'message' => 'OK', 'data' => RoleResource::collection($roles)]);
    }

    /**
     * Create a new role and optionally attach permissions.
     *
     * @example {"success":true,"message":"Role dibuat","data":{"id":3,"name":"operator","display_name":"Operator","permissions":[{"id":1,"name":"view-users"},{"id":2,"name":"create-users"}]}}
     */
    public function store(RoleStoreRequest $request)
    {
        $data = $request->validated();
        $permissionIds = $data['permission_ids'] ?? [];
        unset($data['permission_ids']);

        $role = Role::create($data);
        if (!empty($permissionIds)) {
            $role->permissions()->sync($permissionIds);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role dibuat',
            'data' => new RoleResource($role->load('permissions')),
        ], 201);
    }

    /**
     * Get a role with its permissions.
     */
    public function show($id)
    {
        $role = Role::with('permissions')->find($id);
        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'message' => 'OK', 'data' => new RoleResource($role)]);
    }

    /**
     * Update a role and sync its permissions.
     */
    public function update(RoleUpdateRequest $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role tidak ditemukan'], 404);
        }

        $data = $request->validated();
        $permissionIds = $data['permission_ids'] ?? null;
        unset($data['permission_ids']);

        $role->update($data);
        if (is_array($permissionIds)) {
            $role->permissions()->sync($permissionIds);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role diupdate',
            'data' => new RoleResource($role->fresh()->load('permissions')),
        ]);
    }

    /**
     * Delete a role and detach all relations.
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role tidak ditemukan'], 404);
        }

        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();

        return response()->json(['success' => true, 'message' => 'Role dihapus']);
    }
}
