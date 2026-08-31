<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::search($request)->get();

        return response()->json($events);
    }

    public function show(Request $request, int $id)
    {
        $event = Event::find($id);

        if (! $event) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($event);
    }

    public function create(StoreEventRequest $request)
    {
        $event = new Event(['organizer_id' => $request->user()->id]);

        $this->save($event, $request->validated());

        return response()->json($event, Response::HTTP_CREATED);
    }

    public function update(UpdateEventRequest $request, int $id)
    {
        $event = Event::find($id);

        if (! $event || $event->organizer_id !== $request->user()->id) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $this->save($event, $request->validated());

        return response()->json($event);
    }

    public function delete(Request $request, int $id)
    {
        $event = Event::find($id);

        if (! $event || $event->organizer_id !== $request->user()->id) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $event->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function attachVehicle(Request $request, int $eventId, int $vehicleId)
    {
        $event = Event::find($eventId);
        $vehicle = Vehicle::find($vehicleId);

        if (! $event || ! $vehicle || $vehicle->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        if ($event->vehicles()->where('vehicle_id', $vehicleId)->exists()) {
            return response()->json(['message' => 'Veículo já inscrito neste evento.'], Response::HTTP_CONFLICT);
        }

        $event->vehicles()->attach($vehicleId, ['registration_date' => now()]);

        $vehicle = $event->vehicles()->where('vehicle_id', $vehicleId)->first();

        return response()->json($vehicle, Response::HTTP_CREATED);
    }

    public function detachVehicle(Request $request, int $eventId, int $vehicleId)
    {
        $event = Event::find($eventId);
        $vehicle = Vehicle::find($vehicleId);

        if (! $event || ! $vehicle) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $userId = $request->user()->id;

        if ($event->organizer_id !== $userId && $vehicle->user_id !== $userId) {
            return response()->json(['message' => 'Não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $event->vehicles()->detach($vehicleId);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function save(Event $event, array $data): Event
    {
        $event->fill($data);
        $event->save();

        return $event;
    }
}
