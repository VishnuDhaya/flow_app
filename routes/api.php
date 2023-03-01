<?php

use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Http\Request;

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
   
   Route::middleware('auth.app')->get('app/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['flow', 'cors', 'auth.app'])->post('app/touch', function (Request $request) {


    return [
                    'status' => 'success',
                    'status_code' =>'200',
                    'message' => "TOUCHED",
                    'server_time_ui' => datetime_ui(),
                    'server_time_db' => date('Y-m-d H:i:s')
                ];
});



Route::middleware(['flow', 'cors'])
          ->post('app/user/master_data', 'FlowApp\AppUserAuthController@get_master_data');


Route::group(['middleware' =>  ['flow', 'cors'], 'prefix' => 'app/user'], function () {

    Route::post('/register', 'FlowApp\AppUserAuthController@register');

    Route::post('/login', 'FlowApp\AppUserAuthController@login');

    Route::post('/logout', 'FlowApp\AppUserAuthController@logout');
    
    //Route::middleware('auth:app')->post('/master_data', 'FlowApp\AppUserAuthController@get_master_data');

    Route::middleware('auth:app')->get('/me', 'FlowApp\AppUserAuthController@me');


});


Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/master_data'], function () {

    Route::post('/create', 'Admin\MasterDataController@create_master_data'); 

    //Route::post('/key/all_data_keys', 'Admin\MasterDataController@get_all_data_keys');  

    Route::post('/key/new_data_keys', 'Admin\MasterDataController@get_new_data_keys');  


    Route::post('/key/create', 'Admin\MasterDataController@create_data_key');      

    Route::post('/key/list', 'Admin\MasterDataController@list_data_key');      

    Route::post('/key/status', 'Admin\MasterDataController@update_data_key_status');

    //Route::post('/key/parent_data_codes', 'Admin\MasterDataController@get_parent_data_codes');

    //Route::post('/score_models/list', 'Admin\MasterDataController@get_cs_model_code'); 

    Route::post('/score_factors/list', 'Admin\MasterDataController@get_score_factors');

});



Route::group(['middleware' => ['flow', 'cors' , 'auth.app'], 'prefix' => 'admin/market'], function () {

    Route::post('/', 'Admin\MarketController@create');  

    Route::post('/list', 'Admin\MarketController@list');

    Route::post('/view', 'Admin\MarketController@view');

    Route::post('/update', 'Admin\MarketController@update');

});


Route::group(['middleware' =>  ['flow', 'cors' , 'auth.app'], 'prefix' => 'app/common'], function () {

    Route::post('/master_data', 'CommonController@get_master_data');  

    Route::post('/country', 'CommonController@get_country_list'); 

    Route::post('/currency', 'CommonController@get_currency_list'); 

    Route::post('/currency_code', 'CommonController@get_currency_code'); 

    Route::post('/currency_by_market_id', 'CommonController@get_currency_by_market_id');

    Route::post('/loan_search_criteria', 'CommonController@get_loan_search_criteria');

    Route::post('/approvers', 'FlowApp\LoanApplicationController@list_approvers');

    Route::post('/priv_users', 'CommonController@list_users_by_priv');

    Route::post('/disbursers', 'FlowApp\LoanController@list_disbursers');

    Route::post('customer_accounts','CommonController@get_customer_accounts');

    Route::post('lender_accounts','CommonController@get_lender_accounts');

    Route::post('/dropdown', 'Admin\AddressInfoController@get_dropdown');

   Route::post('/key/parent_data_codes', 'Admin\MasterDataController@get_parent_data_codes');

   Route::post('/list','Admin\AccProviderController@list');

   Route::post('/key/all_data_keys', 'Admin\MasterDataController@get_all_data_keys');

   Route::post('/sms', 'CommonController@send_sms'); 

   Route::post('/update', 'CommonController@update_status');

   

   //Route::post('getcustomer','CommonController@create_customer');

    //Route::post('/all_users','FlowApp\LoanController@get_name_list'); 

    Route::post('/app_users','CommonController@get_app_users');

    //Route::post('/view_aggr', 'Admin\BorrowerController@view_agreement');
    //Route::post('account_txn_type','CommonController@get_acc_txn_type');

    Route::post('/audit_history', 'CommonController@get_record_audits');
    Route::post('/product_group', 'Admin\AgreementController@get_product_group');

    Route::post('/loan_prov', 'CommonController@get_loan_prov');

    Route::post('/loan_prov_year', 'CommonController@get_loan_prov_year');

    Route::post('/stmt_imports', 'CommonController@list_stmt_imports');
    
    Route::post('/stmt_imports/search', 'CommonController@search_stmt_imports');


});

