<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\GoogleSheetsController;
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

    Route::post('register',  'register');

    Route::post("login", "login");    // login the user to have an access token to access to all the routs

    Route::post("logout", "logout")->middleware('api_auth');   // log out the user using his access token
    Route::get("authorize", "userAuthorize")->middleware('api_auth');   // log out the user using his access token
    Route::post("user/profile/photo", "updateUserProfilePhoto")->middleware('api_auth');
    Route::post("user/update", "updateUser")->middleware('api_auth');
    Route::post("user/update/pw", "updateUserPassword")->middleware('api_auth');
});

//Abdelrhman - Kareem - API routes for forms
Route::controller(FormController::class)->group(function () {
    Route::get('forms/','index')->middleware('api_auth');      // method get to restore all forms
    Route::get('forms/show/{form_id}', "show");  // method get to restore forms by userId
    Route::get('forms/submissions/{form_id}', "showWithSubmissions")->middleware('api_auth');  // show form with all it's submissions
    Route::post('forms/store', "store")->middleware('api_auth');  // method post to store new forms ($request form_title , form_description , )
    Route::post('form/reset/{form_id}', "resetForm")->middleware('api_auth');
    Route::put('forms/{form_id}', "update")->middleware('api_auth');  // method put to update forms by its id (in header method put) , ($request form_title , form_description )
    Route::delete('forms/{form_id}', "destroy")->middleware('api_auth');  // method delete to delete form by its id  (in header method delete)
});


//Abdelrhman - Kareem - API routes for fields
Route::controller(FieldController::class)->group(function () {
    Route::post('field/store', "store")->middleware('api_auth');  // method post to store new forms ($request form_title , form_description , )
    Route::post('field/{field_id}', "update")->middleware('api_auth');  // method put to update forms by its id (in header method put) , ($request form_title , form_description )
    Route::delete('field/{field_id}', "destroy")->middleware('api_auth');  // method delete to delete form by its id  (in header method delete)
    Route::post('fields/reorder' , "reOrder")->middleware('api_auth');
});



Route::post('settings/{form_id}',[SettingController::class , "storeOrUpdate" ])->middleware('api_auth');

Route::post('submissions/{form_id}', [SubmissionController::class, 'store']);

Route::post('submissiondata',[SubmissiondataController::class, 'store']);

Route::post('submission/rate/{submission_id}',[SubmissionController::class, 'rateSubmission'])->middleware('api_auth');


Route::post('/files/{form_id}', [FileController::class, 'store']);
Route::post('/create-google-sheet/{form_id}', [GoogleSheetsController::class, 'createNewSpreadsheet']);
Route::post('/googleSheet/grantPermission/{documentId}', [GoogleSheetsController::class, 'grantPermission']);
Route::post('/appendSheet/{form_id}', [GoogleSheetsController::class, 'appendSheet']);
//Route::apiResource('files', FileController::class);

Route::get("login/google",[LoginController::class,'redirectGoogle'])->name('login.google');
Route::get("login/google/callback",[LoginController::class,'redirectGoogleCallback']);


