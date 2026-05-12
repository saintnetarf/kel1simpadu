<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceClientStoreRequest;
use App\Http\Requests\ServiceClientUpdateRequest;
use App\Interfaces\ServiceClientRepositoryInterface;
use Illuminate\Http\Request;

/**
 * @group Master Data - Service Clients
 *
 * Manage service client records used for microservice integrations.
 */
class ServiceClientController extends Controller
{
    public function __construct(protected ServiceClientRepositoryInterface $repo)
    {
    }

    /**
     * List service clients.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $data = $this->repo->paginate($request->only('search'), $perPage);
        return response()->json(['success'=>true,'message'=>'OK','data'=>$data]);
    }

    /**
     * Create a service client.
     */
    public function store(ServiceClientStoreRequest $request)
    {
        $sc = $this->repo->create($request->validated());
        return response()->json(['success'=>true,'message'=>'Service client dibuat','data'=>$sc],201);
    }

    /**
     * Get a service client by id.
     */
    public function show($id)
    {
        $sc = $this->repo->find($id);
        if (!$sc) return response()->json(['success'=>false,'message'=>'Not found'],404);
        return response()->json(['success'=>true,'message'=>'OK','data'=>$sc]);
    }

    /**
     * Update a service client.
     */
    public function update(ServiceClientUpdateRequest $request, $id)
    {
        $sc = $this->repo->find($id);
        if (!$sc) return response()->json(['success'=>false,'message'=>'Not found'],404);
        $sc = $this->repo->update($sc, $request->validated());
        return response()->json(['success'=>true,'message'=>'Service client diupdate','data'=>$sc]);
    }

    /**
     * Delete a service client.
     */
    public function destroy($id)
    {
        $sc = $this->repo->find($id);
        if (!$sc) return response()->json(['success'=>false,'message'=>'Not found'],404);
        $this->repo->delete($sc);
        return response()->json(['success'=>true,'message'=>'Service client dihapus']);
    }
}