Route::group(['middleware' =>  ['flow', 'cors' , 'auth.app'], 'prefix' => 'admin/file'], function () {

   Route::post('upload','Admin\FileController@upload_file');

   Route::post('remove','Admin\FileController@remove_file'); 

    Route::post('rel_path','Admin\FileController@get_file_rel_path'); 

    Route::post('/id_textract','Admin\FileController@extract_text');


});



Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/data_provider'], function () {

    Route::post('/', 'Admin\DataProviderController@create');  

    Route::post('/list', 'Admin\DataProviderController@list');

    Route::post('/name_list', 'Admin\DataProviderController@get_name_list');
    
    Route::post('/view', 'Admin\DataProviderController@view');

    Route::post('/update', 'Admin\DataProviderController@update');

    Route::post('/cca/payments', 'Admin\DataProviderController@list_cca_payments');

});


Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/borrower'], function () {
    Route::post('/', 'Admin\BorrowerController@create');  
    //Route::post('/list', 'Admin\BorrowerController@list');
    Route::post('/third_party_details','Admin\BorrowerController@get_third_party_details');
    Route::post('/view', 'Admin\BorrowerController@view');
    Route::post('/update', 'Admin\BorrowerController@update');
    Route::post('/account', 'Admin\BorrowerController@add_account');
    Route::post('/reference', 'Admin\BorrowerController@add_reference');   
    Route::post('/lender', 'Admin\BorrowerController@add_lender');
    Route::post('/search','Admin\BorrowerController@borrower_search');
    Route::post('/bring_to_probation', 'Admin\BorrowerController@bring_to_probation');
    Route::post('/update_status', 'Admin\BorrowerController@update_status');
    Route::post('/validate', 'Admin\BorrowerController@validate_customer');
    Route::post('/profile', 'Admin\BorrowerController@get_borrower_profile');
    Route::post('/close', 'Admin\BorrowerController@close_profile');
    Route::post('/app_access', 'Admin\BorrowerController@set_cust_app_access');
    Route::post('/manual_capture', 'Admin\BorrowerController@allow_manual_capture');
    Route::post('/list_complaints','CustApp\CustAppController@list_complaints');
    Route::post('/resolved_complaints','CustApp\CustAppController@resolved_complaints');
    Route::post('/view_cust_complaint','CustApp\CustAppController@view_customer_complaints');
  
   });


  Route::group(['middleware' => ['flow', 'cors', 'auth.app'] , 'prefix' => 'admin/rel_mgr'], function () {

    Route::post('/', 'Admin\RelationshipManagerController@create');  

    Route::post('/list', 'Admin\RelationshipManagerController@list');

    Route::post('/name_list', 'Admin\RelationshipManagerController@get_name_list');
    
    Route::post('/view', 'Admin\RelationshipManagerController@view');

    Route::post('/update', 'Admin\RelationshipManagerController@update');

    Route::post('/partner_rms', 'Admin\RelationshipManagerController@get_partner_rm_dropdown');

});

Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/lender'], function () {

    Route::post('/', 'Admin\LenderController@create');  

    Route::post('/list', 'Admin\LenderController@list');

    Route::post('/name_list', 'Admin\LenderController@get_name_list');
    
    Route::post('/view', 'Admin\LenderController@view');

    Route::post('/update', 'Admin\LenderController@update');

    Route::post('/account', 'Admin\LenderController@add_account');

    Route::post('/account/stmt_search','Admin\LenderController@get_acc_stmts');
    
    Route::post('/acc_stmt/add','Admin\LenderController@add_acc_stmts');


});


Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/'], function () {
    Route::post('address/update', 'Admin\AddressInfoController@update');
    Route::post('person/update', 'Admin\PersonController@update');
    Route::post('person/create', 'Admin\PersonController@create_person');
    Route::post('person/view', 'Admin\PersonController@view');
    Route::post('person/list', 'Admin\PersonController@list');
    Route::post('org/update', 'Admin\OrgController@update');
    Route::post('/org/name_list', 'Admin\OrgController@get_name_list');
    Route::post('/org/org_details', 'Admin\OrgController@get_org_details');
    

});


Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/field_visits'], function () {
    Route::post('/search', 'Admin\FieldVisitController@list_field_visits');
    Route::post('/checkin', 'Admin\FieldVisitController@checkin');
    Route::post('/checkout', 'Admin\FieldVisitController@checkout');
    Route::post('/validate', 'Admin\FieldVisitController@check_last_visit');
    Route::post('/details', 'Admin\FieldVisitController@get_field_visit_details');
    Route::post('/reg_schedule','Admin\FieldVisitController@create_reg_schedule');
    Route::post('/force_checkin','Admin\FieldVisitController@allow_force_checkin');

    
});

Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/lead'], function () {
    Route::post('/search', 'Admin\LeadController@search_lead');
    Route::post('/create', 'Admin\LeadController@create_lead');
    Route::post('/list', 'Admin\LeadController@list_leads');
    Route::post('/view', 'Admin\LeadController@view_lead');
    Route::post('/update', 'Admin\LeadController@update');
    Route::post('/delete', 'Admin\LeadController@delete_lead');
    Route::post('/close', 'Admin\LeadController@close_lead');
    Route::post('/allow_capture', 'Admin\LeadController@allow_manual_capture');
    Route::post('/audited_by', 'Admin\LeadController@update_audited_by');
    Route::post('/remarks/view', 'Admin\LeadController@view_remarks');
    Route::post('/remarks/add', 'Admin\LeadController@add_remarks');
    Route::post('/stmt_upload', 'Admin\LeadController@stmt_upload');
    Route::post('/stmt_remove', 'Admin\LeadController@stmt_remove');
    Route::post('/file_process', 'Admin\LeadController@file_process');
    Route::post('/send_kyc_otp', 'Admin\LeadController@send_kyc_otp_mobile_num');
    Route::post('/call_log', 'Admin\LeadController@submit_call_log');
    Route::post('/reject_call_log', 'Admin\LeadController@reject_call_log');

    Route::post('/submit_kyc', 'Admin\LeadController@submit_kyc_for_audit');

});

Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/auditor'], function () {
    Route::post('/name_list', 'Admin\LeadController@get_auditor_name_list');
    Route::post('/assign',  'Admin\LeadController@assign_auditor');
});


Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'app/task'], function () {
    Route::post('/create', 'FlowApp\TaskController@create_task');
    Route::post('/list', 'FlowApp\TaskController@list_tasks');  
    Route::post('/approval', 'FlowApp\TaskController@task_approval');    
});


Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/pre_appr'], function () {

    Route::post('/list', 'Admin\BorrowerController@list_pre_appr_customers');
    Route::post('/remove', 'Admin\BorrowerController@remove_pre_approval');



});

Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/call_log'], function () {
    Route::post('/start', 'Admin\CallLogController@start_call_log');
    Route::post('/complete', 'Admin\CallLogController@complete_call_log');
    Route::post('/search', 'Admin\CallLogController@list_call_logs');
    Route::post('/cancel', 'Admin\CallLogController@cancel_call_log');
    Route::post('/details', 'Admin\CallLogController@get_call_log_details');
    Route::post('/cs_devices', 'Admin\CallLogController@get_cs_rosters');
    Route::post('/update_cs_status', 'Admin\CallLogController@update_cs_status');



});
    
Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/acc_prvdr'], function () {
    
    Route::post('/','Admin\AccProviderController@create');
    Route::post('/list', 'Admin\AccProviderController@list');
    Route::post('/name_list', 'Admin\AccProviderController@get_name_list');
    Route::post('/acc_types','Admin\AccProviderController@update_acc_types');
    //Route::post('/list','Admin\AccProviderController@list');
    Route::post('/name','Admin\AccProviderController@get_acc_prvdr_name');

}); 


Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/loan_product'], function () {
    Route::post('/', 'Admin\LoanProductController@create');   
    Route::post('/list', 'Admin\LoanProductController@list');    
    Route::post('/view', 'Admin\LoanProductController@view');    
    Route::post('/update', 'Admin\LoanProductController@update');    
    
});






Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'app/loan_appl/'], function () {   

    Route::post('/products', 'FlowApp\LoanApplicationController@product_search'); 
    
    Route::post('/req_upgrade', 'FlowApp\LoanApplicationController@request_fa_upgrade'); 

    Route::post('/req_upgrade_status_web', 'FlowApp\LoanApplicationController@request_fa_upgrade_status_web'); 

    Route::post('/apply', 'FlowApp\LoanApplicationController@apply_loan');  

    //Route::post('/lender/accounts', 'FlowApp\LoanApplicationController@get_lender_accounts'); 

    Route::post('/applications', 'FlowApp\LoanApplicationController@loan_appl_search'); 

    Route::post('/application', 'FlowApp\LoanApplicationController@get_application');
    
    Route::post('/approval', 'FlowApp\LoanApplicationController@approval');

    Route::post('validate','FlowApp\LoanApplicationController@validate_appl');
         
});

Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'app/loan/'], function () {

    Route::post('/', 'FlowApp\LoanController@get_loan'); 

    Route::post('/loans', 'FlowApp\LoanController@loan_search');

    Route::post('/held_loans', 'FlowApp\LoanController@held_loan_search');
    
    Route::post('/cancel', 'FlowApp\LoanController@cancel_loan');

    Route::post('/approval', 'FlowApp\LoanApplicationController@approval');
    
    Route::post('/resend_otp', 'CommonController@resend_otp');

    Route::post('/get_disb_attempt', 'FlowApp\LoanController@get_disbursal_attempt');

    Route::post('/change_disb_status', 'FlowApp\LoanController@change_disbursal_status');
    
    Route::post('/retry_disb', 'FlowApp\LoanController@retry_disbursal');

    Route::post('/bypass_cust_conf', 'FlowApp\LoanController@bypass_cust_confirm');

    Route::post('/create_recovery', 'FlowApp\LoanRecoveryController@create_recovery_request');

    Route::post('/capture_recovery', 'FlowApp\LoanRecoveryController@capture_recovery');

    Route::post('/check_ong_rec', 'FlowApp\LoanRecoveryController@check_ongoing_recovery');

    Route::post('/cancel_ong_rec', 'FlowApp\LoanRecoveryController@cancel_ongoing_recovery');

    Route::post('/list_rec', 'FlowApp\LoanRecoveryController@list_recoveries');

    Route::post('/repeat_fa', 'FlowApp\LoanApplicationController@repeat_fa');

    Route::post('/list','FlowApp\LoanController@list');

    Route::post('/release','FlowApp\LoanController@release_loan');

    Route::post('/comment','FlowApp\LoanController@create_comment');

    Route::post('/payment_summary','FlowApp\LoanController@get_payment_summary');
    Route::post('/capture_payment','FlowApp\LoanController@create_capture_payment');
    Route::post('/unlink_payment','FlowApp\LoanController@create_unlink_payment');

    Route::post('/comments/list','FlowApp\LoanController@list_comments');

    Route::post('/comment/assign','FlowApp\LoanController@assign_list');

    Route::post('/allow_pp','FlowApp\LoanController@allow_partial_payment');
    Route::post('/check_txn_id','FlowApp\LoanController@check_txn_id_exists');
    Route::post('/waive','FlowApp\LoanController@update_waiver');
   
    Route::post('/capture_excess','FlowApp\LoanController@capture_excess_reversal');



    Route::post('/remove_payment','FlowApp\LoanController@reverse_payment');
    Route::post('/cancel_capt_disb','FlowApp\LoanController@cancel_capture_disbursal');

    Route::post('/list_upg_reqs','FlowApp\LoanController@list_fa_upgrade_requests');

    Route::post('/upgrade', 'FlowApp\LoanController@fa_upgrade_approval');   
    

    Route::post('/remove_disbursal','FlowApp\LoanController@remove_disbursal');
 
    Route::post('/recon','FlowApp\LoanController@update_recon_status');

    Route::post('/reinitiate_recon','FlowApp\LoanController@reinitiate_recon');

    Route::post('/mnl_capture_fas', 'FlowApp\LoanController@manual_capture_reports');


   });




Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/address'], function () {   

   

   Route::post('/addr_config', 'Admin\AddressInfoController@get_addr_config');
});




Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => '/address_field'], function () {   

   Route::post('/update', 'Admin\AddressInfoController@update');
});

Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/account'], function () {   

   Route::post('/create', 'Admin\AccountController@create');
   Route::post('/list', 'Admin\AccountController@list');
   Route::post('/status', 'Admin\AccountController@update_account_status');
   Route::post('/view', 'Admin\AccountController@view');
   Route::post('/make_primary', 'Admin\AccountController@make_primary');
   Route::post('/update', 'Admin\AccountController@update');
