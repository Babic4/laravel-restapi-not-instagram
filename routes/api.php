<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Http\Controllers\UserController;
use Http\Controllers\FollowController;
use Http\Controllers\AuthController;
use Http\Controllers\GalleryController;
use Http\Controllers\ChatController;
use Http\Controllers\FilterController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('users', 'UserController@users');
// Route::get('users/{nickname}', 'UserController@userById');

Route::post('register', 'AuthController@register');
Route::post('signin', 'AuthController@signin');

//User
Route::apiResource('users', 'UserController');
Route::get('users/email/{email}', 'UserController@email');
Route::get('search-user', 'UserController@search');

//Follow
Route::get('follow/check', 'FollowController@check');
Route::get('follow/checkbox', 'FollowController@checkbox');
Route::get('count-followers', 'FollowController@countF');
Route::get('count-subscriptions', 'FollowController@countS');
Route::get('show-followers', 'FollowController@showF');
Route::get('show-subscriptions', 'FollowController@showS');
// Route::post('follow', 'FollowController@store');
// Route::delete('follow/{id}', 'FollowController@destroy');

//Gallery
Route::get('image/my', 'GalleryController@indexMy');
Route::get('image/others', 'GalleryController@indexOthers');
Route::post('image', 'GalleryController@store');

//Chat
Route::get('chat/{nickname}','ChatController@getConversations');
Route::get('chat/{convId}/messages','ChatController@getMessages');
Route::get('ischat','ChatController@isConversation');
Route::get('countm','ChatController@countM');
Route::put('chat/{convId}', 'ChatController@reviewMessages');
Route::post('chat', 'ChatController@addMessage');
Route::post('conv', 'ChatController@addConv');
Route::put('conv/{convId}', 'ChatController@addLastMessage');

//Filter
Route::post('filter', 'FilterController@filter');
Route::post('allFilter', 'FilterController@allFilter');

