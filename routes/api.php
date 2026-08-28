<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

//Rotas de autenticação

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {

    //Rotas de usuarios

    Route::get('/users', [UserController::class,'index']);

    Route::post('/users',[UserController::class,'create']);

    Route::get('/users/{id}',[UserController::class,'show']);

    Route::delete('/users/{id}',[UserController::class,'delete']);

    Route::put('/users/{id}',[UserController::class,'update']);

    //Rotas de veiculos


    Route::get('/veiculos', [VehicleController::class,'index']);

    Route::post('/veiculos',[VehicleController::class,'create']);

    Route::get('/veiculos/{id}',[VehicleController::class,'show']);

    Route::delete('/veiculos/{id}',[VehicleController::class,'delete']);    

    Route::put('/veiculos/{id}',[VehicleController::class,'update']);




});
