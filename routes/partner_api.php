<?php

use Illuminate\Http\Request;

Route::group(['middleware' =>  ['flow', 'cors'], 'prefix' => 'cca'], function () {   
    Route::post('/req_stmts', 'Partners\CCA\ChapChapController@req_cust_statement');
    Route::post('/mul_req_stmts', 'Partners\CCA\ChapChapController@req_mul_cust_statement');
    Route::post('/callback/stmt', 'Partners\CCA\ChapChapController@statement_req_callback');
});

Route::group(['middleware' =>  ['encrypt.partner', 'cors', 'auth.partner', 'validate.acc_number'], 'prefix' => ''], function () {   
    // Route::post('/float_adv_amounts', 'PartnerController@get_float_adv_amounts');
    // Route::post('/float_adv_durations', 'PartnerController@get_float_adv_durations'); 
    // Route::post('/float_adv_products', 'PartnerController@get_float_adv_products'); 
    Route::post('/elig_float_adv_products', 'PartnerController@get_elig_float_adv_products'); 
    Route::post('/apply_float_adv', 'PartnerController@apply_float_advance'); 
    Route::post('/float_adv_appl', 'PartnerController@get_float_adv_status'); 
    Route::post('/current_os_float_adv', 'PartnerController@get_current_os'); 
    Route::post('/float_adv_list', 'PartnerController@get_last_n_float_advances'); 
    Route::post('/notify_repayment', 'PartnerController@notify_repayment'); 
});

Route::group(['middleware' =>  ['flow', 'cors', 'auth.partner'], 'prefix' => 'acc_stmt'], function () { 
    Route::post('/req', 'PartnerController@req_acc_stmt');
});

Route::group(['middleware' =>  ['encrypt.partner', 'cors', 'auth.partner'], 'prefix' => ''], function () { 
    Route::post('/notify_new_acc_stmt', 'PartnerController@notify_new_acc_stmt')->name('notify_new_acc_stmt');
    Route::post('/notify_cust_interest', 'PartnerController@notify_cust_interest');
    Route::post('/check_lead_status', 'PartnerController@check_lead_status');
});

Route::group(['middleware' =>  ['encrypt.partner', 'cors', 'auth.partner'], 'prefix' => 'mock'], function () { 
    Route::post('/approval', 'PartnerController@mock_float_adv_approval');
    Route::post('/req_acc_stmt', 'PartnerController@mock_req_acc_stmt');
});
