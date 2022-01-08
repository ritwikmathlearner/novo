<?php
use App\Http\Controllers\KoluserController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\KolSessionController;
use App\Http\Controllers\QuestionController;
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

Route::post('/feedbacks', [FeedbackController::class, 'save']);
Route::get('/export-feedback/{kol_session}', [FeedbackController::class, 'export']);

Route::get('/questions/{case}', [QuestionController::class, 'index']);
Route::post('/save-response', [QuestionController::class, 'response']);
Route::get('/get-graph-data/{question}', [QuestionController::class, 'getGraphData']);
Route::get('/export-question/{attendee}/{kol_session}', [QuestionController::class, 'export']);
Route::get('/validate-session/{kol_session:unique_code}', [KolSessionController::class, 'validateSession']);
Route::get('/issessionopen/{kol_session:unique_code}', [KolSessionController::class, 'isSessionOpen']);
Route::post('/attendee-login/{kol_session:unique_code}', [KolSessionController::class, 'loginAttendee']);
Route::post('/login-kol', [KoluserController::class, 'login']);
Route::post('/end-session', [KolSessionController::class, 'endSession']);