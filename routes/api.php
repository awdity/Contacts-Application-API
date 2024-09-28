<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;


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

Route::prefix('api/v1')->group(function () {
    Route::post('users', [AuthController::class, 'register']);
    Route::post('users/activate', [AuthController::class, 'activateUser']);
    Route::post('token/auth', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () { 
        Route::get('/contacts/export', [ContactController::class, 'exportContacts']);
        Route::post('/contacts/import', [ContactController::class, 'import']);
        Route::apiResource('contacts', ContactController::class);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    });
});
