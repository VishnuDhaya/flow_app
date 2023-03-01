<?php

use Illuminate\Http\Request;


Route::group(['middleware' =>  ['flow', 'cors'], 'prefix' => '/user'], function () {

    Route::post('/login', 'Mobile\MobileAppAuthController@login');
    Route::post('/logout', 'Mobile\MobileAppAuthController@logout');
    Route::post('/init_check', 'Mobile\MobileAppAuthController@init_check');

});

Route::group(['middleware' =>['flow', 'cors','auth.mobile','activity']], function(){
    Route::post('/cust_reg/eval' , 'Mobile\RMController@cust_evaluation');
    Route::post('/cust_reg/checkin' , 'Mobile\RMController@cust_reg_checkin');
    Route::post('/cust_reg/checkout' , 'Mobile\RMController@cust_reg_checkout');
    Route::post('/cust_reg/sign_consent' , 'Mobile\RMController@sign_data_consent');
    Route::post('/fa/pre_appr', 'Mobile\RMController@allow_pre_approval');
    Route::post('/fa/remove_appr', 'Mobile\RMController@remove_pre_approval');
    Route::post('/lead/create','Mobile\RMController@create_lead');
    Route::post('/lead/update','Mobile\RMController@update_lead');
    Route::post('/lead/close','Mobile\RMController@close_lead');
    Route::post('/task/approval', 'Mobile\RMController@task_approval');
    Route::post('/cust/sign_agrmt', 'Mobile\RMController@sign_agreement');
    Route::post('/cust/update', 'Mobile\RMController@update_cust_profile');
});

Route::group(['middleware' =>  ['flow', 'cors','auth.mobile'], 'prefix' => '/cust'], function () {
    Route::post('/search', 'Mobile\RMController@borrower_search');
    Route::post('/view','Mobile\RMController@view_borrower');
    Route::post('/condone', 'Mobile\RMController@condone_customer');
    // Route::post('/sign_agrmt', 'Mobile\RMController@sign_agreement');
    Route::post('/delete_agrmt', 'Mobile\RMController@delete_agreement');
    Route::post('/agrmt_to_sign', 'Mobile\RMController@get_agrmt_to_sign');
    // Route::post('/update', 'Mobile\RMController@update_cust_profile');
    Route::post('/nearby_cust', 'Mobile\RMController@get_nearby_cust');
    Route::post('/criteria/{criteria}', 'Mobile\RMController@get_cust_by_criteria');


});


Route::group(['middleware' =>  ['flow', 'cors','auth.mobile'], 'prefix' => '/cust_reg'], function () {
    Route::post('/','Mobile\RMController@reg_cust');
    Route::post('/id_textract','Mobile\RMController@extract_text');
    Route::post('/dup_check', 'Mobile\RMController@dup_check_cust');
    Route::post('/address', 'Mobile\RMController@get_address_dropdown');
    Route::post('/partner_rms', 'Mobile\RMController@get_partner_rm_dropdown');
    // Route::middleware( ['flow', 'cors','auth.mobile','activity'])->post('/eval' , 'Mobile\RMController@cust_evaluation');
    Route::post('/view_consent' , 'Mobile\RMController@view_data_consent');
    // Route::middleware( ['flow', 'cors','auth.mobile','activity'])->post('/sign_consent' , 'Mobile\RMController@sign_data_consent');
    Route::post('/acc_prvdr' , 'Mobile\RMController@get_acc_prvdr');
    Route::post('/data_prvdr' , 'Mobile\RMController@list_data_prvdrs');
    // Route::middleware( ['flow', 'cors','auth.mobile','activity'])->post('/checkin' , 'Mobile\RMController@cust_reg_checkin');
    // Route::middleware( ['flow', 'cors','auth.mobile','activity'])->post('/checkout' , 'Mobile\RMController@cust_reg_checkout');
    Route::post('/send_otp' , 'Mobile\RMController@send_otp_to_mobile_num');
    Route::post('/send_kyc_otp' , 'Mobile\RMController@send_kyc_otp_to_mobile_num');
    Route::post('/verify_mob_num' , 'Mobile\RMController@verify_mobile_num_field');
    Route::post('/verify_kyc_mob_num' , 'Mobile\RMController@verify_kyc_mobile_num_field');
    Route::post('/update_thrd_pty_owner' , 'Mobile\RMController@update_third_party_owner');
    Route::post('/addl_num' , 'Mobile\RMController@update_addl_num_field');
});

    

    



Route::group(['middleware' =>  ['flow', 'cors','auth.mobile'], 'prefix' => '/common'], function () {
  Route::post('/stmt_upload','Mobile\RMController@stmt_upload');
  Route::post('/stmt_remove','Mobile\RMController@stmt_remove');
  Route::post('/upload','Mobile\RMController@file_upload');
  Route::post('/file_remove','Mobile\RMController@remove_file');
  Route::post('/file_process','Mobile\RMController@file_process');

});

