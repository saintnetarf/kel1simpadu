<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuAccessStoreRequest;
use App\Http\Requests\MenuAccessUpdateRequest;
use App\Models\MenuAccess;
use Illuminate\Http\Request;

/**
 * @group Master Data - Menu Access
 *
 * Manage the menu tree used by the application UI and access mapping.
 */
class MenuAccessController extends Controller
{
    /**
     * List menu access entries.
     */
    public function index(Request $request)
    {
        $menus = MenuAccess::with('children')->orderBy('sort_order')->paginate((int) $request->get('per_page', 15));

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $menus]);
    }

    /**
     * Create a menu access entry.
     */
    public function store(MenuAccessStoreRequest $request)
    {
        $menu = MenuAccess::create($request->validated());

        return response()->json(['success' => true, 'message' => 'Menu access dibuat', 'data' => $menu], 201);
    }

    /**
     * Get a menu access entry by id.
     */
    public function show($id)
    {
        $menu = MenuAccess::with('children')->find($id);

        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Menu access tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $menu]);
    }

    /**
     * Update a menu access entry.
     */
    public function update(MenuAccessUpdateRequest $request, $id)
    {
        $menu = MenuAccess::find($id);

        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Menu access tidak ditemukan'], 404);
        }

        $menu->update($request->validated());

        return response()->json(['success' => true, 'message' => 'Menu access diupdate', 'data' => $menu->fresh()]);
    }

    /**
     * Delete a menu access entry.
     */
    public function destroy($id)
    {
        $menu = MenuAccess::find($id);

        if (!$menu) {
            return response()->json(['success' => false, 'message' => 'Menu access tidak ditemukan'], 404);
        }

        $menu->delete();

        return response()->json(['success' => true, 'message' => 'Menu access dihapus']);
    }
}
