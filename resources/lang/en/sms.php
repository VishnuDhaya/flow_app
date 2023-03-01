<?php

$GREETING_MSG = "[FLOW] Hi :cust_name,";

$WELCOME_MSG_FLOW = "[FLOW] Welcome to Flow family!";

$REPEAT_FA_FALIURE = "[FLOW] Unable to REPEAT FLOAT ADVANCE.";

return [

    "WELCOME_MSG" => "$WELCOME_MSG_FLOW  :cust_name!. Your Flow ID is :cust_id. Send this code :otp_code to :sms_reply_to to complete your registration process.\n
     We are glad to have you. Please call :customer_success for assistance. Keep Flowing!",

    "WELCOME_ENABLED_MSG" => "$WELCOME_MSG_FLOW :cust_name!. Your Flow ID is :cust_id. We are glad to have you. Please call :customer_success for assistance. Keep Flowing!",

    "APPL_CONFIRMATION_MSG" =>" $GREETING_MSG This SMS is sent to you to get a one time confirmation for your new Float Advance product application. You applied for :fa_amount :currency_code for :duration days with a fee of :flow_fee :currency_code. Repayment amount is :due_amount :currency_code. This is a one time confirmation that will be used when you apply for the same Float Advance product starting :starting_date .To confirm, send confirmation code as SMS to :sms_reply_to from your registered mobile :cust_mobile_num in below format. \n\n FLOW :confirm_code \n\n Call :cs_num to decline/change application or need support",
    
    "ADDL_NUM_OTP_MSG" => ":cust_name has declared you are :gender :relation. To confirm ownership of this SIM :cust_mobile_num, send below code as SMS to :otp_code in below format.\n\nFLOW :otp_code",

    "OTP_MSG" => "$GREETING_MSG  to confirm ownership of this SIM :cust_mobile_num, send below code as SMS to :otp_code in below format.\n\nFLOW :otp_code",

    "INVALID_OTP_SMS" => "[FLOW] Confirmation code you sent is invalid. Please contact :cs_num for support if required.",

    "EXPIRED_OTP_SMS" => " [FLOW] Confirmation code you sent is expired . Please contact :cs_num to send you a new confirmation code.",

    "REPAYMENT_SETTLED_MSG" => " $GREETING_MSG Payment of :currency_code :payment received. Please send FLOW REPEAT as an SMS to :sms_reply_to to apply the same FLOAT ADVANCE product or call :mobile_num",

    "REPAYMENT_PENDING_MSG" => " $GREETING_MSG Payment of :currency_code :payment received out of total due :currency_code :loan_principal . Current outstanding is :currency_code :new_os_amount. Pay back in time!",

    "DUE_DATE_REMIND_MSG" => "$GREETING_MSG PAYMENT DUE TODAY :currency_code :current_os_amount; Pay back in time to avoid rejection of Float application and keep getting Float Advances.",

    "EVENING_DUE_DATE_REMIND_MSG" => "$GREETING_MSG PAY :currency_code :current_os_amount before end of today to avoid fine for late payment and avoid rejection of Float application.",

    "DUE_TOMORROW_REMIND_MSG" => "$GREETING_MSG PAYMENT DUE TOMORROW!! :currency_code :current_os_amount; Pay back in time to keep getting Flow Float Advances and growing your mobile money business.",

    "REGULAR_OVERDUE_MSG" => "$GREETING_MSG PAYMENT OVERDUE! :currency_code :current_os_amount is LATE. Penalty :currency_code :provisional_penalty added every day. Due amount now :currency_code :os_amt_w_prv_pnlty. Call :mobile_num for details.",

    "APPROVAL_PENDING_MSG" => "[FLOW] :approver_name, for the last 30 mins, you have :no_of_fas FAs pending your review. Please indicate the reason while you Approve or Reject the FA at your discretion.",

    "FIELD_VISIT_FEEDBACK_MSG" => "$GREETING_MSG Tnkx for your cooperation when :visitor_name visited your shop today at :cust_address, :visit_time. To give feedback on the visit or if visit didnt happen, pls call :market_head_mobile.",

    "FA_DELAYED_NOTIFICATION" => "$GREETING_MSG Your FLOAT ADVANCE is delayed. Please be patient while we process shortly. You will receive an SMS once FLOAT ADVANCE is credited",

    "LEAD_MSG_WITH_RM_ASSIGNED" => "$WELCOME_MSG_FLOW Our representative :rm_name  will reach out to you to start the registration process. You can contact :rm_gender_pronoun on :rm_mobile_num",

    "LEAD_MSG_WITHOUT_RM_ASSIGNED" => "$WELCOME_MSG_FLOW A Flow representative will reach out to you to start the registration process. You can contact Flow on :cs_num ",

    "LEAD_MSG_BY_RM" => "$WELCOME_MSG_FLOW Flow representative :rm_name is visiting you now. You can note his/her contact number :rm_mobile_num.",

    "ALT_NUM_SMS_RESPONSE" => "[FLOW] NEED ACTION : SMS transactions can be fulfilled ONLY from your registered mobile number :reg_mobile_num.",

    "UNKNOWN_NUM_SMS_RESPONSE" => "[FLOW] NEED ACTION : SMS transactions can be fulfilled ONLY from your registered mobile number.",
    
    "LAST_FA_OS_REPEAT_FA" => "[FLOW] Unable to REPEAT FLOAT ADVANCE(FA). If you have already repaid your previous FA, please wait while we settle. Please call us at :cs_num for details.",
    
    "DISABLED_CUST_REPEAT_FA" => "$REPEAT_FA_FALIURE Your Flow account is in disabled status. Please call us at :cs_num for details.",

    "PENDING_FA_APPL_FA_REPEAT" => "$REPEAT_FA_FALIURE You already have a pending FA application. Please call us at :cs_num for details.",

    "UNABLE_TO_REPEAT_FA" => "$REPEAT_FA_FALIURE Unable to process your request now. Please call us at :cs_num for details.",

    "REPEAT_FA_QUEUE" => "If you have already repaid your previous FA, please allow us to settle it and then we will process your REPEAT request. Thanks for your patience",

    "TF_DISBURSEMENT_MSG" => "$GREETING_MSG Loan amount of :currency_code :loan_principal is credited to your EzeeMoney SC A/C :acc_number towards POS purchase. Interest for :duration months is :flow_fee :currency_code. Daily repayment deduction of :currency_code :daily_deductions will start on :repayment_date until the loan principal and interest are paid back completely",

    "CUST_APP_MOBILE_CONFIRM" => "[FLOW] The OTP for the mobile number verification on FLOW App is :otp_code. Please Don,t share this OTP with anyone. Thank you",

    "DISBURSEMENT_MSG" => "$GREETING_MSG Float advance :currency_code :loan_principal credited to :acc_number; Due date: :due_date Total amount due: :currency_code :current_os_amount. Pay back in time!",
    
    "REPEAT_FA_MSG" => "[FLOW] Your FA application has been submitted for approval. You will be notified about the next steps",

    "MERCHANT_CHANGE_FA_REPEAT"=>"$REPEAT_FA_FALIURE You have changed the account to :acc_number and can not repeat the previous FA. Please call us on :cs_num to submit a new application",

    "RM_CUST_REASSIGN_MSG" => "Dear :cust_name, From today, Your RM :disable_rm_name will no longer be assisting as he/she is no longer a FLOW staff. Any interaction with him/her will be at your own risk. You have been assigned a new RM (:subs_rm_name - {:subs_rm_mobile_num) to assist. Please call Flow customer success for Info.",

    "RM_CUST_OTHER_MSG" => "Dear :cust_name, From today, Your RM :disable_rm_name will no longer be assisting. You have been assigned a new RM (:subs_rm_name - {:subs_rm_mobile_num) to assist. Please call Flow customer success for Info.", 
       
    "CUST_RESOLVED_COMPLAINT_MSG" => "$GREETING_MSG the complaint you have raised on :raised_date has been resolved by FLOW. Please refer to your customer app for more details.",

    "CUST_AGGR_RENEWAL_MSG" => "Your agreement with Flow is expiring on :aggr_valid_upto. To get FAs without interruption please arrange/cooperate visit with RM for renewal",

    "PROB_CUST_AGGR_RENEWAL_MSG" => "Your agreement will be expiring soon. To get FAs without interruption please arrange/cooperate visit with RM for renewal" 
];