<?php

use App\Models\Group;
use Illuminate\Support\Facades\Route;

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

//Route::get('/', function () {
//    return view('welcome');
//});
//
//Auth::routes();
//
//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/get-code/{id}', [\App\Http\Controllers\ApplicationController::class, 'getCode']);
Route::get('/application/get-token', [\App\Http\Controllers\ApplicationController::class, 'getToken'])->name('get-token');

Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');

Route::post('/websocket-test', function() {
    broadcast(new \App\Events\NoteAdded('ky'));
});
