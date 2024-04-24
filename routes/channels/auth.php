<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group([
    "middleware" => ["auth:sanctum"]
], function () {
    Route::get("profile", [AuthController::class, 'profile'] );
    Route::get("logout", [AuthController::class, 'logout'] );
});
