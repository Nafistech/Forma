<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\SubmissiondataController;

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


//Abdelrhman - API routes for Auth
Route::controller(ApiAuthController::class)->group(function () {

    Route::post("login", "login");    // login the user to have an access token to access to all the routs

    Route::post("logout", "logout")->middleware('api_auth');   // log out the user using his access token
    Route::get("authorize", "userAuthorize")->middleware('api_auth');   // log out the user using his access token
});

//Abdelrhman - API routes for forms
Route::controller(FormController::class)->group(function () {
    Route::get('forms/','index')->middleware('api_auth');      // method get to restore all forms
    Route::get('forms/show/{form_id}', "show")->middleware('api_auth');  // method get to restore forms by userId
    Route::post('forms/store', "store")->middleware('api_auth');  // method post to store new forms ($request form_title , form_description , )
    Route::put('forms/{form_id}', "update")->middleware('api_auth');  // method put to update forms by its id (in header method put) , ($request form_title , form_description )
    Route::delete('forms/{form_id}', "destroy")->middleware('api_auth');  // method delete to delete form by its id  (in header method delete)
});


//Abdelrhman - API routes for fields
Route::controller(FieldController::class)->group(function () {
    Route::post('field/store', "store")->middleware('api_auth');  // method post to store new forms ($request form_title , form_description , )
    Route::put('field/{field_id}', "update")->middleware('api_auth');  // method put to update forms by its id (in header method put) , ($request form_title , form_description )
    Route::delete('field/{field_id}', "destroy")->middleware('api_auth');  // method delete to delete form by its id  (in header method delete)
});


Route::controller(SettingController::class)->group(function(){

    Route::post('settings/{form_id}', "store")->middleware('api_auth'); // method post to store setting by form id
    Route::put('settings/{form_id}', "update")->middleware('api_auth'); // method post to update setting by form')
});


Route::post('submissions/{form_id}', [SubmissionController::class, 'store']);

Route::post('submissiondata',[SubmissiondataController::class, 'store']);