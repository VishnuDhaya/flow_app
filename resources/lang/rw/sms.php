<?php

$GREETING_MSG = "[FLOW] Hi :cust_name,";

$WELCOME_MSG_FLOW ="[FLOW] Murakaza neza mu muryango wa Flow! ";   

$REPEAT_FA_FALIURE = "[FLOW] Ntibikunze guhabwa indi nguzanyo ya FLOAT.";

return [

    "APPL_CONFIRMATION_MSG" => "$GREETING_MSG mwemeze ko mwasabye inguzanyo nshya ya Float ingana na :fa_amount :currency_code. Mu minsi :duration days mukishyura :flow_fee :currency_code. Ohereza kode yemeza kuri :sms_reply_to ukoresheje nimero wiyandikishirijeho :cust_mobile_num mu buryo bukurikira.\n\n FLOW :confirm_code \n\nIki kemezo kizakoreshwa mu gusaba kongera guhabwa indi nguzanyo ya Float.Hamagara :cs_num niba ushaka guhagarika/guhindura ubusabe cyangwa ukeneye ubufasha.",

    "OTP_MSG" => "$GREETING_MSG Confirming that this card belongs to you :cust_mobile_num, send this :otp_code as a text message to 5025 as follows.\n\nFLOW :otp_code",

    "FA_DELAYED_NOTIFICATION" => "$GREETING_MSG Inguzanyo yanyu ya Float Iratindaho. Mwihangane akanya gato mu gihe ikiri gutunganywa. Murakira ubutumwa bugufi.",

    "FIELD_VISIT_FEEDBACK_MSG" => "$GREETING_MSG Mwakoze kubufatanye bwanyu ubwo :visitor_name yasuraga iduka ryanyu. Gutanga ibitekerezo cyangwa niba ugusurwa bitabaye, hamagara kuri :market_head_mobile.",

    "DISBURSEMENT_MSG" => "$GREETING_MSG Inguzanyo ya Float ingana na :currency_code :loan_principal ihawe :acc_number; :due_date Uzishyura  :currency_code :current_os_amount. Ubahiriza igihe cyo kwishyura",

    "REPAYMENT_SETTLED_MSG" => " $GREETING_MSG Hishyuwe :currency_code :payment. Ohereza ijambo FLOW REPEAT kuri :sms_reply_to niba ushaka gusaba indi nguzanyo ya Float cyangwa uhamagare :mobile_num.",

    "REPAYMENT_PENDING_MSG" => " $GREETING_MSG Hishyuwe :currency_code :payment. Yose hamwe ni :currency_code :loan_principal. Asigaye :currency_code :new_os_amount. Ubahiriza igihe",

    "DUE_DATE_REMIND_MSG" => "$GREETING_MSG Muributswa KWISHYURA UYU :currency_code :current_os_amount; Ishyura ku gihe wirinde kwimwa inguzanyo ya Float ukomeze kuyihabwa.",

    "EVENING_DUE_DATE_REMIND_MSG" => "$GREETING_MSG ISHYURA :currency_code :current_os_amount. Mbere yuko uyu umunsi urangira, wirinde gucibwa amande yubukererwe no kwimwa inguzanyo ya Float.",

    "DUE_TOMORROW_REMIND_MSG" => "$GREETING_MSG URASABWA KWISHYURA BITARENZE EJO :currency_code :current_os_amount",

    "REGULAR_OVERDUE_MSG" => "$GREETING_MSG MWAKEREWE KWISHYURA! :currency_code :current_os_amount. Amande ni :currency_code :provisional_penalty. yongerwaho buri munsi. :currency_code :os_amt_w_prv_pnlty. Hamagara :mobile_num ku bisobanuro. ",

    "APPROVAL_PENDING_MSG" => "[FLOW] :approver_name, hari ubusabe :no_of_fas bwa FA ugomba kugenzura. Werekane impamvu mu gihe wemeje cyangwa wanze ubusabe bwa FA.",

    "LEAD_MSG_WITH_RM_ASSIGNED" => "$WELCOME_MSG_FLOW Uduhagarariye :rm_name azakugeraho utangire kwiyandikisha. Wamuhamagara kuri :rm_mobile_num.",

    "LEAD_MSG_WITHOUT_RM_ASSIGNED" => "$WELCOME_MSG_FLOW Uhagarariye Flow azakugeraho utangire kwiyandikisha. Wahamagara Flow kuri :cs_num.",

    "LEAD_MSG_BY_RM" => "$WELCOME_MSG_FLOW Uhagarariye Flow :rm_name aje kugusura nonaha. Ushobora kwandika nimero ye :rm_mobile_num.",

    "INVALID_OTP_SMS" => "[FLOW] Kode mwohereje yo kwemeza ntabwo ariyo. Mwahamagara kuri :cs_num.",

    "EXPIRED_OTP_SMS" => " [FLOW] Kode yo kwemeza mwohereje yarangije igihe. Mwahamagara kuri :cs_num mubone kode nshya.",

    "ALT_NUM_SMS_RESPONSE" => "[FLOW] Guhererekanya amafaranga ku butumwa bugufi bikorwa hifashishijwe nimero wiyandikishirijeho GUSA ariyo :reg_mobile_num.",

    "UNKNOWN_NUM_SMS_RESPONSE" => "[FLOW] Guhererekanya amafaranga ku butumwa bugufi bikorwa hifashishijwe nimero wiyandikishirijeho GUSA.",

    "WELCOME_ENABLED_MSG" => "$WELCOME_MSG_FLOW :cust_name!.Nimero ibaranga ya Flow ni :cust_id. Twishimiye gukorana namwe! Mwahamagara :customer_success niba mukeneye ubufasha.",

    "CUST_APP_MOBILE_CONFIRM" => "[FLOW] OTP yo kwemeza nimero ya telefoni kuri apulikasiyo ya FLOW ni :otp_code. Nyamuneka ntugire uwo uyisangiza. Murakoze.",

    "REPEAT_FA_MSG" => "[FLOW] Twakiriye ubusabe bwanyu bwa FA. Murakira ubutumwa bubabwira igikurikira.",

    "DISABLED_CUST_REPEAT_FA" => "$REPEAT_FA_FALIURE Konti yanyu ya FLOW irafunze. Duhamagare kuri :cs_num ku bindi bisobanuro.",

    "PENDING_FA_APPL_FA_REPEAT" => "$REPEAT_FA_FALIURE Mufite ubundi busabe butarakirwa. Duhamagare kuri :cs_num ku bindi bisobanuro.",

    "LAST_FA_OS_REPEAT_FA" => "[FLOW] Unable to REPEAT FLOAT ADVANCE(FA). If you have already repaid  your previous FA, please wait while we settle. Please call us at :cs_num for details.",

    "UNABLE_TO_REPEAT_FA" => "$REPEAT_FA_FALIURE Ntitubashije kwakira ubusabe bwanyu. Duhamagare kuri :cs_num ku bindi bisobanuro.",

    "MERCHANT_CHANGE_FA_REPEAT" => "$REPEAT_FA_FALIURE Mwahinduriye konti yanyu kuri :acc_number bityo ntimwasaba indi FA. Duhamagare kuri :cs_num utange ubundi busabe.",

    "CUST_AGGR_RENEWAL_MSG" => "Your agreement with Flow is expiring on :aggr_valid_upto. To get FAs without interruption please arrange/cooperate visit with RM for renewal",

    "PROB_CUST_AGGR_RENEWAL_MSG" => "Your agreement will be expiring soon. To get FAs without interruption please arrange/cooperate visit with RM for renewal" 
];