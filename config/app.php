<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */
    'log_sql' => env('LOG_SQL'),
    'name' => env('APP_NAME', 'FLOW APP'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => env('APP_DEBUG', false),

    '1_usd_in_UGX' => 3744,

    'validate_agreement' => true,

    'default_prob_fas' => 15,

    'default_cond_fas' => 8,

    'UEZM_TF_CRED' => [
        'SC_CODE_CRED' => [
            'username' => '0703463210',
            'password' => 'ABC@#abc123456'
        ],
        'LOAN_SETUP_CRED' => [
            'username' => 'Geofrey',
            'password' => 'Password1*'
        ],
        'LOAN_REPORT_CRED' => [
            'username' => '92249908',
            'password' => 'ABC@#abc9879'
        ]
    ],

    'vendor_credentials' => [ 
                              'UAIT' => ['SMS-OB' => ['UGA' => ['username' => env('UAIT_UGA_USERNAME'),
                                                                'api_key' => env('UAIT_UGA_API_KEY')],
                                                      'RWA' => ['username' => env('UAIT_RWA_USERNAME'),
                                                                'api_key' => env('UAIT_RWA_API_KEY')]
                                                     ]
                                        ],
                            ],
    
    'sms_vendor' => ['UGA' => 'UAIT', 'RWA' => 'UAIT'],
    
    'recon_scr_strt_date' => '2022-01-01' ,
                            
    'sms_reply_to' => ['UGA' => '6115', 'RWA' => '5025'],
    
    'partner_creds' => [
                        'RRTN' => ["test" => ["username" => "rtn_test_api_user", "token" => 'weu4bc1zg3vcdl6d', "country_code" => 'RWA' ], "production" => ["username" => "rtn_api_user", "token" => 'hgyj5vfbdl1icw2n', "country_code" => 'RWA' ] ],
                        
                        'UISG' => ["test" => ["username" => "isg_test_api_user", "token" => 'hudijg07rkfrux51', "country_code" => 'UGA'], "production" => ["username" => "isg_api_user", "token" => '4s3afbbdhjcy24xa', "country_code" => 'UGA' ] ],

                        'CCA' => ["test" => ["username" => "cca_test_api_user", "token" => 'eyJhbGciOiJIUzI1', "country_code" => 'UGA'], "production" => ["username" => "cca_api_user", "token" => 'SflKxwRJSMeKKF2Q', "country_code" => 'UGA' ] ],
                    ],
    
    'internal_api_creds' => [ 'scoring' => [ 'username' => 'FLOW', 'token' => 'eyJhbGciOiJIUzUxMiJ9' ] ],


   // 'customer_success' => ['UEZM' => '+256 750 554558', 'CCA' => '+256 759 625559', 'XXX' => '+256 776 886388', 'UMTN' => '+256 776 909989','UISG' => '+256 776 909989', 'RRTN' => '+250791346689', 'RBOK' => '+250791346689', 'RMTN' => '+250791346689', 'UATL' => '+256 776 909989', 'RATL' => '+250791346689'],
    
   // 'customer_success_mobile' => ['UEZM' => '750554558', 'CCA' => '759625559', 'UMTN' => '776909989', 'XXX' => '776886388' , "UISG" => '776909989', "RRTN" => '791346689', 'RBOK' => '791346689', 'RMTN' => '791346689', 'UATL' => '776909989', 'RATL' => '791346689' ],



    'customer_success' => ['UEZM' => '0800220707', 'CCA' => '0800220707', 'XXX' => '+256 776 886388', 'UMTN' => '0800220707','UISG' => '0800220707', 'RRTN' => '+250791346689', 'RBOK' => '+250791346689', 'RMTN' => '+250791346689', 'UATL' => '0800220707', 'RATL' => '+250791346689'],

    // 'audit_kyc_line' => ['UMTN' => '797903', 'UEZM' => 'upload', 'CCA' => 'upload', 'RBOK' => 'upload', 'RMTN' => 'upload'], 
    
    'customer_success_mobile' => ['UEZM' => '0800220707', 'CCA' => '0800220707', 'UMTN' => '0800220707', 'XXX' => '776886388' , "UISG" => '0800220707', "RRTN" => '791346689', 'RBOK' => '791346689', 'RMTN' => '791346689', 'UATL' => '0800220707', 'RATL' => '791346689' ],


    'swap_sip_to_mobile' =>['XXX' => '+256780554558'],
    
    'audit_kyc_line' => ['UMTN' => 'ussd', 'UEZM' => 'upload', 'CCA' => 'upload', 'RBOK' => 'upload', 'RMTN' => 'ussd', 'RATL' => 'ussd'], 
    
    'swap_sip_to_mobile' =>['XXX' => '+256 776 886388'],
    
    'override_bal_check' => ['UEZM','CCA', 'UMTN', 'UFLO','UISG'],
    
    'flow_disbursal_limit' => 10000000,

    'acc_prvdr_disbursal_limits' => ['CCA' => 2000000],

    'operations_manager' => ['UEZM' => '2443', 'CCA' => '2443', 'UMTN' => '2443','UISG' => '2443', 'RBOK' => 4245, 'RMTN' => 4245],

    'max_allowed_condonation' => 2,

    'condonation_punishment_delay' => 7,

    'auto_condonation_overdue_days' => 30,

    'kyc_cut_off_days' => 30,

    'cust_status_cut_off_days' => 10,

    'rm_approval_limit' => 10,

    'opm_approval_limit' => 1,

    'new_appl_cust_fund' => null,

    'perf_eff_cutoff_days' => 185,

    'otp_validity' => 30, // validity duration of an OTP;

    'fa_delay_notify_time' => 20, //minutes after FA appl- send delay acknowledgement to customer if not disbursed

    'max_resends' => 2, //maximum times OTP can be resent

    'fa_appr_delay_notify_time' => 5, //minutes from application after  which non-approved FA Appls will be displayed on home page

    'fa_conf_delay_notify_time' => 5, //minutes from approval after which unconfirmed FAs will be displayed on home page

    'aggr_expiry_thers_days' => 7,

    'app_support_email' => env('APP_SUPPORT_EMAIL'),

    'level3_support_email' => 'techdev@flowglobal.net',
    
    'ops_auditor_email' => ['praveen@flowglobal.net','kevina@flowglobal.net','sateesh@flowglobal.net'],

    'founder_emails' => ['nitin@flowglobal.net', 'michael@flowglobal.net', 'sateesh@flowglobal.net'],


    'first_n_prob_fas_wo_score' => ['UEZM' => 5, 'CCA' => 5, 'UMTN' => 5, 'UISG' => 5, 'RRTN' => 5, 'RBOK' => 5, 'RMTN' => 5, 'RATL' => 5],

    'first_n_cond_fas_wo_score' => ['UEZM' => 5, 'CCA' => 5, 'UMTN' => 5 , 'UISG' => 5, 'RRTN' => 5, 'RBOK' => 5, 'RMTN' => 5, 'RATL' => 5],


    'auto_disbursal_acc_providers' => ['UEZM', 'UMTN', 'RRTN', 'RMTN', 'RBOK', 'RATL'],

    'RMTN_district_accounts' => ['gasabo' => '791519171', 'nyarugenge' => '791516469', 'kicukiro' => '791334419', 'eastern_province' => '791637017'],

    'branch_wise_acc_prvdr_list' => ['RMTN'],

    'tf_acc_prvdrs' => ['UGA' => ['UEZM']],

    'acc_prvdrs_with_data' => ['UEZM', 'CCA', 'UMTN', 'UISG', 'RRTN', 'RBOK', 'RMTN', 'RATL'],

    'single_session_acc_prvdrs' => ['UMTN', 'RBOK', 'RMTN', 'RATL'],

    'schedules_per_slot_limit' => 10,

    'waive_fee_validity' => 1, //duration (in hours) after disbursal during which a customer can repay his loan principal and get his fee waived

    'text_extract_client' => ['UGA' => 'reckognition', 'RWA' => 'reckognition'],

    'pre_approval_validity_days' => 90,

    'pre_approval_fa_count' => 5,
    
    'pre_approval_enabled_markets' => ['UGA'],
    
    'tf_exception_list' => ['00849460', '69028483', '14503440', '85200886', '93327921', '15616030', '89435481', '41275310', '19282502', '81157001', '38053726', '49825940', '25254222', '26583889', '47810426'],

    'acc_prvdr_logo' => ["UGA" => ['UEZM' => "/files/UGA/acc_providers/UEZM/acc_prvdr_logo/t_1601458332.png", 'CCA' => "/files/UGA/acc_providers/CCA/acc_prvdr_logo/t_1601458279.png", 'UMTN' => "/files/UGA/acc_providers/UMTN/acc_prvdr_logo/t_1627630100.png", 'UDFC' => "/files/UGA/acc_providers/UDFC/acc_prvdr_logo/t_2205161406.png", 'UISG' => ""], "RWA" => ['RBOK' => "/files/RWA/acc_providers/RBOK/acc_prvdr_logo/t_2204071052.jpg", 'RMTN' => "/files/RWA/acc_providers/RMTN/acc_prvdr_logo/t_1627630100.png", 'RRTN' => "/files/RWA/acc_providers/RRTN/acc_prvdr_logo/t_2201458280.png", 'RATL' => "/files/RWA/acc_providers/RATL/acc_prvdr_logo/t_1669100398.png"]],

    'ussd_disb_success_response' => ['UMTN' => ["You have sent UGX"], 'RMTN' => ["Transfer float is completed successfully your balance after that is", "koherezwa byakozwe neza musigaranye", "murakoze gukoresha mtn mobile money"], 'RATL' => ["Money successfully sent to"]],
    
    'ussd_disb_failure_response' => ['UMTN' => ["try again", "Receiver not found"], 'RMTN' => ["try again", "Receiver not found", "Iki gikorwa ntikibashije kunyuramo, mutwihanganire"], 'RATL' => ['Transaction value is higher than your current balance']],

    'acc_elig_validity_days' => 90,

    'acc_prvdrs_allow_approval' => ['UMTN'],  #Temporary approval feature given to Praveen

    #'operations_manager' => ['UGA' => '1681'],

    'stmt_req_max_retries' => 5,

    'lender_code_config' => ['UGA' => 'UFLW', 'RWA' => 'RFLW', '*' => '*'],

    'month_write_off_after_no_payment' => 2,

	"prm_optional_for_aps" => [],

    "max_dist_to_cust" => 50,

    "max_dist_to_force_checkin" => 1000,

    "gps_mandatory_for_schedule" => true,

    "max_checkout_mins" => 120,

    'lbl_field_2' => 'District',

    'resend_otp_delay' => 180,

    'fa_upgradable_upto' => 2,

    'acc_prvdrs_w_alt_acc_num' => ['UGA' => ['UMTN'], 'RWA' => []],


    'fa_upgrade_approvers' => ['relationship_manager', 'ops_admin'],

    'task_approvers' => ['relationship_manager', 'ops_admin'],

    "max_dist_to_track" => 200,

    "register_num_verify" => 'otp',

    "cust_otp_wait_check_time" => [180, 30],

    "alternate_num_verify" => 'otp',

    "addl_num_verify" => 'call_log',

    "holder_name_verify" => 'call_log',

    "owner_name_verify" => 'ussd',

    'acc_num_label' => ['UGA' => ['UEZM' => 'SC Code', 'CCA' => 'Mobile Number', 'UMTN' => 'Agent ID'],'RWA' => ['RMTN' => 'Agent ID', 'RBOK' => 'Account Number', 'RATL' => 'Airtel Wallet Mobile Number']],
    
    'otp_msg' => 'An OTP has been sent to the mobile number.Please ask customer to send the OTP to :shortcode in the format FLOW <OTP> Eg: If OTP is 123456, FLOW 123456',

    'mobile_config_keys' => ['ap_alt_ac_num_name', 'ap_alt_ac_num_hint', 'wallet_mandatory_for_aps', 'prm_optional_for_aps', 'max_dist_to_cust', 'gps_mandatory_for_schedule', 'max_checkout_mins', 'visit_checkout_delay', 'lbl_field_2', 'resend_otp_delay','otp_msg','ap_ac_num_name','ap_ac_num_hint','max_dist_to_force_checkin', 'pre_approval_fa_count','punch_in_criteria', 'max_dist_to_track'],

    'punch_in_criteria' => ["1. Create Lead \n2. Renewal Agreement \n3. Approve/Reject FA \n4. Perform KYC \n5. Approve/Reject FA Upgrade Request \n6. Enable/Disable Pre-Approval \n7. Profile Update \n8. Approve/Reject Waiver Request"],

    'ap_ac_num_name' => ["UEZM" => "SC Code", "UMTN" => "Agent ID", "CCA" => "Registered Mobile Number", "RBOK" => "Account Number", "RMTN" => "MSISDN", "UATL" => "Airtel Wallet Mobile Number", "RATL" => "Airtel Wallet Mobile Number"],

    'ap_alt_ac_num_name' => ['UMTN' => "MSISDN"],

    'ap_ac_num_hint' => [
        "UEZM" => "This is the Service Center(SC) code of the customer given by EzeeMoney to the customer. Note : If there is a starting zero, include that as well.", 
                            "UMTN" => "This is the six digit agent ID provided by MTN to the customer. Note : If there is a starting zero, include that as well.",
                            "CCA" => "This is the mobile number the customer registered with Chap Chap.",
                            
                            "RBOK" => "This is the account number given to the customer by BOK",
                            
                            "RMTN" => "This is the nine digit agent line (SIM) number provided by MTN. Note : Do not include isd code number +250.",

                            "RATL" => "This is the nine digit agent line (SIM) number provided by Airtel. Note : Do not include isd code number +250."
                            ], 

    'ap_alt_ac_num_hint' => ["UMTN" => "This is the nine digit agent line (SIM) number provided by MTN. Note : Do not include isd code number +256."],

  
    'web_ui_config_keys' => ['cust_otp_wait_check_time', 'ap_alt_ac_num_name','ap_ac_num_name', 'acc_prvdrs_allow_approval', 'acc_elig_validity_days','fa_rm_approval_late1','fa_rm_approval_late2','fa_cust_late1', 'fa_cust_late2', 'fa_disb_late1', 'fa_disb_late2','fa_late1','fa_late2', 'capture_payment_late1', 'capture_payment_late2', 'acc_prvdr_support_ussd', 'audit_kyc_line'],

    'rms_monthly_targets' => [
        "2022-01" => ["2707" => 30, "2439" => 30, "2561" => 25, "3150" => 30, "2440" => 25, "2709" => 30, "2562" => 30, "2575" => 30, "1006" => 30, "2509" => 30, "2427" => 30, "1742" => 30, "2560" => 30 ],
        "2022-02" => ["2707" => 60, "2439" => 50, "2561" => 60, "3150" => 60, "2440" => 60, "2709" => 50, "2562" => 60, "2575" => 60, "1006" => 50, "2509" => 50, "2427" => 60, "1742" => 60, "2560" => 50 ],
        "2022-03" => ["2707" => 80, "2439" => 75, "2561" => 75, "3150" => 50, "2440" => 75, "2709" => 75, "2562" => 75, "2575" => 75, "1006" => 75, "2509" => 75, "2427" => 75, "1742" => 75, "2560" => 75, "*" => 50 ],
        "2022-04" => ["2707" => 80, "2439" => 80, "2561" => 80, "3150" => 75, "2440" => 75, "2709" => 80, "2562" => 80, "2575" => 80, "1006" => 80, "2509" => 80, "2427" => 80, "1742" => 80, "2560" => 80, "3568" => 30, "*" => 50 ],
        "2022-05" => ["2707" => 80, "2439" => 80, "2561" => 80, "3150" => 75, "2440" => 75, "2709" => 80, "2562" => 80, "2575" => 80, "1006" => 80, "2509" => 80, "2427" => 80, "1742" => 80, "2560" => 80, "3568" => 60, "*" => 50 ],
        "2022-06" => ["2707" => 75, "2439" => 75, "2561" => 75, "3150" => 75, "2440" => 75, "2709" => 75, "2562" => 75, "2575" => 75, "1006" => 75, "2509" => 75, "2427" => 75, "1742" => 75, "2560" => 75, "3568" => 60, "3948" => 60, "3951" => 60, "3939" => 60, "*" => 75 ],
        "2022-07" => ["2707" => 40, "2439" => 40, "2561" => 40, "3150" => 50, "2440" => 40, "2709" => 50, "2562" => 40, "2575" => 40, "1006" => 30, "2509" => 40, "2427" => 40, "1742" => 40, "2560" => 40, "3464" => 50, "3462" => 50, "3461" => 50, "3463" => 50, "4084" => 50, "4081" => 50, "4082" => 50, "4086" => 40, "3568" => 60, "3948" => 60, "3951" => 60, "3939" => 60, "*" => 75 ],
        "2022-08" => ["2707" => 40, "2439" => 40, "2561" => 40, "3150" => 50, "2440" => 40, "2709" => 50, "2562" => 40, "2575" => 40, "1006" => 30, "2509" => 40, "2427" => 40, "1742" => 40, "2560" => 40, "3464" => 50, "3462" => 50, "3461" => 50, "3463" => 50, "4084" => 50, "4081" => 50, "4082" => 50, "4086" => 40, "3568" => 60, "3948" => 60, "3951" => 60, "3939" => 60, "*" => 50 ],
        "2022-09" => ["2707" => 40, "2439" => 40, "2561" => 40, "3150" => 40, "2440" => 40, "2709" => 40, "2562" => 40, "2575" => 40, "1006" => 0, "2509" => 40, "2427" => 40, "1742" => 40, "2560" => 40, "3464" => 40, "3462" => 40, "3461" => 40, "3463" => 40, "4084" => 40, "4081" => 40, "4082" => 40, "4086" => 40, "4922" => 40, "4923" => 40, "4927" => 40, "4928" => 40, "4926" => 40, "3568" => 50, "3948" => 50, "3951" => 50, "3939" => 50, "4611" => 50, "*" => 50 ]
    ],

    'new_rm_target' => 40,
    
    'aps_reqrd_disb_capture_from_stmt' => ['UMTN', 'RBOK', 'RMTN', 'RATL'],

    'aps_without_disb_id_in_remarks' => ['RMTN', 'RATL'],

    'ussd_disbursal_timeout_secs' => 35,

    'ussd_disbursal_app_process_time' => 25,

    'wallet_app_allowed_roles' => ['ops_admin', 'customer_success', 'customer_success_officer'],

    'heartbeat_interval' => 10, //interval in seconds for which heartbeat request is sent

    'heartbeats_to_monitor' => 10, //no of last heartbeats to consider while checking for outage of wallet app

    'wallet_app_max_resp_time' => 30, //max time for wallet app to respond to FCM message (in seconds)

    'wallet_app_outage_notify_interval' => 10, //time (in minutes) after which duplicate notification will not be sent

    'national_id_instruction' =>
    "Unable to extract text from the National ID. Ensure below is followed before reporting the issue to Biz Ops (App Support)
    
    1. National ID photo is captured over a clear plain background (preferable white)
    2. There is enough lighting around
    3. Capture the National ID more closer and with no shakes

    Even after the doing the above , if you see this message, report to Biz Ops (App Support) to allow you to enter the National ID data manually yourself.",

    'UGA_contact_number' => '+256323200694',

    'ait_voice_hangup_msgs' => [
        "CALL_REJECTED" => "This cause indicates the called party does not wish to accept this call",
        "USER_BUSY" => "This cause is used to indicate that the called party is unable to accept another call because the user busy condition has been encountered/engaged on another call.",
        "NO_ANSWER" => "This cause is used when the called party has been alerted but does not respond with a connect indication within a prescribed period of time.",
        "NO_USER_RESPONSE" => "This cause is used when a called party does not respond to a call establishment message with either an alerting or connect indication within the prescribed period of time allocated.",
        "SUBSCRIBER_ABSENT" => "This cause value is used when a mobile station has logged off, radio contact is not obtained with a mobile station or if a personal telecommunication user is temporarily not addressable at any user-network interface.",
        "SERVICE_UNAVAILABLE" => "This cause is used to report a service not available",
        "UNSPECIFIED" => "This cause happens on very rare occasions when a valid hangup cause can’t be obtained. We (AfricasTalking) are usually alerted for this and we look into it immediately.",
    ],

    'ait_voice_error_msgs' => [
        "InvalidPhoneNumber" => "Recipient number is in an incorrect format",
        "DestinationNotSupported" => "Recipient number is outside the supported zone",
        "InsufficientCredit" => "Your AfricasTalking account has insufficient balance.",
    ],

    // ['UEZM'=>4,'CCA'=>0]
    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    'market_head_mobile' => ['UGA' => '772656752', 'RWA' => '907466540'],

    'visit_checkin_delay' => 5,

    'visit_checkout_delay' => 10,

    'biz_start_hour' => '08:00',

    'biz_end_hour' => '21:00',

    'mob_rm_cal_days' => 10,

    'fa_rm_approval_late1' => 5,
    'fa_rm_approval_late2' => 8,

    'fa_cust_late1' => 5,
    'fa_cust_late2' => 8,

    'fa_disb_late1' => 5,
    'fa_disb_late2' => 8,

    'fa_late1' => 10,
    'fa_late2' => 15,

    'capture_payment_late1' => 5,
    'capture_payment_late2' => 8,

    'mob_rm_cal_prev_days' => 2,

    'stalled_disbursals_threshold' => 3,
    'stalled_disbursals_notify_interval' => 1,
    'whatsapp_group_codes' => ['biz_ops' => ['UGA' => env('BIZ_OPS_JID'), 'RWA' => env('BIZ_OPS_JID')], 'auditor' => ['UGA' => env('OPS_ENGG_JID'), 'RWA' => env('OPS_ENGG_JID')]],
    'statement_import_fails_threshold' => 3,
    'statement_import_fails_notify_interval' => 1,
    'mobile_num' => '9943154341',
    'ISD' => ['IND' => '91', 'UGA' => '256', 'RWA' => '250'],
    'whatsapp_notification_number' => env('APP_SUPPORT_WTSP_NUM'), //isd + num

    'lender_code' => ['UGA' => 'UFLW', 'RWA' => 'RFLW'],

    'allow_cust_stmt_upload' => false,
    
    'agent_stmt_procure_method' => [  
        'stmt_from_rm_app' => ['RMTN', 'RBOK', 'RATL'],
        'req_to_partner' => ['UISG', 'RRTN', 'CCA']
    ],

    'write_off_overdue_days' => 120,

    'account_elig_conditions' => [
        "UMTN" => [
            "elig_on_comms" => ["validity" => 90, "limit" => 2000000], # For new account only
            "appr_inelig_on_comms" => ["validity" => 90, "limit" => 1000000], # For new account only
            "appr_unknown_elig_new_acc" => ["validity" => 90, "limit" => 1000000], # For new account only
            "commission_based_limits" => [
                0 => [ 
                    "validity" => "*", "limit" => 250000, 
                    "commission_from" => 40000, "commission_to" => 60000
                ],
                1 => [ 
                    "validity" => "*", "limit" => 500000, 
                    "commission_from" => 60000, "commission_to" => 120000
                ],
                2 => [ 
                    "validity" => "*", "limit" => 1000000, 
                    "commission_from" => 120000, "commission_to" => 180000
                ],
                3 => [ 
                    "validity" => "*", "limit" => 1500000, 
                    "commission_from" => 180000, "commission_to" => 240000
                ],
                4 => [ 
                    "validity" => "*", "limit" => 2000000, 
                    "commission_from" => 240000, "commission_to" => 300000
                ],
            ]
        ],
        "UEZM" => [
            "tf_w_fa" => ["validity" => 90, "limit" => 1000000]
        ],
        "RMTN" => [
            "commission_based_limits" => [
                0 => [
                    "validity" => "*", "limit" => 300000,
                    "commission_from" => 35000, "commission_to" => 50000
                ],
                1 => [
                    "validity" => "*", "limit" => 500000,
                    "commission_from" => 50000, "commission_to" => 100000
                ]
            ],
        ],
        "RBOK" => [
            "commission_based_limits" => [
                0 => [
                    "validity" => "*", "limit" => 500000,
                    "commission_from" => 35000, "commission_to" => 100000
                ]
            ],
        ],
    ],

    'acc_prvdr_support_ussd' => ['UGA' => ['UMTN'], 'RWA' => ['RMTN']],

    'network_operator_code' => ['UGA' => ['UMTN' => ['64110']], 'RWA' => ['RMTN' => ['63510']]],

    'get_mobile_no_ussd' => ['UGA' => ['UMTN' => '*160*7*1#'], 'RWA' => ['RMTN' => '*135*8#']],

    'sms_lang' => ['UGA' => 'en', 'RWA' => 'rw'],

    'skip_txn_id_check' => ['RBOK', 'CCA'],

    'exempted_log_url' => ['api/app/loan_appl/applications', 'api/app/loan/loans', 'api/admin/lender/account/stmt_search', 'api/admin/borrower/search', 'api/app/loan/get_disb_attempt', 'api/app/loan/held_loans',
        'api/app/user/logout', 'api/app/user/master_data', 'app/common/country', 'app/common/currency', 'app/common/approvers', 'app/common/disbursers','app/common/dropdown', 'admin/data_provider/name_list',
        'admin/rel_mgr/name_list', 'admin/lender/name_list', 'admin/org/name_list', 'admin/org/org_details', 'app/loan_txns/disbursal_accounts','api/admin/data_provider/list','api/mobile/user/login'],


    'stmt_imp_scheduler_accs' => ['01063616833446', '737179936'],


    'bank_repayment_exempted_list' => [ 'UGA' => ['UMTN'], 'RWA' => []],

    'cust_app_version' => '3.2.0',

    'rm_app_version' => '1.5.0',

    'cust_app_default_lang' => ['UGA' => null, 'RWA' => 'rw'],
    
    'feedback_questions' => ['communication', 'professionalism', 'support'],

    'txn_delay_cut_off_time' => 6, // Minutes

    'ignored_acc_prvdr_for_stmt_time_diff' => ['RBOK', 'DFCU'], //Statement transaction ignored accout providers

    'umtn_n_iterations' => ['4161' => 3], // No of iterations to import monthly statement as it has large no of transactions

    'conseq_late_pay' => [
                            "late" => [
                            "header" => "Consequences of Late Payments",
                            "data" => [
                                "Reduces eligible amount",
                                "Makes you ineligible",
                                "Imposes penalty",
                                "Penalty reduces your profits",
                                "You will be disabled from FLOW"
                                ]
                            ],
                            "ontime" => [
                            "header" => "Benefits of Ontime Payments",
                            "data" => [
                                "Qualifies bigger amounts",
                                "Helps to grow your business",
                                "Qualifies cash rewards"
                                ]
                            ]
                        ],
    'default_operations_dashboard_load' => ['fas_pending','active_customer_info', 'penalty', 'acquisition_target', 'fas_due', 'lead_pending', 'get_aggr_due_count', 'get_fa_requst_count',
                                            'get_complaints_count', 'mobile_users', 'repayment_metrics', 'account_balance', 'disb_dup_n_rtn_count', "appl_approvals", "rm_visit_chart",
                                            "fa_applied_by", "disb_delay_reason", "field_visits","apply_to_disb_time_chart"],

    'aggr_expiry_interval' => 14,

    'days_for_active_user' => 30,

    'pre_approval_limit_amount' => '1500000',
    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    'partner_enc_pass' => env('PARTNER_ENC_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Maatwebsite\Excel\ExcelServiceProvider::class,

        /*
         * Package Service Providers...
         */
        Aws\Laravel\AwsServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        Fruitcake\Cors\CorsServiceProvider::class,


        Intervention\Image\ImageServiceProvider::class

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'JWTAuth' => Tymon\JWTAuth\Facades\JWTAuth::class,
        'JWTFactory' => Tymon\JWTAuth\Facades\JWTFactory::class,
        'Consts'  => \App\Consts::class,
        'Image' => Intervention\Image\Facades\Image::class,
        'Excel' => Maatwebsite\Excel\Facades\Excel::class,
        'AWS' => Aws\Laravel\AwsFacade::class,

    ],

];