//    Route::post('/create_acc_txn', 'Admin\AccountController@create_acc_txn');
   Route::post('/list_acc_txns', 'Admin\AccountController@list_acc_txns');
   Route::post('/ref_accounts', 'Admin\AccountController@get_ref_accounts');
   Route::post('/balance', 'Admin\AccountController@get_acc_n_balance');
   Route::post('/search','Admin\AccountController@get_acc_txns');
   

});



Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'app/loan_txns/'], function () {   
   
   Route::post('/disburse', 'FlowApp\LoanTransactionController@disburse');
   
   Route::post('/instant_disburse', 'FlowApp\LoanTransactionController@instant_disburse');

   Route::post('/capture_repayment', 'FlowApp\LoanTransactionController@capture_repayment');

   Route::post('/list', 'FlowApp\LoanTransactionController@list');


 
});


Route::group(['middleware' => ['flow', 'cors' , 'auth.app'], 'prefix' => 'admin/agreement'], function () {

    

    Route::post('/cust', 'Admin\AgreementController@generate_new_cust_agreement');

    Route::post('/save', 'Admin\AgreementController@save_agreement');

    //Route::post('/view_aggr', 'Admin\BorrowerController@view_agreement');

    Route::post('/load', 'Admin\AgreementController@load_aggrs_to_upload');

    Route::post('/status', 'Admin\AgreementController@update_aggr_status');

    Route::post('/aggrs', 'Admin\AgreementController@get_existing_aggr');

    Route::post('/', 'Admin\AgreementController@generate_new_master_agreement');  

    Route::post('/list', 'Admin\AgreementController@list_master_agreements');

    Route::post('/list_products', 'Admin\AgreementController@list_dp_lndr_spcfc_products');


    

    //Route::post('/name_list', 'Admin\AgreementController@get_name_list');


});


