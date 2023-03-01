<?php

Route::group(['middleware' => ['flow_cust', 'cors']], function () {

    Route::post('/validate_num', 'CustApp\CustAppUserAuthController@validate_mobile_num');
    Route::post('/ver_otp','CustApp\CustAppUserAuthController@verify_otp');
    Route::post('/set_pin','CustApp\CustAppUserAuthController@set_cust_app_pin');
    Route::post('/login','CustApp\CustAppUserAuthController@login');
    Route::post('/refresh','CustApp\CustAppUserAuthController@refresh');
    Route::post('/create_lead','CustApp\CustAppUserAuthController@create_lead');
    Route::post('/init_check','CustApp\CustAppUserAuthController@init_check');
    Route::post('/acc_prvdr' , 'CustApp\CustAppController@get_acc_prvdr');
    Route::post('/request' , 'CustApp\CustAppUserAuthController@request_cust_app_access');

});

Route::group(['middleware' => ['flow_cust','cors','auth.cust','transformer']], function () {
    Route::post('/recent_fas','CustApp\CustAppController@get_recent_fas');
    Route::post('/home','CustApp\CustAppController@get_home_fa');
    Route::post('/get_fa_detail','CustApp\CustAppController@get_fa_detail');
    Route::post('/cust_accs','CustApp\CustAppController@get_customer_accounts');
    Route::post('/repeat_fa','CustApp\CustAppController@repeat_fa');
    Route::post('/apply_fa','CustApp\CustAppController@apply_fa');
    Route::post('/list_prdcts','CustApp\CustAppController@list_products');
    Route::post('/confirm','CustApp\CustAppController@get_fa_appl_summary');
    Route::post('/repay_accs','CustApp\CustAppController@get_repayment_accs');
    Route::post('/cust_profile','CustApp\CustAppController@get_cust_profile');
    Route::post('/support','CustApp\CustAppController@get_support');
    Route::post('/aggr','CustApp\CustAppController@get_aggr_link');
    Route::post('/fa_upgrade','CustApp\CustAppController@request_fa_upgrade');
    Route::post('/get_faqs','CustApp\CustAppController@get_FAQs');
    Route::post('/pay_info','CustApp\CustAppController@get_pay_now_info');
    Route::post('/update_pay','CustApp\CustAppController@update_payment_status');
    Route::post('/prfle_updte','CustApp\CustAppController@request_profile_update');
    Route::post('/cust_fback', 'CustApp\CustAppController@cust_feedback');
    Route::post('/rm_visit_req', 'CustApp\CustAppController@rm_visit_request');
    Route::post('/rm_visit_list', 'CustApp\CustAppController@rm_visit_list');
    Route::post('/cust_complaint', 'CustApp\CustAppController@cust_complaint');
    Route::post('/view_cust_complaints','CustApp\CustAppController@view_cust_complaints'); 
    Route::post('/master_data/{data_key}','CustApp\CustAppController@get_master_data');
    Route::post('/cust_gps','CustApp\CustAppController@get_cust_gps');
});