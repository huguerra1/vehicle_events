<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(User::all());
    }

    public function show(Request $request, int $id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($user);
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $user = User::find($id);

        if (! $user || $user->id !== $request->user()->id) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $user->update($request->validated());

        return response()->json($user);
    }

    public function delete(Request $request, int $id)
    {
        $user = User::find($id);

        if (! $user || $user->id !== $request->user()->id) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $user->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
