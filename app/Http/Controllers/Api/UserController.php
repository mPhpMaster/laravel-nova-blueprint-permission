<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Resources\Json\JsonResource as BaseJsonResource;

/**
 *
 */
class UserController extends Controller
{
    /**
     * @var string|\Illuminate\Database\Eloquent\Model
     */
    public static $model = User::class;

    /**
     * @var string|BaseJsonResource
     */
    public static $responseResource = UserResource::class;

    /**
     * @param \App\Http\Requests\UserRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     * @throws \Throwable
     */
    public function index(UserRequest $request)
    {
        return $this->responseCollection(
            $this->search($request, 'first_name', withTrashed: $request->trashed)->paginate()
        );
    }

    /**
     * @param \App\Http\Requests\UserRequest $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     * @throws \Throwable
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();
        $validated[ 'roles' ] = $validated[ 'roles' ] ?? [];
        $roles = array_pull($validated, 'roles');


        $model = $this->createModel($request);

         if( $model && !empty($roles) ) {
            $model->syncRoles(...Role::find($roles));
        }

        return $this->responseResource($model, 201);
    }

    /**
     * @param \App\Http\Requests\UserRequest $request
     * @param \App\Models\User               $user
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function show(UserRequest $request, User $user)
    {
        return $this->responseResource($user);
    }

    /**
     * @param \App\Http\Requests\UserRequest $request
     * @param \App\Models\User               $user
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();
        $validated[ 'roles' ] = $validated[ 'roles' ] ?? [];
        $roles = array_pull($validated, 'roles');

        $model = $this->updateModel($request, $user);

         if( $model && !empty($roles) ) {
            $model->syncRoles(...Role::find($roles));
         }

        return $this->responseResource($model);
    }

    /**
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function destroy(UserRequest $request, User $user)
    {
        $aborts = [
            "User loggedIn [{$user->name}]!" => fn() => currentUserId() && currentUserId() === $user->id,
        ];

        return $this->responseResource($this->deleteModel($request, $user, $aborts));
    }

    /**
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function forceDestroy(UserRequest $request, User $user)
    {
        $aborts = [
            "User loggedIn [{$user->name}]!" => fn() => currentUserId() && currentUserId() === $user->id,
        ];

        return $this->responseResource(
            $this->forceDeleteModel($request, $user, $aborts)
        );
    }

    /**
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function restore(UserRequest $request, User $user)
    {
        return $this->responseResource($this->restoreModel($request, $user));
    }
}
