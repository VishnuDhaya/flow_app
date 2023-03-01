import pandas as pd
import mysql.connector
from pandas.io import sql
from flow_common import db_read_only_str,db_rep_str,load_env,env
from reports_common import get_current_forex, map_fund_to_country
from sqlalchemy import create_engine
import requests
import traceback

load_env()
db_eng = create_engine('mysql+mysqlconnector://'+db_read_only_str('STMT'))
db_rep_eng = create_engine('mysql+mysqlconnector://'+db_rep_str("STMT"))

db = db_eng.connect()
db_rep = db_rep_eng.connect()

def table_exists():
    table_list = pd.read_sql(con=db_rep, sql="SHOW TABLES").iloc[:,0].tolist()
    if 'client_performance_funds' not in table_list:
            sql = f"CREATE TABLE client_performance_funds (\
                id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,\
                `fund_code` varchar(20),\
                `country_code` varchar(5),\
                `gender` varchar(12),\
                `acc_prvdr_code` varchar(5),\
                `acc_number` varchar(20),\
                `biz_name` varchar(80),\
                 `ongoing_loan` bigint DEFAULT NULL,\
                 `rel_mgr_name` varchar(80),\
                `total_fa` int DEFAULT NULL,\
                `total_amt` double(15,3) DEFAULT NULL,\
                `total_fee` double(15,2) DEFAULT NULL,\
                 `total_fee_usd` double(5,2) DEFAULT NULL,\
                `per_adv_revenue` decimal(5,2) DEFAULT NULL,\
                `avg_fa_size` double(15,2) DEFAULT NULL,\
                 `avg_fa_duration` decimal(5,2) DEFAULT NULL,\
                 `assume_income` decimal(7,2) DEFAULT NULL,\
                 `total_late_loans` int,\
                 `total_late_loans_percent` decimal(5,2) DEFAULT NULL,\
                 `fee_per_income` decimal(2,2) DEFAULT NULL)"

            db_rep.execute(sql)



def fetch():
    table_exists()
    db_rep.execute("TRUNCATE TABLE client_performance_funds")
    forex = get_current_forex('USD','UGX', db)
    countries = map_fund_to_country(db)
    for country, funds in countries.items():
        borrower_query = f"select acc_prvdr_code, acc_number,cust_id,ongoing_loan_doc_id,biz_name, owner_person_id,dp_rel_mgr_id  from borrowers where country_code = '{country}'"
        borrower_df = pd.read_sql(sql=borrower_query, con=db)
        
        for index, borrower in borrower_df.iterrows():
            for fund_code in funds:




                    print(borrower['owner_person_id'])
                    gender_query = f"select gender from persons where id = '{borrower['owner_person_id']}'"
                    gender_df = pd.read_sql(sql=gender_query, con=db)

                    dp_rel_mgr_query = f"select first_name, IFNULL(last_name, '') as last_name from persons where id = '{borrower['dp_rel_mgr_id']}'"
                    dp_rel_mgr_df = pd.read_sql(sql=dp_rel_mgr_query, con=db)
                    rel_mgr_name = dp_rel_mgr_df['first_name'][0] + dp_rel_mgr_df['last_name'][0]

                    loan_details_query = f"select sum(duration) as duration, count(duration) as count_duration, count(loan_doc_id) as total_fa,\
                                           sum(loan_principal) as total_amt, sum(flow_fee) as total_fee, sum(if(paid_date > due_date, 1, 0)) as total_late_loans \
                                           from loans where cust_id = '{borrower['cust_id']}' and fund_code = '{fund_code}'"

                    loan_details_df = pd.read_sql(sql=loan_details_query, con=db)

                    borrower['ongoing_loan_doc_id'] = 1 if borrower['ongoing_loan_doc_id'] is not None else 0

                    check_nan = loan_details_df['total_amt'].isnull().any()

                    if not(check_nan):
                        total_fee_usd = loan_details_df['total_fee'][0]/forex if loan_details_df['total_fee'][0] is not None else 0

                        per_adv_revenue = total_fee_usd / loan_details_df['total_fa'][0]

                        avg_fa_size = 0 if ( (loan_details_df['total_fa'][0] is None) or (loan_details_df['total_amt'][0] is None)  ) \
                                        else loan_details_df['total_amt'][0] / loan_details_df['total_fa'][0]  

                        avg_fa_duration = loan_details_df['duration'][0] / loan_details_df['count_duration'][0]

                        assume_income= (avg_fa_size *avg_fa_duration * 0.015 * loan_details_df['total_fa'][0])/forex

                        total_late_loans_percent = loan_details_df['total_late_loans'][0]/loan_details_df['total_fa'][0]\
                                                    if loan_details_df['total_late_loans'][0] != 0 else 0

                        fee_per_income = total_fee_usd/assume_income if total_fee_usd != 0 else 0

                    


                        record = {'fund_code': [fund_code], 
                                'country_code': [country], 
                                'gender': [gender_df['gender'][0]],
                                'acc_prvdr_code': [borrower['acc_prvdr_code']],
                                'acc_number': [borrower['acc_number']],
                                'biz_name': [borrower['biz_name']],
                                'ongoing_loan': [borrower['ongoing_loan_doc_id']],
                                'rel_mgr_name': [rel_mgr_name], 
                                'total_fa': [loan_details_df['total_fa'][0]],
                                'total_amt': [loan_details_df['total_amt'][0]], 
                                'total_fee': [loan_details_df['total_fee'][0]],
                                'total_fee_usd': [total_fee_usd],
                                'per_adv_revenue': [per_adv_revenue],
                                'avg_fa_size': [avg_fa_size],
                                'avg_fa_duration': [avg_fa_duration],
                                'assume_income': [assume_income],
                                'total_late_loans': [loan_details_df['total_late_loans'][0]],
                                'total_late_loans_percent': [total_late_loans_percent],
                                'fee_per_income': [fee_per_income] }
                        record_df = pd.DataFrame(record)
                        record_df.to_sql('client_performance_funds', index=False, con=db_rep, if_exists='append')






                    

try:
    transaction = db_rep.begin()
    fetch()
    transaction.commit()
except Exception as e:
    transaction.rollback()
    traceback.print_exc()

db.close()
db_rep.close()

db_eng.dispose()
db_rep_eng.dispose()