Route::group(['middleware' => ['flow', 'cors' , 'auth.app'], 'prefix' => 'admin/score_model'], function () {
    Route::post('/cs_result_config', 'Admin\DataScoreModelController@create_cs_results');
    Route::post('/cs_result_config/list', 'Admin\DataScoreModelController@get_cs_result_config');
    Route::post('/cs_factor', 'Admin\DataScoreModelController@create_cs_factor');
    Route::post('/weightages', 'Admin\DataScoreModelController@list_cs_weightages');
    Route::post('/weightages/update', 'Admin\DataScoreModelController@update_cs_weightages');
    Route::post('/cs_factor/list', 'Admin\DataScoreModelController@get_filtered_csfs');
    Route::post('/create', 'Admin\DataScoreModelController@create');
    Route::post('/list', 'Admin\DataScoreModelController@list');
    Route::post('/cust_csf_values/create', 'Admin\DataScoreModelController@create_cust_csf_values');
    Route::post('/eligibility', 'Admin\DataScoreModelController@get_score_eligibility');
    });

   Route::group(['middleware' => ['flow', 'cors' , 'auth.app'], 'prefix' => 'admin'], function () {
     Route::post('/upload_cust_txns', 'Admin\DataScoreModelController@upload_cust_txns'); 
   });


Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/user'], function () {   
   Route::post('/list', 'AppUserController@list');
   Route::post('/app/password/email','FlowApp\AppUserForgotPasswordController@sendResetLinkEmail');
   Route::post('/inv/password/email','InvApp\InvForgotPasswordController@sendResetLinkEmail');
});



