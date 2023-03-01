<?php
 
namespace App;

class Consts{

const ENABLED = 'enabled';

const RM = 'rm';
const CUSTOMER = 'customer';
const INDIVIDUAL = 'individual';
const FLOW_RM = 'flow_rm';
const DP_RM = 'data_prvdr_rm';
const CHAP_CHAP_AP_CODE = 'CCA';
const EZEEMONEY_AP_CODE = 'UEZM';
const MTN_AP_CODE = 'UMTN';
const BOK_AP_CODE = 'RBOK';
const RMTN_AP_CODE = 'RMTN';
const RRTN_AP_CODE = 'RRTN';
const RATL_AP_CODE = 'RATL';
const INSTITUTION = 'institutional';
const LOGO_H = 30;
const LOGO_W = 75;
const LOGO_RATIO = 1.25;
const NEW_CUST_ADDL_FACTOR = 75;
const REPEAT_CUST_ADDL_FACTOR = 40;
const LOAN_APPLIED = "submitted";
const LOAN_APPL_PNDNG_APPR = "pending_approval";
const LOAN_APPL_APPROVED = "approved";
const LOAN_APPL_REJECTED = "rejected";
const LOAN_APPL_CANCELLED = "cancelled";

const REPEAT_FA_REQUESTED = 'requested';
const REPEAT_FA_REJECTED = 'rejected';
const REPEAT_FA_PROCESSED = 'processed';

const PRIV_CODE_APPROVAL = "application/approval";
const PRIV_CODE_DISBURSAL = "loan/disburse";
const PRIV_CODE_REPAY = "loan/capture_repayment";

const PRIV_CODE_APPLIER = "application/apply";
const PRIV_CODE_EMAIL = "email_report/daily_due_loans";

const COUNTRY_CTN = " country_code = ?";

const STATUS_CTN = " status = ?";

const LOAN_APPL_STATUS = array(self::LOAN_APPLIED, self::LOAN_APPL_PNDNG_APPR , self::LOAN_APPL_APPROVED, "processed", self::LOAN_APPL_REJECTED);

const MATCHING_RECON_STATUS = "80_recon_done";
const PENDING_STMT_IMPORT = "72_pending_stmt_import";
const SMS_IMPORT_INPROGRESS = "in_progress";
const SMS_IMPORT_DONE = "done";
const SMS_CAPTURE = 'sms';

const LOAN_PNDNG_DSBRSL = "pending_disbursal";
const LOAN_ONGOING = "ongoing";
const LOAN_DUE = "due";
const LOAN_OVERDUE = "overdue";
const LOAN_SETTLED = "settled";
const LOAN_HOLD = "hold";
const LOAN_CANCELLED = "voided";
const LOAN_EVNT_DISBURSED = "disbursed";
const LOAN_EVENT_PART_PYMNT = "partial_payment";
const PENALTY_WAIVER = "penalty_waiver";
const LOAN_PENALTY_PAYMENT = "penalty_payment";
const LOAN_PAYMENT_REVERSED = "payment_reversed";
const LOAN_DISBURSAL_REVERSED = "disbursal_reversed";

const LOAN_TXN_DUPLICATE_DISBURSAL = "duplicate_disbursal";
const LOAN_TXN_DUPLICATE_DISBURSAL_RVRSL = "dup_disb_rvrsl";
const DATETIME_FORMAT = "Y-m-d";
const AGGR_ACTIVE = "active";
const AGGR_INACTIVE = "inactive";
const FLOAT_ID_DATE_FORMAT = "ymd";
const AGGR_DATE_TIME_FORMAT = "ymdHis";

const DSBRSL_WITH_CUST = "pending_with_customer";
const DSBRSL_PROCESSING = "sent_to_disbursal_queue";
const DSBRSL_IN_PROGRESS = "in_progress";
const DSBRSL_SUCCESS = "disbursed";
const DSBRSL_UNKNOWN = "unknown";
const DSBRSL_FAILED = "failed";
const DSBRSL_CPTR_FAILED = "pending_disb_capture";
const PNDNG_DSBRSL_MANUAL = "pending_mnl_dsbrsl";

const LOAN_TXN_DUP_PAYMENT = "duplicate_payment";
const LOAN_TXN_DUP_PAYMENT_REVERSAL = "duplicate_payment_reversal";

const LOAN_TXN_PAYMENT_DIFF_ACC_INT_TRANS = "payment_diff_acc_int_trans";
const LOAN_TXN_PAYMENT_DIFF_ACC = "payment_diff_acc";

const RCVRY_INIT = "pending_customer_otp";
const RCVRY_CNFM = "ready_to_collect";
const RCVRY_CNCL = 'cancelled';
const RCVRY_RCRD = 'collected_from_customer';

const REDEMPTION_INT_TRANSFER = '75_redemption_int_transfer';

const DATE_FORMAT = "Y-m-d";

const DB_DATETIME_FORMAT = "Y-m-d H:i:s";

const DB_DATE_FORMAT = "Y-m-d";

const UI_DATE_FORMAT = "d-M-Y";

const UI_DATETIME_FORMAT = "d-M-Y h:i A T (e)"; 
 
const LOAN_STATUS = array(self::LOAN_PNDNG_DSBRSL , self::LOAN_ONGOING , self::LOAN_DUE, self::LOAN_OVERDUE,  self::LOAN_SETTLED, self::LOAN_HOLD, self::LOAN_CANCELLED);

const ALLOWED_LAST_LOAN_STATUS = array(self::LOAN_SETTLED , self::LOAN_CANCELLED);

const DISBURSED_LOAN_STATUS = array(self::LOAN_OVERDUE , self::LOAN_DUE, self::LOAN_ONGOING);

//const LOAN_APPL_STATUS_CRITERIA = array(self::LOAN_APPL_PNDNG_APPR);

// const HOLIDAYS = array(
// 							"UGA" => ['2019-06-03', '2019-06-05', '2019-06-09', '2019-08-12', '2019-10-09', '2019-12-25', '2019-12-26']
// 							);

const HOLIDAYS = array(
"UGA" => ['2019-06-03', '2019-06-05', '2019-06-09', '2019-08-12', '2019-10-09', '2019-12-25', '2019-12-26',
'2020-01-01', '2020-01-26', '2020-02-16', '2019-03-08', '2020-04-10', '2020-04-13', '2020-05-01', '2020-05-24', 
'2020-06-03', '2020-06-09', '2020-07-31', '2020-10-09', '2020-12-25', '2020-12-26', '2020-10-08', 
'2021-01-01', '2021-01-14','2021-01-26','2021-02-16','2021-03-08','2021-04-02','2021-04-04','2021-04-05',
'2021-05-01','2020-05-13','2021-06-03','2021-06-09','2021-07-20','2021-10-09','2021-12-25','2021-12-26',
'2021-01-14','2021-01-15','2021-01-16','2021-01-18', '2021-02-03','2021-02-04','2021-05-12','2021-05-13',
'2022-01-01','2022-01-26','2022-02-16','2022-03-08','2022-04-15','2022-04-18','2022-05-01','2022-05-02','2022-06-03',
'2022-06-09','2022-07-09','2022-10-09','2022-12-25','2022-12-26', 
'2023-01-01', '2023-01-26', '2023-02-16', '2023-03-08', '2023-04-07', '2023-04-09', '2023-04-10', '2023-04-21',
'2023-05-01', '2023-06-03', '2023-06-09', '2023-06-28', '2023-10-09', '2023-12-25', '2023-12-26'],

"RWA" => ['2022-01-01','2022-01-03','2022-02-01','2022-04-07','2022-04-15','2022-04-18','2022-05-01','2022-05-02','2022-07-01',
'2022-07-04','2022-07-09','2022-07-11','2022-08-05','2022-08-15', '2022-12-25','2022-12-26', '2022-12-27']
);

const CCA_STMT_LOGIN  = ['703463210', 'Kyt8@AQVXqsAh69'];
       
#const UEZM_STMT_LOGIN = ['73457167','admin', 'F!0w#z3b!z','112233'];
#const UEZM_STMT_LOGIN = ['20718971','1CP@ghrul!'];  # Collection / Repayment

const UEZM_STMT_LOGIN = ['73457167','7@@i4NjWLpK37eL']; # Disbursal
        
const MODEL_ALIAS = array("borrower" => "Borrower", 
						"reference_borrower" => "Borrower",
						"add_lender" => "Borrower",
						"owner_person" => "Person" ,
						"cr_owner_person" => "Person" ,
						"cr_addl_num" => "Person" ,
						"biz_info" => "CRBizInfo" ,
						"biz_identity" => "CRBizIdentity" ,
						"cr_account" => "Account" ,
						"lender" => "Lender" , 
						"org" => "Org" , 
						"contact_persons" => "Person", 
						"relationship_manager" => "Person",
						"rel_mgr" => "Person",
						"credit_score_factor" => "CreditScoreFactor",
						"market" => "Market",
						"head_person" => "Person",
						"person" => "Person",
						"reg_address" => "AddressInfo",
						"biz_address" => "AddressInfo",
						"owner_address" => "AddressInfo",
						"address" => "AddressInfo",
						"account" => "Account",
						"acc_txn" => "AccountTxn",
						"acc_provider" => "AccProvider",
						"loan_product_template" => "LoanProductTemplate",
						"loan_product" => "LoanProduct",
					    "data_prvdr" => "DataProvider",
					    "data_provider" => "DataProvider",
					    "loan_application" => "LoanApplication",
					    "disbursal_txn" => "LoanTransaction",
					    "instant_disbursal_txn" => "LoanTransaction",
					    "repayment_txn" => "LoanTransaction",
					    "loan_approvers" =>"LoanApprover",
					    "loans" =>"Loan",
						"lead" => "Lead",
					    "loan_request" => "Loan",
					    "loan_reject" => "LoanApplication",
					    "loan_cancel" => "LoanApplication",
					    'market_addr_config' => "MarketAddressConfig",
					    'address_info' => "AddressInfo",
					    'loan_comments' =>"LoanComment",
					    'master_data' => "MasterData",
					    'master_data_key' => "MasterDataKeys",
					    'master_agreement' => "MasterAgreement",
						'loan_recovery' => "LoanRecovery",
						'partner_data' => "PartnerAccStmtRequests"
						);

const CONFIRM_CODE_ALERT = "An SMS with a unique confirmation code has been sent to the customer's registered mobile number. Customer should send back the confirmation code to :shortcode in the format 'FLOW <CODE>'";

const RECOVERY_OTP_INFO = ["An SMS with an OTP has been sent to the customer's registered mobile number.", "Customer should send back the OTP to :shortcode in the format 'FLOW <OTP>'"];

const TIMED_OUT_MSG = "Timed out waiting for response";

const ADDL_NUM_TMPLT = [
						'mobile_num' => ['value' => NULL,'type' => 'text','cmts' => []],
						'relation' => ['value' => NULL,'type' => 'dropdown','cmts' => []],
						'name' => ['value' => NULL,'type' => 'text','cmts' => []],
						'serv_prvdr' => ['value' => NULL,'type' => 'dropdown','cmts' => []]
						];

const LEAD_FILE_BASE_TMPLT = [
	'file_data' => null,
	'file_name' => null,
	"file_err" => null,
];
const LEAD_FILE_UPLOAD_TMPLT = [

'RMTN' =>	[
			"desc" => "Transaction Statement",
			"file_err" => null,
			"files" => [
				0 => Consts::LEAD_FILE_BASE_TMPLT +["file_of" => "txn_stmt", 
					"file_label" => "Transaction Statement", 
					"file_desc" => "Upload the last three month transaction statement of the lead's MTN A/C :account_num",
					"file_type" => ['application/xlsx', 'application/xls', 'application/csv', 'application/pdf']
					]
			]
		],


'RBOK' =>	[
			"desc" => "Transaction Statement & Commission Summary",
			"file_err" => null,
			"files" => [
							0 => Consts::LEAD_FILE_BASE_TMPLT + [	"file_of" => "txn_stmt", 
									"file_label" => "Transaction Statement", 
									"file_desc" => "Upload the last three month transaction statement of the lead's BK A/C :account_num in PDF format",
									"file_type" => ['application/pdf']
								],

							1 => Consts::LEAD_FILE_BASE_TMPLT + [	"file_of" => "month1_comm_sum", 
									"file_label" => "Month 1 Commission Summary", 
									"file_desc" => "Upload the :month1 month's commission summary of the lead's BK A/C :account_num in PDF format",
									"file_type" => ['application/pdf'],
								],
								
							2 => Consts::LEAD_FILE_BASE_TMPLT + [	"file_of" => "month2_comm_sum", 
									"file_label" => "Month 2 Commission Summary", 
									"file_desc" => "Upload the :month2 month's commission summary of the lead's BK A/C :account_num in PDF format",
									"file_type" => ['application/pdf']
								],
								
							3 => Consts::LEAD_FILE_BASE_TMPLT + [	"file_of" => "month3_comm_sum", 
									"file_label" => "Month 3 Commission Summary", 
									"file_desc" => "Upload the :month3 month's commission summary of the lead's BK A/C :account_num in PDF format",
									"file_type" => ['application/pdf']
								],

							4 => Consts::LEAD_FILE_BASE_TMPLT + [	"file_of" => "comm_stmt", 
									"file_label" => "Commission Statement", 
									"file_desc" => "Upload the last three month commission statement of the lead's BK A/C :account_num in PDF format",
									"file_type" => ['application/pdf']
							]
						]
	],

	'RATL' =>	[
		"desc" => "Transaction Statement",
		"file_err" => null,
		"files" => [
			0 => Consts::LEAD_FILE_BASE_TMPLT +["file_of" => "txn_stmt", 
				"file_label" => "Transaction Statement", 
				"file_desc" => "Upload the last three month transaction statement of the lead's Airtel A/C :account_num",
				"file_type" => ['application/xlsx']
				]
		]
	],

];

const CUST_REG_ARR = [
						'cust_id' => null,
						'same_as_biz_address' => false,
						'same_as_mobile_number' => false,
						'same_as_owner_person' => false,
						'is_templ' => true,
						'allow_biz_owner_manual_id_capture' => false,
						'allow_tp_ac_owner_manual_id_capture' =>false,
						'is_rented_line' => false,
						'biz_identity' => [
							'gps' => [
									'value' => NULL,
									'type' => 'image',
									'cmts' => []
									 ],
							'photo_biz_lic' => ['value' => NULL,
												'type' => 'image',
												'cmts' => []
						   						],
							'photo_biz_lic_full_path' => ['value' => NULL,
												'type' => 'image',
												'cmts' => []
						   						],
							'photo_shop' => ['value' => NULL,
											 'type' => 'image',
							                 'cmts' => []
						   					],
											   
							'photo_shop_full_path' => ['value' => NULL,
											 'type' => 'image',
							                 'cmts' => []
						   					],
							'mobile_num' => ['value' => NULL,
												'type' => 'text',
							                  'cmts' => []
							  				],
							'alt_biz_mobile_num_1' => ['value' => NULL,
														'type' => 'text',
						  								'cmts' => []
		  												],
							'alt_biz_mobile_num_2' => ['value' => NULL,
														'type' => 'text',
						  									'cmts' => []
		 												 ] 
							
						],
						'account' => [
							'acc_prvdr_code' => ['value' => NULL,
												'type' => 'text',
						  							'cmts' => []
		  										],
							'acc_number' => ['value' => NULL,
											'type' => 'text',
						  					'cmts' => []
		  									],
							'photo_new_acc_letter' => ['value' => NULL,
											  'type' => 'image',
											  'cmts' => []
											   ],
							'photo_new_acc_letter_full_path' => ['value' => NULL,
											   'type' => 'image',
											   'cmts' => []
												],
							
							'id' => NULL,
						],
						'owner_person' => [
							'national_id' => ['value' => NULL,
											  'type' => 'text',
						  					  'cmts' => []
		  									],
							'first_name' => ['value' => NULL,
							                 'type' => 'text',
						  	                 'cmts' => []
		  				                   ],
							'middle_name' => ['value' => NULL,
												'type' => 'text',
							  					'cmts' => []
			  									],
							'last_name' => ['value' => NULL,
											'type' => 'text',
						  					'cmts' => []
		  				                   ],
							'dob' => ['value' => NULL,
							          'type' => 'date',
						              'cmts' => []
		                             ],
							'gender' => ['value' => NULL,
							            'type' => 'radio',
						                'cmts' => []
		                                ],
							'photo_national_id' => ['value' => NULL,
										'type' => 'image',
										'cmts' => []
										 ],
							'photo_national_id_full_path' => ['value' => NULL,
										 'type' => 'image',
										 'cmts' => []
										  ],
							'photo_national_id_back' => ['value' => NULL,
										  'type' => 'image',
										  'cmts' => []
										   ],
							'photo_national_id_back_full_path' => ['value' => NULL,
										  'type' => 'image',
										  'cmts' => []
										   ],
										
							'photo_selfie' => ['value' => NULL,
							                  'type' => 'image',
						                      'cmts' => []
		                                       ],
							'photo_selfie_full_path' => ['value' => NULL,
											   'type' => 'image',
											   'cmts' => []
												],
							'photo_pps' => ['value' => NULL,
							                  'type' => 'image',
						                      'cmts' => []
		                                       ],
							'photo_pps_full_path' => ['value' => NULL,
											   'type' => 'image',
											   'cmts' => []
												],
							'email_id' => ['value' => NULL,
							              'type' => 'text',
						                  'cmts' => []
		                                  ],
							'whatsapp' => ['value' => NULL,
							              'type' => 'text',
						                   'cmts' => []
		                                  ],
						],
						'biz_info' => [
							'biz_name' => ['value' => NULL,
							               'type' => 'text',
						                   'cmts' => []
		                                   ],
							'biz_addr_prop_type' => ['value' => NULL,
										   'type' => 'dropdown',
										   'cmts' => []
										  ],
							'business_distance' => ['value' => NULL,
							                        'type' => 'dropdown',
						                            'cmts' => []
		                                           ],
							'ownership' => ['value' => NULL,
							                'type' => 'radio',
						                    'cmts' => []
		                                   ],
							'territory' => ['value' => NULL,
							                'type' => 'dropdown',
						                    'cmts' => []
		                                   ],
							'dp_rel_mgr_id' => ['value' => NULL,
							                    'type' => 'text',
						                        'cmts' => []
		  										],
						],
						'partner_kyc' => [
							'UEZM' => [
								'UEZM_MainContent_txtAbbreviationName' => ['value' => NULL,
								                                           'type' => 'dropdown',
							                                               'cmts' => []
			  																],
								'UEZM_MainContent_txtCompanyRegistrationNo' => ['value' => NULL,
								                                                'type' => 'dropdown',
							                                                     'cmts' => []
			 																	 ],
								'UEZM_MainContent_ddlNatureOfBusiness' => ['value' => NULL,
								                                           'type' => 'dropdown',
							                                               'cmts' => []
			                                                              ],
								'UEZM_MainContent_ddOperatedBy' => ['value' => NULL,
								                                    'type' => 'dropdown',
							                                        'cmts' => []
			                                                       ],
								'UEZM_MainContent_ddlBankCode' => ['value' => NULL,
								                                   'type' => 'dropdown',
							                                        'cmts' => []
			                                                      ],
								'UEZM_MainContent_txtBankAccNo' => ['value' => NULL,
								                                   'type' => 'dropdown',
							                                        'cmts' => []
			                                                        ],
								'UEZM_MainContent_txtBankAccName' => ['value' => NULL,
								                                      'type' => 'dropdown',
							                                           'cmts' => []
			                                                         ],
								'UEZM_MainContent_ddWallet' => ['value' => NULL,
								                               'type' => 'dropdown',
							                                   'cmts' => []
			                                                    ],
								'UEZM_MainContent_txtRecruiterID' => ['value' => NULL,
								                                     'type' => 'dropdown',
							                                         'cmts' => []
			                                                        ],
								'UEZM_MainContent_ddlZone' => ['value' => NULL,
								                               'type' => 'dropdown',
							                                    'cmts' => []
			                                                   ],
								'UEZM_MainContent_txtLC1' => ['value' => NULL,
								                               'type' => 'text',
							                                    'cmts' => []
			                                                   ],
							]
						],
						'references' => [
							0 => [
								'ref_type' => 'guarantor',
								
								'guarantor1_name' => ['value' => NULL,
													  'type' => 'text',
													  'cmts' => []
							   						],
								'guarantor1_doc' => ['value' => NULL,
													 'type' => 'image',
													 'cmts' => []
							  						 ],
								'guarantor1_doc_full_path' => ['value' => NULL,
																'type' => 'image',
																'cmts' => []
							  								  ],
								],
							1 => [
								'ref_type' => 'guarantor',
								
								'guarantor2_name' => ['value' => NULL,
													  'type' => 'text',
													  'cmts' => []
							   						],
								'guarantor2_doc' => ['value' => NULL,
													 'type' => 'image',
													 'cmts' => []
							  						 ],
								'guarantor2_doc_full_path' => ['value' => NULL,
																'type' => 'image',
																'cmts' => []
							  								  ],

							],
							2 => [
								'ref_type' => 'lc_letter',
								
								'lc_name' => ['value' => NULL,
													  'type' => 'text',
													  'cmts' => []
							   						],
								'lc_doc' => ['value' => NULL,
													 'type' => 'image',
													 'cmts' => []
							  						 ],
								'lc_doc_full_path' => ['value' => NULL,
																'type' => 'image',
																'cmts' => []
							  								  ],
							],
						],
						'biz_address' => [],
						'owner_address' => [],
						'contact_persons' => [
							0 => [
								'first_name' => ['value' => NULL,
												'type' => 'text',
							  					'cmts' => []
			  									],
								'middle_name' => ['value' => NULL,
												'type' => 'text',
							  					'cmts' => []
			  									],
								'last_name' => ['value' => NULL,
												'type' => 'text',
							  					'cmts' => []
			  									],
								'dob' => ['value' => NULL,
											'type' => 'date',
							  				'cmts' => []
			  								],	
								'gender' => ['value' => NULL,
											'type' => 'text',
							  				'cmts' => []
			  								],
								'national_id' => ['value' => NULL,
											  'type' => 'text',
						  					  'cmts' => []
		  									],
								'photo_national_id' => ['value' => NULL,
											  'type' => 'image',
											  'cmts' => []
											   ],
								'photo_national_id_full_path' => ['value' => NULL,
											  'type' => 'image',
											  'cmts' => []
											   ],
								'photo_national_id_back' => ['value' => NULL,
											   'type' => 'image',
											   'cmts' => []
												],
								'photo_national_id_back_full_path' => ['value' => NULL,
												'type' => 'image',
												'cmts' => []
												 ],
								'photo_selfie' => ['value' => NULL,
													'type' => 'image',
							 						 'cmts' => []
			  										],
								'photo_selfie_full_path' => ['value' => NULL,
													  'type' => 'image',
														'cmts' => []
														],
								'photo_pps' => ['value' => NULL,
														'type' => 'image',
														  'cmts' => []
														  ],
								'photo_pps_full_path' => ['value' => NULL,
														  'type' => 'image',
															'cmts' => []
															],
								'email_id' => ['value' => NULL,
												'type' => 'text',
							  					'cmts' => []
			 									 ],
								'whatsapp' => ['value' => NULL,
												'type' => 'text',
							  					'cmts' => []
			  									],
								'mobile_num' => ['value' => NULL,
												'type' => 'text',
							                  'cmts' => []
							  				],
								'alt_biz_mobile_num_1' => ['value' => NULL,
														'type' => 'text',
						  								'cmts' => []
		  												],
								'alt_biz_mobile_num_2' => ['value' => NULL,
														'type' => 'text',
						  									'cmts' => []
		 												 ],
								'handling_biz_since' => ['value' => NULL,
														'type' => 'date',
							  							'cmts' => []
			 											 ],
								'relation_with_owner' => ['value' => NULL,
														  'type' => 'dropdown',
							                               'cmts' => []
			  											],
								'contact_address' => [],
							],
						],
						'agreements' => []
						// 'addl_num' => [  Consts::ADDL_NUM_TMPLT,
						// 				 Consts::ADDL_NUM_TMPLT,
						// 				 Consts::ADDL_NUM_TMPLT
					   	// 				]
					];

const THIRD_PARTY_OWNER_DETAILS =  [
	
								'national_id' => ['value' => NULL,
												'type' => 'text',
													'cmts' => []
												],
								'first_name' => ['value' => NULL,
												'type' => 'text',
												'cmts' => []
												],
								'middle_name' => ['value' => NULL,
													'type' => 'text',
													'cmts' => []
													],
								'last_name' => ['value' => NULL,
												'type' => 'text',
												'cmts' => []
												],
								'dob' => ['value' => NULL,
										'type' => 'date',
										'cmts' => []
										],
								'gender' => ['value' => NULL,
											'type' => 'radio',
											'cmts' => []
											],
								'photo_national_id' => ['value' => NULL,
											'type' => 'image',
											'cmts' => []
											],
								'photo_national_id_full_path' => ['value' => NULL,
											'type' => 'image',
											'cmts' => []
											],
								'photo_national_id_back' => ['value' => NULL,
											'type' => 'image',
											'cmts' => []
											],
								'photo_national_id_back_full_path' => ['value' => NULL,
											'type' => 'image',
											'cmts' => []
											],
								'photo_consent_letter' => ['value' => NULL,
											'type' => 'image',
											'cmts' => []
											],
								'photo_consent_letter_full_path' => ['value' => NULL,
											'type' => 'image',
											'cmts' => []
											],
											
											
										];

const PARTNER_RESP_CODES = [ 	
								9999 => "UNKNOWN ERROR",
								1000 => "SUCCESS", 
								1001 => "AGENT NOT ENABLED",
								1002 => "NO SUCH AGENT PROFILE EXIST", 
								1003 => "AGENT ID MISSING IN REQUEST",
								2001 => "PRODUCT DOES NOT EXIST OR NOT ENABLED", 
								3001 => "ANOTHER APPLICATION EXIST",
								3002 => "ANOTHER FLOAT ADVANCE EXIST",
								3003 => "NO EXISTING FLOAT ADVANCE TO APPLY A TOPUP",
								4001 => "NO FLOAT ADVANCE RECORD FOUND",
								4002 => "NO OUTSTANDING FLOAT ADVANCE EXIST FOR AGENT",
								5001 => "REPAY NOTIFICATION ALREADY RECEIVED",
								5002 => "FLOW REQ ID IS NOT VALID",
								5003 => "REPAYMENT CAPTURED ALREADY FOR A DIFFERENT FLOAT ADVANCE",
								5004 => "CANNOT REPAY BEFORE DISBURSAL",
								5005 => "CANNOT REPAY WHEN FLOAT ADVANCE IS NOT OUTSTANDING",
								5006 => "CANNOT REPAY A HIGHER AMOUNT",
								5007 => "CANNOT MAKE PART PAYMENT",
								5008 => "CANNOT MAKE PART PAYMENT WITHOUT PENALTY",
								5009 => "CANNOT MAKE EXCESS PENALTY PAYMENT",
								5010 => "REPAYMENT ACCOUNT DOESN'T EXIST",
								6001 => "MISSING AUTH PARAMS",
								6002 => "INVALID AUTH PARAMS",
								6003 => "MISSING OR INVALID FIELDS IN REQUEST",
								8001 => "NO PAST FLOAT ADVANCE RECORD EXIST",
								9001 => "DECRYPTION ERROR"
							];
const PENDING_PRODUCT_SEL =	'07_pending_prod_sel';							
const PENDING_RM_EVAL = '10_pending_rm_eval';
const PENDING_RM_ALLOC = '09_pending_rm_alloc';
const RM_REJECTED = '20_rm_rejected';
const PENDING_DATA_CONSENT = '21_pending_data_consent';
const PENDING_DATA_UPLOAD = '22_pending_data_upload';
const PENDING_DATA_PROCESS = '23_pending_data_process';
const INELIGIBLE_CUST = '24_ineligible';
const CUST_NOT_INTERESTED = '30_cust_not_interested';
const PENDING_KYC = '40_pending_kyc';
const KYC_INPROGRESS = '41_kyc_inprogress';
const RETRIEVE_HOLDER_NAME = '42_retrieve_holder_name';
const PENDING_MOBILE_NUMBER_VER = '43_pending_mobile_num_ver';
const PENDING_AUDIT = '50_pending_audit';
const KYC_FAILED = '52_kyc_failed';
const PENDING_ENABLE = '59_pending_enable';
const CUSTOMER_ONBOARDED = '60_customer_onboarded';
const PENDING_DOWNPAYMENT = '08_pending_dp';
const TF_PENDING_PRODUCT_SEL = 'tf_01A_pending_prod_sel';
const TF_PENDING_PROCESS = '51_pending_tf_process' ;
const TF_PENDING_POS_TO_RM = 'tf_40_pending_pos_to_rm';
const TF_PENDING_POS_TO_CUST = 'tf_50_pending_pos_to_cust';
const TF_PENDING_DOWNPAYMENT = 'tf_01_pending_dp';
const TF_PENDING_DOWNPAYMENT_VER = 'tf_01_pending_dp_ver';
const TF_PENDING_KYC = 'tf_02_pending_flow_kyc';
const TF_PENDING_SC_GEN = 'tf_10_pending_sc_gen';
const TF_PENDING_FLOW_DISB = 'tf_30_pending_flow_disb';
const TF_PENDING_REPAY_CYCLE = 'tf_50_pending_repay_cycle';

#SC - Score Status
const SC_PENDING_DATA_UPLOAD = 'pending_data_upload';
const SC_PENDING_DATA_PROCESS = 'pending_data_process';
const SC_SCORED = 'scored';

#PS - Partner Status
const PS_PENDING = 'pending';
const PS_INELIGIBLE = 'assessment_failed';
const PS_ELIGIBLE = 'assessment_passed';
const PS_KYC_FAILED = 'kyc_failed';
const PS_KYC_PASSED = 'kyc_passed';
const PS_ONBOARDED = 'onboarded';

const UPGRADE_REQUEST = 'requested';

const TASK_REQUESTED = 'requested';
const TASK_APPROVED = 'approved';
const TASK_REJECTED = 'rejected';



// Lead Actions
const LA_KYC_SUBMITTED = 'submit_kyc';
const LA_AUDIT_INITIATED = 'initiate_audit';
const LA_KYC_REASSIGNED = 'reassign_kyc';
const LA_KYC_REJECTED = 'reject_kyc';
const LA_REASSIGN_AUDIT = 'reassign_audit';
const LA_MOB_NUM_VERF = 'mobile_num_verify';

const LEAD_ACTIONS = [
	self::LA_KYC_SUBMITTED => "KYC Submitted by :cmtr_name", 
	self::LA_AUDIT_INITIATED => "KYC Audit Initiated by :cmtr_name", 
	self::LA_KYC_REASSIGNED => "KYC Assigned to RM by :cmtr_name", 
	self::LA_KYC_REJECTED => "KYC Rejected by :cmtr_name",
	self::LA_REASSIGN_AUDIT => "KYC Audit Reassigned to :cmtr_name",
	self::LA_MOB_NUM_VERF => "KYC Submitted for Mobile Number Verfication by :cmtr_name"
];

const AVAIL_STATUS = 'available';
const UNAVAIL_STATUS = 'unavailable';
const START_TRACKING_STATUS = 'started';
const STOP_TRACKING_STATUS = 'stopped';
const PRE_APPR_RMVE_OD_DAYS_THRESHOLD = 1;
}


?>
