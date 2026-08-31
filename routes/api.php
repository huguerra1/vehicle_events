<?php

use App\Enums\ServiceType;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ServiceRecordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

//Rotas de autenticação

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

//Rota pública de tipos de serviço

Route::get('/service-types', function () {
    return response()->json(ServiceType::options());
});

Route::middleware(['auth:api'])->group(function () {

    //Rotas de usuarios

    Route::get('/users', [UserController::class,'index']);

    Route::get('/users/{id}',[UserController::class,'show']);

    Route::delete('/users/{id}',[UserController::class,'delete']);

    Route::put('/users/{id}',[UserController::class,'update']);

    //Rotas de perfil

    Route::get('/profile', [UserProfileController::class, 'show']);

    Route::post('/profile', [UserProfileController::class, 'create']);

    Route::put('/profile', [UserProfileController::class, 'update']);

    //Rotas de veiculos


    Route::get('/vehicles', [VehicleController::class,'index']);

    Route::post('/vehicles',[VehicleController::class,'create']);

    Route::get('/vehicles/{id}',[VehicleController::class,'show']);

    Route::delete('/vehicles/{id}',[VehicleController::class,'delete']);

    Route::put('/vehicles/{id}',[VehicleController::class,'update']);


    //Rotas de serviços

    Route::get('/vehicles/{vehicleId}/service-records', [ServiceRecordController::class, 'index']);

    Route::post('/vehicles/{vehicleId}/service-records', [ServiceRecordController::class, 'create']);

    Route::get('/service-records/{id}', [ServiceRecordController::class, 'show']);

    Route::put('/service-records/{id}', [ServiceRecordController::class, 'update']);

    Route::delete('/service-records/{id}', [ServiceRecordController::class, 'delete']);

    //Rotas de eventos

    Route::get('/events', [EventController::class, 'index']);

    Route::post('/events', [EventController::class, 'create']);

    Route::get('/events/{id}', [EventController::class, 'show']);

    Route::put('/events/{id}', [EventController::class, 'update']);

    Route::delete('/events/{id}', [EventController::class, 'delete']);

    Route::post('/events/{eventId}/vehicles/{vehicleId}', [EventController::class, 'attachVehicle']);

    Route::delete('/events/{eventId}/vehicles/{vehicleId}', [EventController::class, 'detachVehicle']);

});
