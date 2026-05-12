<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiTokenStoreRequest;
use App\Http\Requests\ApiTokenUpdateRequest;
use App\Http\Resources\ApiTokenResource;
use App\Interfaces\ApiTokenRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

/**
 * @group Master Data - API Clients
 *
 * Manage API client tokens used by external services to authenticate against Auth Center.
 */
class ApiClientController extends Controller
{
    public function __construct(protected ApiTokenRepositoryInterface $repo)
    {
    }

    /**
     * List API clients.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        $data = $this->repo->paginate($request->only('user_id'), $perPage);
        return response()->json(['success'=>true,'message'=>'OK','data'=>ApiTokenResource::collection($data)]);
    }

    /**
     * Create an API client token.
     */
    public function store(ApiTokenStoreRequest $request)
    {
        $payload = $request->validated();
        $token = Str::random(80);
        $payload['token'] = hash('sha256', $token);
        $payload['abilities'] = $payload['abilities'] ?? ['*'];
        $payload['last_used_at'] = now();
        $api = $this->repo->create($payload);
        return response()->json(['success'=>true,'message'=>'API client token dibuat','data'=>['id'=>$api->id,'token'=>$token,'name'=>$api->name]] ,201);
    }

    /**
     * Get an API client by id.
     */
    public function show($id)
    {
        $api = $this->repo->find($id);
        if (!$api) return response()->json(['success'=>false,'message'=>'Not found'],404);
        return response()->json(['success'=>true,'message'=>'OK','data'=>new ApiTokenResource($api)]);
    }

    /**
     * Update an API client and optionally rotate its token.
     */
    public function update(ApiTokenUpdateRequest $request, $id)
    {
        $api = $this->repo->find($id);
        if (!$api) return response()->json(['success'=>false,'message'=>'Not found'],404);

        $data = $request->validated();
        $plainToken = null;
        if (array_key_exists('rotate_token', $data) && $data['rotate_token']) {
            $plainToken = Str::random(80);
            $data['token'] = hash('sha256', $plainToken);
            unset($data['rotate_token']);
        }

        $api->fill($data);
        $api->save();

        $responseData = ['api_client' => new ApiTokenResource($api->fresh('user'))];
        if ($plainToken) {
            $responseData['token'] = $plainToken;
        }

        return response()->json(['success'=>true,'message'=>'API client diupdate','data'=>$responseData]);
    }

    /**
     * Revoke an API client token.
     */
    public function destroy($id)
    {
        $api = $this->repo->find($id);
        if (!$api) return response()->json(['success'=>false,'message'=>'Not found'],404);
        $this->repo->delete($api);
        return response()->json(['success'=>true,'message'=>'Token dicabut']);
    }
}
