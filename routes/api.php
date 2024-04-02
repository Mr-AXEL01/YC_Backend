<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
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


//---------Authentification-------//
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout']);

//--------- organizer ----------//
Route::post('announcements/create', [AnnouncementController::class, 'store']);
Route::get('announcements', [AnnouncementController::class, 'index']);


//--------------- applications ----------------------//
Route::post('applications', [ApplicationController::class, 'store']);
Route::get('reviewApplications', [ApplicationController::class, 'index']);
Route::put('applications/{application}', [ApplicationController::class, 'manageApplications']);