Route::group(['middleware' =>  ['flow', 'cors','auth.app'], 'prefix' => 'admin/audit_kyc'], function () {
    Route::post('/cust_reg', 'Admin\BorrowerController@reg_cust');
    Route::post('/holder_name_proof', 'Admin\LeadController@email_holder_name_proof_to_app_support');
    Route::post('/update', 'Admin\LeadController@update_status');
    Route::post('/reject', 'Admin\LeadController@reject_kyc');
    Route::post('/audit_name', 'Admin\LeadController@audit_name');
    Route::post('/bypass_holder_name_audit', 'Admin\LeadController@bypass_holder_name_audit');
});

Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/report'], function () {   
   Route::post('/flow_kpi_report', 'Admin\ReportController@get_kpi_report');
   Route::post('/', 'Admin\ReportController@get_report');
   Route::post('growth_chart','Admin\ReportController@get_growth_chart');
   Route::post('custom_report','Admin\ReportController@get_custom_report');
   Route::post('rm_report','Admin\ReportController@get_rm_report');
   Route::post('/cur_details','Admin\ReportController@get_currency_details');
   Route::post('/sms_reports','Admin\ReportController@get_sms_details');
   Route::post('/report_date','Admin\ReportController@get_report_date');

});

Route::group(['middleware' => ['flow', 'cors' , 'auth.app'], 'prefix' => 'app/otp'], function () {

    Route::post('/status','CommonController@check_for_valid_otp');

});

