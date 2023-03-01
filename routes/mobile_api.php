<?php

use Illuminate\Http\Request;


Route::middleware( ['flow', 'cors'])->get('core/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' =>  ['flow', 'cors'], 'prefix' => 'core/user'], function () {
    Route::post('/register', 'CorePlatform\CoreUserAuthController@register');
    Route::post('login', 'CorePlatform\CoreUserAuthController@login');
    Route::post('/logout', 'CorePlatform\CoreUserAuthController@logout');
    Route::middleware( ['flow', 'cors', 'auth.core'])->get('/me', 'CorePlatform\CoreUserAuthController@me');
});

Route::group(['middleware' =>  ['flow', 'cors'], 'prefix' => 'core/customer'], function () {
    Route::post('/profile', 'CorePlatform\MobileAppController@get_user_profile');
    Route:: post('/details','CorePlatform\MobileAppController@get_customer_details');


});

Route::group(['middleware' =>  ['flow', 'cors'], 'prefix' => 'core/loan_appl/'], function () {   
    Route::post('/products', 'CorePlatform\LoanApplicationController@product_search');         
    Route::post('/apply', 'CorePlatform\LoanApplicationController@apply_loan');
    Route::post('/application', 'CorePlatform\LoanApplicationController@get_current_application');     
});

Route::group(['middleware' =>  ['flow', 'cors'], 'prefix' => 'core/loan/'], function () {   
    Route::post('/', 'CorePlatform\LoanController@get_loan');      
    Route::post('/product/summary','CorePlatform\LoanController@getproductsummary');
    Route::post('/loans', 'CorePlatform\LoanController@loan_search');    
    Route::post('/currentloan', 'CorePlatform\LoanController@current_loan_search');   
    Route::post('/getloanproduct', 'CorePlatform\LoanController@getloanproduct');    
   });


Route::group(['middleware' => ['flow', 'cors'], 'prefix' => 'core/agreement/'], function () {

    Route::post('/', 'CorePlatform\AgreementController@get_mobile_agreement');
    Route::post('/save', 'CorePlatform\AgreementController@save_agreement');
});

Route::group(['middleware' =>  ['flow', 'cors'], 'prefix' => 'core/common/'], function () {       
    Route::post('/markets', 'CorePlatform\CommonController@get_market_list');
});

 Route::group(['middleware' => ['flow', 'cors'] , 'prefix' => 'core/rel_mgr'], function () {
     Route:: post('/','CorePlatform\MobileAppController@get_rel_mgr');
});

Route::group(['middleware' => ['flow', 'cors'], 'prefix' => 'core/agreement'], function () {
    Route::post('/agreement', 'CorePlatform\AgreementController@getagreement');
});


Route::group(['middleware' =>  ['flow', 'cors'], 'prefix' => 'core/common/'], function () {   
    Route::post('/markets', 'CorePlatform\CommonController@get_market_list');
    Route::post('/data_prvdrs', 'CorePlatform\CommonController@get_data_prvdrs');
    Route::post('/customer_register', 'CorePlatform\CommonController@create_customer_register');
    Route::post('/register_otp', 'CorePlatform\CommonController@create_register_otp');
    });


