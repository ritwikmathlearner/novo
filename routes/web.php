<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('admin', 'App\Http\Controllers\Admin\AdminController@index');
Route::resource('admin/roles', 'App\Http\Controllers\Admin\RolesController');
Route::resource('admin/permissions', 'App\Http\Controllers\Admin\PermissionsController');
//Route::resource('admin/users', 'App\Http\Controllers\Admin\UsersController');
Route::resource('admin/kolusers', 'App\Http\Controllers\Admin\KolUsersController');
Route::resource('admin/pages', 'App\Http\Controllers\Admin\PagesController');
Route::resource('admin/activitylogs', 'App\Http\Controllers\Admin\ActivityLogsController')->only([
    'index', 'show', 'destroy'
]);
Route::resource('admin/settings', 'App\Http\Controllers\Admin\SettingsController');
Route::get('admin/generator', ['uses' => '\Appzcoder\LaravelAdmin\Controllers\ProcessController@getGenerator']);
Route::post('admin/generator', ['uses' => '\Appzcoder\LaravelAdmin\Controllers\ProcessController@postGenerator']);
