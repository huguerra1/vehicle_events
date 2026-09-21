<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaginationRequest;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VehicleController extends Controller
{
    public function index(PaginationRequest $request): JsonResponse
    {
        $vehicles = $request->user()
            ->vehicles()
            ->search($request)
            ->latest('id')
            ->paginate($request->perPage())
            ->withQueryString();

        return response()->json($vehicles);
    }

    public function create(StoreVehicleRequest $request)
    {
        $vehicle = $request->user()->vehicles()->create($request->validated());

        return response()->json($vehicle, Response::HTTP_CREATED);
    }

    public function show(Request $request, int $id)
    {
        $vehicle = Vehicle::find($id);

        if (! $vehicle || $vehicle->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($vehicle);
    }

    public function update(UpdateVehicleRequest $request, int $id)
    {
        $vehicle = Vehicle::find($id);

        if (! $vehicle || $vehicle->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $vehicle->update($request->validated());

        return response()->json($vehicle);
    }

    public function delete(Request $request, int $id)
    {
        $vehicle = Vehicle::find($id);

        if (! $vehicle || $vehicle->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $vehicle->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