Route::group(['middleware' =>  ['flow', 'cors'], 'prefix' => '/addr_config'], function () {
    Route::get('/{country_code}','Mobile\RMController@get_address_config');
});

Route::group(['middleware' =>  ['flow', 'cors','auth.mobile'], 'prefix' => '/schedule'], function () {
    Route::post('/calendar', 'Mobile\RMController@get_visit_schedules');
    Route::post('/','Mobile\RMController@create_schedule');
    Route::post('/cancel', 'Mobile\RMController@cancel_schedule');
    Route::post('/reschedule', 'Mobile\RMController@reschedule_visit');
    Route::post('/cal_days', 'Mobile\RMController@get_cal_days');
    
});

Route::group(['middleware' =>  ['flow', 'cors','auth.mobile','activity'], 'prefix' => '/visit'], function () {
    Route::post('/checkin', 'Mobile\RMController@checkin');
    Route::post('/checkout', 'Mobile\RMController@visit_checkout');
});

Route::group(['middleware' =>  ['flow', 'cors','auth.mobile'], 'prefix' => '/home'], function () {
    Route::post('/', 'Mobile\RMController@get_home_data');
    Route::post('/{month_num}', 'Mobile\RMController@get_dashboard_data');
    
});

Route::group(['middleware' =>  ['flow', 'cors','auth.mobile','activity'], 'prefix' => '/fa/action'], function () {
    Route::post('/approval', 'Mobile\RMController@loan_approval');
    Route::post('/upgrade', 'Mobile\RMController@fa_upgrade_approval');    
});

Route::group(['middleware' =>  ['flow', 'cors','auth.mobile'], 'prefix' => '/fa/criteria'], function () {
    Route::post('/{criteria}', 'Mobile\RMController@get_fas_by_criteria');
    
});


Route::group(['middleware' =>  ['flow', 'cors','auth.mobile'], 'prefix' => '/fa'], function () {
    Route::post('/last_fas', 'Mobile\RMController@get_last_n_fas');
    // Route::middleware( ['flow', 'cors','auth.mobile','activity'])->post('/pre_appr', 'Mobile\RMController@allow_pre_approval');
    // Route::middleware( ['flow', 'cors','auth.mobile','activity'])->post('/remove_appr', 'Mobile\RMController@remove_pre_approval');
    Route::post('/list_upg_reqs', 'Mobile\RMController@list_fa_upgrade_requests');
    Route::post('/{status}', 'Mobile\RMController@get_fas_by_status');

});

Route::group(['middleware' =>  ['flow', 'cors','auth.mobile'], 'prefix' => '/call_log'], function () {
    Route::post('/','Mobile\RMController@do_call_log');
    Route::post('/list', 'Mobile\RMController@list_call_logs');
    
});


Route::group(['middleware' =>  ['flow', 'cors','auth.mobile'], 'prefix' => '/lead'], function () {
    // Route::middleware( ['flow', 'cors','auth.mobile','activity'])->post('/create','Mobile\RMController@create_lead');
    Route::post('/search','Mobile\RMController@search_lead');
    Route::post('/view','Mobile\RMController@view_lead');
    // Route::middleware( ['flow', 'cors','auth.mobile','activity'])->post('/update','Mobile\RMController@update_lead');
    // Route::middleware( ['flow', 'cors','auth.mobile','activity'])->post('/close','Mobile\RMController@close_lead');
    Route::post('/products','Mobile\RMController@get_tf_products');
    Route::post('/update_status','Mobile\RMController@update_status');
    Route::post('/remarks/add','Mobile\RMController@add_remarks');


});


Route::group(['middleware' =>  ['flow', 'cors','auth.mobile','activity'], 'prefix' => '/cust_kyc'], function () {
    Route::post('/','Mobile\RMController@submit_kyc');   
});


Route::post('cust_a/validate_num', 'CustAppUserAuthController@validate_mobile_num');
Route::post('cust_a/ver_otp','CustAppUserAuthController@verify_otp');
Route::post('cust_a/set_pin','CustAppUserAuthController@set_cust_app_pin');
Route::post('cust_a/login','CustAppUserAuthController@login');

Route::group(['middleware' =>  ['flow', 'cors','auth.mobile'], 'prefix' => '/address'], function () {
    Route::post('/add_location','Mobile\RMController@add_location');   
});

Route::group(['middleware' =>  ['flow', 'cors', 'auth.mobile'], 'prefix' => '/task'], function () {
    Route::post('/list', 'Mobile\RMController@list_tasks');   
    // Route::post('/approval', 'Mobile\RMController@task_approval');   
    Route::post('/list_counts', 'Mobile\RMController@list_task_counts');   
 
 
});

Route::group(['middleware' =>  ['flow', 'cors', 'auth.mobile'], 'prefix' => '/rm'], function () {
    Route::post('/live_location', 'Mobile\RMController@rm_cur_location'); 
    Route::post('/punch_out', 'Mobile\RMController@punch_out');
    Route::post('/routes', 'Mobile\RMController@get_rm_routes');

});



