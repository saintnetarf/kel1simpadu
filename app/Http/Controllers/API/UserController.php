<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Http\Request;

/**
 * @group Master Data - Users
 *
 * Manage users that belong to the Auth Center and are assigned roles for access control.
 */
class UserController extends Controller
{
    public function __construct(protected UserRepositoryInterface $users)
    {
    }

    /**
     * List users with pagination and optional search.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $users = $this->users->paginate($request->only('search'), $perPage);
        return response()->json(['success' => true, 'message' => 'OK', 'data' => UserResource::collection($users)]);
    }

    /**
     * Create a new user and assign one or more roles.
     *
     * @example {"success":true,"message":"User dibuat","data":{"id":10,"name":"User Baru","email":"userbaru@poliban.ac.id","roles":[{"id":1,"name":"super_admin"}]}}
     */
    public function store(UserStoreRequest $request)
    {
        $data = $request->validated();
        $user = $this->users->create($data);
        return response()->json(['success' => true, 'message' => 'User dibuat', 'data' => new UserResource($user)], 201);
    }

    /**
     * Update an existing user and sync its roles.
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $user = $this->users->find($id);
        if (!$user) return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        $user = $this->users->update($user, $request->validated());
        return response()->json(['success' => true, 'message' => 'User diupdate', 'data' => new UserResource($user)]);
    }

    /**
     * Delete a user.
     */
    public function destroy($id)
    {
        $user = $this->users->find($id);
        if (!$user) return response()->json(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        $this->users->delete($user);
        return response()->json(['success' => true, 'message' => 'User dihapus']);
    }
}
