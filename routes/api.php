<?php

use App\Http\Controllers\BusinessContactController;
use App\Http\Controllers\CategoryNameController;
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
Route::get('/makeCategories', [CategoryNameController::class, 'fillListings']);
Route::get('/getCategories', [CategoryNameController::class, 'getCatergories']);
Route::get('/fetchContacts', [BusinessContactController::class, 'getCategoryContacts']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
