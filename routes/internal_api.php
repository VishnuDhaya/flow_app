<?php

use Illuminate\Http\Request;


Route::group(['middleware' => ['flow', 'cors', 'auth.internal', 'throttle:500,1'], 'prefix' => 'admin/score_model'], function () {
    Route::post('/eligibility', 'Admin\DataScoreModelController@get_score_eligibility');
    Route::post('/cs_model_factor_info', 'Admin\DataScoreModelController@get_cs_model_factor_info');
    Route::post('/calculate_score_and_insert_csf_values', 'Admin\DataScoreModelController@calculate_score_and_insert_csf_values');
    Route::post('/scoring_model', 'Admin\DataScoreModelController@get_scoring_model');
});
Route::group(['middleware' =>  ['flow', 'cors', 'auth.internal'], 'prefix' => ''], function () {   
    Route::post('/lambda_status', 'PartnerController@update_lambda_status');
    Route::post('/internal_mail', 'PartnerController@send_internal_mail');
});
