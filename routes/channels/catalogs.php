<?php

use App\Http\Controllers\Api\CatalogController;
use Illuminate\Support\Facades\Route;

Route::post('/catalogs', [CatalogController::class, 'store']); //->middleware(['auth:api', 'permission:catalogs-crear-catalog']);
Route::put('/catalogs/{catalog}', [CatalogController::class, 'update'])/* ->middleware(['auth:api', 'permission:catalogs-actualizar-catalog']) */;
