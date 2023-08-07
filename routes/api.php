<?php

use App\Http\Controllers\Api\ArApController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ImportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::get('/customers/public/{customer}', [CustomerController::class, 'showPublic']);
Route::get('/ar-ap/public', [ArApController::class, 'indexPublic']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::resource('companies', CompanyController::class, [
        'except' => ['edit', 'create'],
    ]);

    Route::get('companies/{company}/total-ar-ap', [CompanyController::class, 'totalArAp']);

    Route::resource('customers', CustomerController::class, [
        'except' => ['edit', 'create'],
    ]);

    Route::resource('ar-ap', ArApController::class, [
        'except' => ['edit', 'create'],
    ]);

    Route::post('import-buku-kas', [ImportController::class, 'bukuKas']);
});
