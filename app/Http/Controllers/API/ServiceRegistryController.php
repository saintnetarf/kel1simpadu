<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRegistryStoreRequest;
use App\Http\Requests\ServiceRegistryUpdateRequest;
use App\Models\ServiceRegistry;
use Illuminate\Http\Request;

/**
 * @group Master Data - Service Registry
 *
 * Register internal and external services that integrate with Auth Center.
 */
class ServiceRegistryController extends Controller
{
    /**
     * List registered services.
     */
    public function index(Request $request)
    {
        $services = ServiceRegistry::orderBy('name')->paginate((int) $request->get('per_page', 15));

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $services]);
    }

    /**
     * Create a service registry record.
     *
     * @example {"success":true,"message":"Service registry dibuat","data":{"id":1,"name":"Academic Service","code":"academic-service","base_url":"https://api.example.com","status":"active"}}
     */
    public function store(ServiceRegistryStoreRequest $request)
    {
        $service = ServiceRegistry::create($request->validated());

        return response()->json(['success' => true, 'message' => 'Service registry dibuat', 'data' => $service], 201);
    }

    /**
     * Get a registered service by id.
     */
    public function show($id)
    {
        $service = ServiceRegistry::find($id);

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service registry tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'message' => 'OK', 'data' => $service]);
    }

    /**
     * Update a registered service.
     */
    public function update(ServiceRegistryUpdateRequest $request, $id)
    {
        $service = ServiceRegistry::find($id);

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service registry tidak ditemukan'], 404);
        }

        $service->update($request->validated());

        return response()->json(['success' => true, 'message' => 'Service registry diupdate', 'data' => $service->fresh()]);
    }

    /**
     * Delete a registered service.
     */
    public function destroy($id)
    {
        $service = ServiceRegistry::find($id);

        if (!$service) {
            return response()->json(['success' => false, 'message' => 'Service registry tidak ditemukan'], 404);
        }

        $service->delete();

        return response()->json(['success' => true, 'message' => 'Service registry dihapus']);
    }
}
