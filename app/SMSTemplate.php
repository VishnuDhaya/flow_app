<?php

namespace App;

class SMSTemplate{

const GREETING_MSG = "[FLOW] Hi:cust_name";

const WELCOME_MSG = "Welcome to Flow, :cust_name!. Your Flow ID is :cust_id. Send this code :otp_code to :sms_reply_to to complete your registration process.\n
  We are glad to have you. Please call :customer_success for assistance. Keep Flowing!";

const APPL_CONFIRMATION_MSG = "Hi :cust_name. This SMS is sent to you to get a one time confirmation for your new Float Advance product application. You applied for :fa_amount :currency_code for :duration days with a fee of :flow_fee :currency_code. Repayment amount is :due_amount :currency_code. This is a one time confirmation that will be used when you apply for the same Float Advance product starting :current_date.\n\nTo confirm send confirmation code as SMS to :sms_reply_to from your registered mobile :cust_mobile_num in below format.\n\nFLOW :confirm_code\n\nCall :cs_num to decline/change application or need support";



// const OTP_MSG = "Hi :cust_name, as a part of verification process, please confirm ownership of this SIM-:cust_mobile_num  by sending the OTP code :otp_code  in the format given below to :sms_reply_to.\n\nFLOW :otp_code";

const OTP_MSG = "[FLOW] Hi :cust_name, to confirm ownership of this SIM :cust_mobile_num, send below code as SMS to :otp_code in below format.\n\nFLOW :otp_code";

const WELCOME_ENABLED_MSG = "Welcome to Flow, :cust_name!. Your Flow ID is :cust_id. We are glad to have you. Please call :customer_success for assistance. Keep Flowing!";

const RCVRY_CONFIRM_MSG = "Hi :cust_name, you have requested to make a cash payment of :recovery_amount :currency_code for your overdue FA. To confirm please send the OTP as an SMS to :sms_reply_to in the format given below.\n\nFLOW :confirm_code\n\nPlease contact :cs_num if you have not requested to make a cash payment.";

const RCVRY_RECORDED_MSG = "Hi :cust_name, our RM :rs_name has successfully received a cash payment of :recovery_amount :currency_code for your overdue FA";
    
const DISBURSEMENT_MSG = self::GREETING_MSG.", Float advance :currency_code :loan_principal credited to :acc_number; Due date: :due_date Total amount due: :currency_code :current_os_amount. Pay back in time!";

const TF_DISBURSEMENT_MSG = self::GREETING_MSG.", Loan amount of :currency_code :loan_principal is credited to your EzeeMoney SC A/C :acc_number towards POS purchase. Interest for :duration months is :flow_fee :currency_code. Daily repayment deduction of :currency_code :daily_deductions will start on :repayment_date until the loan principal and interest are paid back completely.";

// const REPAYMENT_SETTLED_MSG = self::GREETING_MSG.", Repayment of :currency_code :payment received. Please send FLOW REPEAT as an SMS to :sms_reply_to to apply the same amount of FLOAT ADVANCE product again or call :mobile_num for support. Thank you for using Flow.";

const REPAYMENT_SETTLED_MSG = self::GREETING_MSG.", Payment of :currency_code :payment received. Please send FLOW REPEAT as an SMS to :sms_reply_to to apply the same FLOAT ADVANCE product or call :mobile_num";


// const REPAYMENT_PENDING_MSG = self::GREETING_MSG.", Repayment of :currency_code :payment rcvd out of tot due :currency_code :current_os_amount. Current outstanding is :currency_code :new_os_amount. Pay back in time!";

const REPAYMENT_PENDING_MSG = self::GREETING_MSG.", Payment of :currency_code :payment received out of total due :currency_code :loan_principal . Current outstanding is :currency_code :current_os_ammount. Pay back in time!";



// const DUE_DATE_REMIND_MSG = self::GREETING_MSG.", Repayment due today: :currency_code :current_os_amount; Pay back in time and keep getting Flow advances.Keep it flowing!";

const DUE_DATE_REMIND_MSG = self::GREETING_MSG.",  PAYMENT DUE TODAY:  :currency_code :current_os_amount; Pay back in time to avoid rejection of Float application and keep getting Float Advances.";

const EVENING_DUE_DATE_REMIND_MSG = self::GREETING_MSG.", PAY :currency_code :current_os_amount before end of today to avoid fine for late payment and avoid rejection of Float application.";



// const DUE_TOMORROW_REMIND_MSG = self::GREETING_MSG.", PAYMENT DUE TOMORROW!! :currency_code :current_os_amount; Pay back in time and keep getting Flow advances. Pay ontime to avoid rejection of Float application. Keep it flowing!";

const DUE_TOMORROW_REMIND_MSG = self::GREETING_MSG.", PAYMENT DUE TOMORROW: :currency_code :current_os_amount; Pay back in time to keep getting Flow Float Advances and growing your mobile money business.";


// const REGULAR_OVERDUE_MSG = self::GREETING_MSG.", PAYMENT OVERDUE!! Repayment of :currency_code :balance_amount is LATE. Penalty of :currency_code :provisional_penalty added. Due amount now: :currency_code :os_amt_w_prv_pnlty. Pay ontime to avoid rejection of Float application. Call :mobile_num for details.";

const REGULAR_OVERDUE_MSG = self::GREETING_MSG.", PAYMENT OVERDUE!  :currency_code :current_os_amount is LATE. Penalty :currency_code :provisional_penalty added every day. Due amount now: :currency_code :os_amt_w_prv_pnlty. Call :mobile_num for details.";


const LOAN_COMMENT_MSG = ":cmt_type : FA ID - :loan_doc_id 
:cmt_from_name: :comment";

// const APPROVAL_PENDING_MSG = ":approver_name, for the last :last_time, you have :no_of_fas FA appls waiting for your review. Ensure you add the reason while you Approve or Reject the FAs at your discretion.";

const APPROVAL_PENDING_MSG = ":approver_name, for the last 30 mins, you have 10 FAs pending your review. Please indicate the reason while you Approve or Reject the FA at your discretion.";


// const FIELD_VISIT_FEEDBACK_MSG = "Hi :cust_name, Tnkx for your cooperation when :visitor_name visited your shop at :cust_address, :visit_time. If you have feedback on the visit or if visit didnt happen, pls call :market_head_mobile.";

const FIELD_VISIT_FEEDBACK_MSG = "[FLOW] Hi :cust_name, Tnkx for your cooperation when :visitor_name visited your shop today at :cust_address, :visit_time. To give feedback on the visit or if visit didnt happen, pls call :market_head_mobile.";


// const CALL_LOG_FEEDBACK_MSG = "Thank you for talking to Flow, keep your payments on time and Keep FLOWing.";

const INVALID_OTP_SMS = "Confirmation code you sent is invalid. Please contact :cs_num for support if required.";

const EXPIRED_OTP_SMS = "Confirmation code you sent is expired . Please contact :cs_num to send you a new confirmation code.";

// const REWARD_MSG = "Hello :cust_name [:cust_id]!, Flow has credited your account with a cash back reward of :cashback UGX for your excellent on-time repayment. Keep paying on time and keep growing your business with Flow!";

const ALT_NUM_SMS_RESPONSE = "NEED ACTION : SMS transactions can be fulfilled ONLY from your registered mobile number :reg_mobile_num.";

const UNKNOWN_NUM_SMS_RESPONSE = "NEED ACTION : SMS transactions can be fulfilled ONLY from your registered mobile number";

// const FA_DELAYED_NOTIFICATION = "Hello :name, Your FLOAT ADVANCE request will be delayed. Please be patient while we process shortly. You will receive an SMS once your FLOAT ADVANCE has been sent by FLOW.";

const FA_DELAYED_NOTIFICATION = "[FLOW] Hi :cust_name, Your FLOAT ADVANCE is delayed. Please be patient while we process shortly. You will receive an SMS once FLOAT ADVANCE is credited";

// const LEAD_MSG_WITH_RM_ASSIGNED = " Welcome to Flow family! Flow representative :rm_name will reach out to you to start the registration process. You can contact :rm_name at :rm_mobile_num for more details";

// const LEAD_MSG_WITH_RM_ASSIGNED =" Our representative JUSTINEJUSTINE will reach out to you to start the registration process. You can contact him on 987654321"

const CUST_APP_MOBILE_CONFIRM = "The OTP for the mobile number verification on FLOW App is :otp_code. Please do not share this OTP with anyone. Thank you";

const LEAD_MSG_WITH_RM_ASSIGNED = " Welcome to Flow family! Flow representative :rm_name will reach out to you to start the registration process. You can contact :rm_name at :rm_mobile_num for more details";


const LEAD_MSG_WITHOUT_RM_ASSIGNED = " A Flow representative will reach out to you to start the registration process. You can contact Flow on  :cs_num ";

const LEAD_MSG_BY_RM = " Welcome to Flow family! Flow representative :rm_name is visiting you now. You can note :rm_name contact number :rm_mobile_num for more details";


  //const PENALTY_ONLY_PENDING_MSG = self::GREETING_MSG.", You've paid :currency_code :paid_amount. Late payment penalty still due. Due amount now: :currency_code :current_os_amount. Call :mobile_num for details.";


//Hello 1234567890! UEZM-123456, Float advance UGX 5,000,000 credited SC code; Due date: 2019-05-30 23:59:59 Total amount due: UGX 5,000,000.Pay back in time!

//Hello 1234567890! UEZM-123456, Repayment due today: UGX 5,000,000; Pay back in time and keep getting FlowEzee advances.Keep it flowing!

//Hello 1234567890! UEZM-123456, Repayment of UGX 5,000,000 received.Please call 0772656752 to apply for the next advance. Thank you for using FlowEzee.

//Hello 1234567890! UEZM-123456, Repayment of UGX 5,000,000 is LATE.Due Amount now: UGX 5,000,000.Penalty of UGX 5,000,000 added.Call 0772656752 for details.



}

?>