Route::group(['middleware' =>  ['cors', 'auth.partner'], 'prefix' => 'partner'], function () {   
   
   
   Route::post('/os_amt', 'Vendor\VendorController@get_os_amt');
   
});

Route::group(['middleware' =>  ['cors'], 'prefix' => 'vendor'], function () {   
   Route::post('/yo/payment/notify', 'Vendor\VendorController@receive_yo_payment_notification');
   Route::post('/beyonic/payment/notify','Vendor\VendorController@receive_beyonic_payment_notification');
   Route::post('/chapchap/payment/notify','Vendor\VendorController@receive_beyonic_payment_notification');
});


Route::group(['middleware' => ['ait','cors'], 'prefix' => 'ait'], function(){
    Route::post('/sms/inbound', 'Vendor\AitSMSController@process_inbound_sms');
    Route::post('/sms/report', 'Vendor\AitSMSController@process_sms_delivery_report');
});

Route::post('/util/login', 'Mobile\FlowWalletAppAuthController@login');

Route::group(['middleware'=> ['auth.wallet_app', 'cors'], 'prefix' => 'util'], function(){
    Route::post('/forward_otp', 'FlowInternalController@process_forwarded_otp');
    Route::post('/forward_txn_sms' , 'FlowInternalController@process_transaction_sms');
    Route::post('/configure', 'FlowInternalController@configure_ussd_accounts');
    Route::post('/disb_response', 'FlowInternalController@process_ussd_response');
    Route::post('/heartbeat', 'FlowInternalController@process_heartbeat_response');
    Route::post('/audit_kyc', 'FlowInternalController@save_ussd_response');
    Route::post('/email_insufficient_balance', 'FlowInternalController@email_insufficient_balance');
});

Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'app/write_off/'], function () {  

    Route::post('/', 'FlowApp\WriteOffController@get_write_off'); 

    Route::post('/req_write_off', 'FlowApp\WriteOffController@req_write_off');

    Route::post('/list', 'FlowApp\WriteOffController@listWriteOff');

    Route::post('/appr_reject', 'FlowApp\WriteOffController@appr_reject_write_off');

    Route::post('/recover_amt', 'FlowApp\WriteOffController@get_recovery_amount');

    Route::post('/loan_prov_year', 'FlowApp\WriteOffController@get_loan_prov_year');

});

Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'app/whatsapp'], function () {

    Route::post('/connect', 'FlowInternalController@connect');
    
    Route::post('/send', 'FlowInternalController@send');
    
    Route::post('/logout', 'FlowInternalController@logout');
    
    Route::post('/get_sessions', 'FlowInternalController@get_sessions');
    
    Route::post('/check_session_status', 'FlowInternalController@check_session_status');

});

Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/rm_assign'], function () {  

    Route::post('/', 'Admin\RMCustAssignmentsController@rm_reassignment');

    Route::post('/rm_details', 'Admin\RMCustAssignmentsController@rm_and_terr_details');
    Route::post('/cust_details', 'Admin\RMCustAssignmentsController@cust_details');
    Route::post('/temp_rm_details', 'Admin\RMCustAssignmentsController@temp_rm_details');
    Route::post('/SMS_details', 'Admin\RMCustAssignmentsController@sms_details');

});

Route::group(['middleware' => ['aitv','cors']], function(){
    Route::post('/callback','AfricasVoiceController@process_callback');
    Route::post('/event', 'AfricasVoiceController@process_event');
    // Route::post('/callback_inbound','AfricasVoiceController@process_inbound_callback');
    // Route::post('/event_inbound', 'AfricasVoiceController@process_inbound_event');
} );

Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/sms'], function () {
    Route::post('/search','Admin\SMSController@search_sms_logs');
});


Route::group(['middleware' =>  ['flow', 'cors', 'auth.app'], 'prefix' => 'admin/rm_targets'], function () {

    Route::post('/view', 'Mobile\RMController@view_rm_target'); 
    Route::post('/update', 'Mobile\RMController@update_rm_target');
});




Route::fallback(
    function() {
             return response()->json(["message" => "Invalid HTTP Method or URL"]) ;
                      }
        );

Event::listen('illuminate.query', function($query)
{
    var_dump($query);
});
