<?php

use App\Http\Controllers\TestController;
use App\Http\directions\Controllers\DirectionController;
use App\Http\directions\borderCrossings\Controllers\BorderCrossingController;
use App\Http\directions\borderCrossings\cameras\Controllers\CameraController;
use App\Http\directions\borderCrossings\reports\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthorizationAPI;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test/testMethod', [TestController::class, 'testMethod']);
Route::get('/directions/all', [DirectionController::class, 'getAll']);
Route::get('/directions/borderCrossing', [BorderCrossingController::class, 'getAll']);
Route::get('/directions/borderCrossing/cameras', [CameraController::class, 'getAll']);
Route::get('/directions/borderCrossing/reports/getLastReports', [ReportController::class, 'getLastReports']);
Route::get('/directions/borderCrossing/reports/getAll', [ReportController::class, 'getAll']);
Route::post('/directions/borderCrossing/reports/createReport', [ReportController::class, 'createReport']);

//Route::middleware('auth.api')->group(function () {
//    Route::get('/test/testMethod', [TestController::class, 'testMethod']);
//    Route::get('/directions/all', [DirectionController::class, 'getAll']);
//    Route::get('/directions/borderCrossing', [BorderCrossingController::class, 'getAll']);
//    Route::get('/directions/borderCrossing/cameras', [CameraController::class, 'getAll']);
//    Route::get('/directions/borderCrossing/reports/getLastReports', [ReportController::class, 'getLastReports']);
//    Route::get('/directions/borderCrossing/reports/getAll', [ReportController::class, 'getAll']);
//    Route::post('/directions/borderCrossing/reports/createReport', [ReportController::class, 'createReport']);
//});
