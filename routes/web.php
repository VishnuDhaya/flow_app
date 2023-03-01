<?php
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


Route::post('/inv/password/reset','InvApp\InvResetPasswordController@reset')->name('password.inv_update');
Route::post('/password/reset','FlowApp\AppUserResetPasswordController@reset')->name('password.update');
//Route::get('/password/reset', 'InvApp\InvForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::get('/inv/password/reset/{token}','InvApp\InvResetPasswordController@showResetForm')->name('password.inv_reset');
Route::get('/password/reset/{token}','FlowApp\AppUserResetPasswordController@showResetForm')->name('password.reset');
Route::get('/inv/login', 'InvApp\InvAuthController@show')->name('login') ;
Route::post('/inv/login', 'InvApp\InvAuthController@loginInvestor') ;
Route::get('/inv/bank_acc', 'InvApp\InvAppController@bank_acc')->name('view_acc');
Route::post('/inv/bank_acc', 'InvApp\InvAppController@add_bank_acc')->name('add_acc');
Route::post('/inv/logout', 'InvApp\InvAuthController@logout')->name('logout') ;


Route::get('/inv/login/{driver}', 'InvApp\InvAuthController@redirectToProvider')
    ->name('login.provider')
    ->where('driver', implode('|', config('auth.socialite.drivers')));

Route::get('/inv/redirect/{driver}', 'InvApp\InvAuthController@loginWithProvider')->name('login.callback');

Route::group(['middleware' =>  ['auth.inv'], 'prefix' => '/inv'], function () {

    Route::get('/home', 'InvApp\InvAppController@home_view');
    Route::get('/bonds/{fund_code}', 'InvApp\InvAppController@bond_details_view');
    Route::get('/transactions/{fund_code}', 'InvApp\InvAppController@transactions_view');
    Route::get('/transactions', 'InvApp\InvAppController@transactions_view');


});


Route::get('/score_models/export/', 'ScriptController@scoreModelsReport');
Route::get('/cust_validation/export/', 'ScriptController@custValidationReport');
Route::get('/cust_status/export/', 'ScriptController@custStatusReport');
Route::get('/agreement/cust', 'Admin\AgreementController@cust_aggr');

Route::get('/privacy-policy', function () {
    return view('privacypolicy');
});

Route::any('/{any}', function () {
    return view('index');
})->where('any', '.*');

Route::get('/', function () {
    return view('index');
});

Route::get('/', function () {
    return view('index');
});








