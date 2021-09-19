<?php

use App\Events\ProgressAddedEvent;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::resource('book', \App\Http\Controllers\BookController::class);
Route::resource('application', \App\Http\Controllers\ApplicationController::class,
    ['only' => ['index', 'store']]);
Route::get('application/free', [\App\Http\Controllers\ApplicationController::class, 'indexFreeApplication']);

Route::resource('group', \App\Http\Controllers\GroupController::class);
Route::post('check_group', [\App\Http\Controllers\GroupController::class, 'checkGroup']);

Route::get('socket', [\App\Http\Controllers\GroupController::class, 'socket']);
Route::get('/socket2', function () {
    broadcast(new ProgressAddedEvent(10));
});


Route::resource('girl', \App\Http\Controllers\GirlController::class);
Route::get('/girl/group/{id}', [\App\Http\Controllers\GirlController::class, 'getGirlFromGroup']);
Route::get('/girl', [\App\Http\Controllers\GirlController::class, 'getGirlNormal']);
Route::post('/girl/like', [\App\Http\Controllers\GirlController::class, 'like']);
Route::post('/girl/dislike', [\App\Http\Controllers\GirlController::class, 'dislike']);
Route::post('/girl/search', [\App\Http\Controllers\GirlController::class, 'searchGirls']);

Route::resource('/note', \App\Http\Controllers\NoteController::class);
