
update borrowers set tot_loan_appls=0,tot_loans=0,tot_default_loans=0,late_loans=0,late_1_day_loans=0,late_2_day_loans=0,late_3_day_loans=0,late_3_day_plus_loans=0,number_of_tills=0,first_loan_date=NULL;

update borrowers b set csf_run_id = (select run_id from cust_csf_values c where c.data_prvdr_cust_id = b.data_prvdr_cust_id order by run_id desc limit 1);

update loans l set paid_date = (select txn_date from loan_txns t  where t.loan_doc_id = l.loan_doc_id and t.txn_type = 'payment' order by t.txn_date desc limit 1 );

update app_users set status = 'disabled' where id in (1,7,9,8,10);

update app_users set country_code = '*' where id = 4;

update loan_products set product_code = 'EM13', product_name = 'EM13' where id = 18;	

update loan_products set penalty_amount = 0 where id in (17,50);

update loan_products set penalty_amount = 15000 where id in (14, 18, 19,23,24,25,40,41,42);

update loan_products set penalty_amount = 10000 where id in (4, 5, 9, 10, 11, 16,51,30,31,37,38,39);

update loan_products set penalty_amount = 5000 where id in (1, 2, 3, 6, 7, 8, 15,27,28,29,34,35,36);

update master_data set created_at = now();

update  master_data set country_code = '*' where data_key  = 'transaction_mode';

update  accounts set acc_purpose ='disbursement', type = 'wallet'  where id = 3;

update loan_txns set from_ac_id = 3 where from_ac_id = 4;

update loan_txns set to_ac_id = 3 where to_ac_id = 4;

update accounts set lender_data_prvdr_code = 'UEZM' where id = 3;

delete from accounts where id = 4;

update accounts set is_primary_acc = 1 where id = 3;

update app_users set person_id = 13 where id = 6;

update data_prvdrs set cust_comm = 25000, repay_comm = 500 where data_prvdr_code = 'UEZM';

update borrowers b set first_loan_date =  (select disbursal_date from loans l where l.cust_id = b.cust_id order by disbursal_date asc limit 1);

update borrowers b set last_loan_date = (select disbursal_date from loans l where  l.cust_id = b.cust_id order by disbursal_date desc limit 1);

update borrowers b set reg_date = first_loan_date where reg_date is null;

update loan_txns set to_ac_id = from_ac_id, from_ac_id = NULL where to_ac_id is NULL and from_ac_id is NOT NULL and txn_type='payment';

update borrowers b set ongoing_loan_doc_id = (select loan_doc_id from loans l where l.status not in('settled', 'pending_disbursal') and l.cust_id = b.cust_id order by id limit 1);

update borrowers b set is_og_loan_overdue = (select 1 from loans l where l.status = 'overdue' and l.cust_id = b.cust_id order by id limit 1);

UPDATE borrowers SET data_prvdr_cust_id="06292783" Where data_prvdr_cust_id = "6292783";

update app_users set country_code='*' where email='michael@consultcolors.com';

update orgs set country_code="UGA" where id=1;

update loan_txns set txn_mode="internet_banking" where txn_mode="net_banking";

delete from borrowers where cust_id="UEZM-599495" and id="877";

update borrowers set number_of_tills=0 where number_of_tills is NULL;

update borrowers set tot_loan_appls=0,tot_loans=0,tot_default_loans=0,late_loans=0,late_1_day_loans=0,late_2_day_loans=0,late_3_day_loans=0,late_3_day_plus_loans=0,first_loan_date=NULL;

update accounts set balance =0 where id= 3;

