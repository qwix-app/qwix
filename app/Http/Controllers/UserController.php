<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::where(
            'full_name',
            'LIKE',
            '%' . $request->query('search', '') . '%'
        )->paginate(10);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, User::getRules());

        $request->merge([
            'password' =>
                Hash::make($request->get('password'))
        ]);

        $user = User::create($request->all());

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function show(int $userId)
    {
        try {
            $user = User::findOrFail($userId);
    
            return new UserResource($user);
        } catch (ModelNotFoundException $e) {
            abort(404, "A user with id #$userId could not be found.");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $userId)
    {
        $this->validate($request, User::getRules());

        try {
            $user = User::findOrFail($userId);

            $user->fill($request->all());

            if ($user->isClean())
                abort(422, "No changes made to user type #$userId");

            $user->save();

            return new UserResource($user);
        } catch (ModelNotFoundException $e) {
            abort(404, "User with id #$userId could not be found.");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $userId)
    {
        try {
            $user = User::findOrFail($userId);

            $user->delete();

            return new UserResource($user);
        } catch (ModelNotFoundException $e) {
            abort(404, "User #$userId was not found.");
        }
    }
}
