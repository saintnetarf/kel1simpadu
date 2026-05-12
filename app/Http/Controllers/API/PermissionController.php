<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionStoreRequest;
use App\Http\Requests\PermissionUpdateRequest;
use App\Models\Permission;
use Illuminate\Http\Request;

/**
 * @group Master Data - Permissions
 *
 * Manage API permissions that can be attached to roles.
 */
class PermissionController extends Controller
{
    /**
     * List permissions with pagination.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $permissions = Permission::query()->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => $permissions,
        ]);
    }

    /**
     * Create a new permission.
     */
    public function store(PermissionStoreRequest $request)
    {
        $permission = Permission::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Permission dibuat',
            'data' => $permission,
        ], 201);
    }

    /**
     * Get a permission by id.
     */
    public function show($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['success' => false, 'message' => 'Permission tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $permission]);
    }

    /**
     * Update a permission.
     */
    public function update(PermissionUpdateRequest $request, $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['success' => false, 'message' => 'Permission tidak ditemukan'], 404);
        }

        $permission->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Permission diupdate',
            'data' => $permission->fresh(),
        ]);
    }

    /**
     * Delete a permission.
     */
    public function destroy($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['success' => false, 'message' => 'Permission tidak ditemukan'], 404);
        }

        $permission->delete();

        return response()->json(['success' => true, 'message' => 'Permission dihapus']);
    }
}
