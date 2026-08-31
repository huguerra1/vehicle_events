<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserProfileRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()->profile;

        if (! $profile) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($profile);
    }

    public function create(StoreUserProfileRequest $request)
    {
        if ($request->user()->profile) {
            return response()->json(['message' => 'Perfil já existe.'], Response::HTTP_CONFLICT);
        }

        $profile = $request->user()->profile()->create($request->validated());

        return response()->json($profile, Response::HTTP_CREATED);
    }

    public function update(UpdateUserProfileRequest $request)
    {
        $profile = $request->user()->profile;

        if (! $profile) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $profile->update($request->validated());

        return response()->json($profile);
    }

}
