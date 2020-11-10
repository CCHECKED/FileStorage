<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('app');
});

Route::get('/storage/images/{width}/{height}/{image}', \App\Http\Controllers\ImageController::class)
    ->where('width', '[0-9]+')
    ->where('height', '[0-9]+')
    ->where('image', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.\w+');

Route::get('/test/{filename}', \App\Http\Controllers\TestController::class)
    ->where('filename', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.\w+');

//Route::get('/testcors', function () {
//    return view('test');
//});
