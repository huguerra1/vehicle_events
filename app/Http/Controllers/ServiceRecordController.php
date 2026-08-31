<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRecordRequest;
use App\Http\Requests\UpdateServiceRecordRequest;
use App\Models\ServiceRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServiceRecordController extends Controller
{
    public function index(Request $request, int $vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);

        if (! $vehicle || $vehicle->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $serviceRecords = ServiceRecord::where('vehicle_id', $vehicleId)->get();

        return response()->json($serviceRecords);
    }

    public function create(StoreServiceRecordRequest $request, int $vehicleId)
    {
        $vehicle = Vehicle::find($vehicleId);

        if (! $vehicle || $vehicle->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validated();
        $data['vehicle_id'] = $vehicleId;

        $serviceRecord = ServiceRecord::create($data);

        return response()->json($serviceRecord, Response::HTTP_CREATED);
    }

    public function show(Request $request, int $id)
    {
        $serviceRecord = ServiceRecord::find($id);

        if (! $serviceRecord || $serviceRecord->vehicle->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($serviceRecord);
    }

    public function update(UpdateServiceRecordRequest $request, int $id)
    {
        $serviceRecord = ServiceRecord::find($id);

        if (! $serviceRecord || $serviceRecord->vehicle->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $serviceRecord->update($request->validated());

        return response()->json($serviceRecord);
    }

    public function delete(Request $request, int $id)
    {
        $serviceRecord = ServiceRecord::find($id);

        if (! $serviceRecord || $serviceRecord->vehicle->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $serviceRecord->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
