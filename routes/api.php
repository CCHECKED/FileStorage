<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/getHash', \App\Http\Controllers\API\HashController::class);

Route::post('/images/save', \App\Http\Controllers\API\Images\SaveController::class);

Route::post('/images', [\App\Http\Controllers\API\ImageController::class, 'store']);

Route::delete('/images/{l1}/{l2}/{l3}/{name}', [\App\Http\Controllers\API\ImageController::class, 'destroy']);
//    ->middleware(\App\Http\Middleware\CheckToken::class);
