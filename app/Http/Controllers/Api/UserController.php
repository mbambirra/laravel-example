<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Lista todos os usuários.
     */
    public function index()
    {
        $users = User::paginate();

        return UserResource::collection($users);
    }

    /**
     * Cadastra um novo usuário.
     */
    public function store(StoreUpdateUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        return new UserResource($user);
    }

    /**
     * Lista os dados de um usuário, especificado por e-mail.
     */
    public function show(string $email)
    {
        $user = User::where('email', '=', $email)->first();
        if (!$user) {
            return response()->json(['message'=> 'Usuário não encontrado'], 404);
        }

        return new UserResource($user);
    }

    /**
     * Atualiza os dados de um usuário, especificado por ID.
     */
    public function update(StoreUpdateUserRequest $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message'=> 'Usuário não encontrado'], 404);
        }

        $data = $request->validated();

        if ($request->password) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return new UserResource($user);

    }

    /**
     * Deleta um usuário, especificado por ID.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message'=> 'Usuário não encontrado'], 404);
        }

        $user->delete();

        return response()->json([], 204);
    }
}
