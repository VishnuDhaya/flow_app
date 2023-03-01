import pandas as pd
from flow_common import db_read_only_str,db_rep_str,load_env,STATUSES_BEFORE_DISBURSAL,WRITE_OFF_STATUS
from sqlalchemy import create_engine
import sys
import datetime as dt
from dateutil.relativedelta import relativedelta
from reports_common import get_ignore_written_off_condn, map_ap_to_country, extract_from_query, get_float_vend_products

load_env()

db = create_engine('mysql+mysqlconnector://'+db_read_only_str('STMT'))
db_rep = create_engine('mysql+mysqlconnector://'+db_rep_str('STMT'))


def get_range(year):
    now = dt.datetime.now()
    start = year * 100 + 1
    if year == now.year:
        end = year * 100 + now.month
    else:
        end = year * 100 + 12

    return start,end

def rep_table_exists():
    
    table_list = pd.read_sql(con=db_rep, sql="SHOW TABLES").iloc[:,0].tolist()
    if 'finance_reports' not in table_list:
        sql = "CREATE TABLE finance_reports (\
              id int unsigned NOT NULL AUTO_INCREMENT,\
              month bigint,\
              country varchar(5),\
              acc_prvdr_code varchar(5),\
              tot_fee_rcvd decimal,\
              tot_penalty_rcvd decimal,\
              tot_fee_os decimal,\
              tot_cust_disb decimal,\
              tot_repaid decimal,\
              repayments_count int,\
              tot_principal_os_cust decimal,\
              tot_float_vending_os decimal,\
              PRIMARY KEY (id)\
              )"
        db_rep.execute(sql)


def run(year):
    start,end = get_range(year)
    rep_table_exists()
    for month in range(start,end+1):
        generate_report(month)


def get_wallet_balance(month, acc_prvdr_code):
    acc_query = f"select id from accounts where to_recon is true and JSON_CONTAINS(acc_purpose, JSON_ARRAY('disbursement')) and status='enabled' and network_prvdr_code='{acc_prvdr_code}'"
    acc_df = pd.read_sql(acc_query, db)
    if len(acc_df) > 1 or len(acc_df) == 0:
        return None
    balance_query = f"select balance from account_stmts where account_id = {acc_df['id'][0]} and EXTRACT(YEAR_MONTH from stmt_txn_date) = {month} and balance != 0 order by stmt_txn_date desc limit 1"
    balance = extract_from_query(balance_query, db).get('balance')
    return balance


def generate_report(month):
    

    db_rep.execute(f"DELETE FROM finance_reports where month = {month}")
    db.execute(f"set @month = '{month}'")

    country_codes = map_ap_to_country(db)

    fv_products = get_float_vend_products(db)
    in_fv = f"in {fv_products}"
    not_in_fv = f"not in {fv_products}"

    for country_code, acc_prvdr_codes in country_codes.items():
        last_day_of_month = (dt.datetime.strptime(str(month),"%Y%m") + relativedelta(day=31)).strftime("%Y-%m-%d")
        ignore_write_off_condn = get_ignore_written_off_condn(country_code, last_day_of_month, db, alias="{0}")

        for acc_prvdr_code in acc_prvdr_codes:


            fee_pen_rcvd_query = f"""select sum(fee) tot_fee_rcvd, sum(penalty) tot_pen_rcvd 
                                    from loans l, loan_txns t 
                                    where l.loan_doc_id = t.loan_doc_id and acc_prvdr_code = '{acc_prvdr_code}'
                                    and EXTRACT(YEAR_MONTH FROM txn_date) = @month and status not in {STATUSES_BEFORE_DISBURSAL}  and 
				                    product_id {not_in_fv}"""

            fee_pen_rcvd_df = extract_from_query(fee_pen_rcvd_query,db)
            fee_rcvd = fee_pen_rcvd_df.get('tot_fee_rcvd')
            penalty_rcvd = fee_pen_rcvd_df.get('tot_pen_rcvd')


            fee_os_query = f"""SELECT SUM(IF(due_fee - COALESCE(partially_paid_fee, 0) < 0, 0, due_fee - COALESCE(partially_paid_fee, 0))) AS tot_fee_os
                                FROM (
                                SELECT loan_doc_id, flow_fee AS due_fee,
                                        (SELECT SUM(fee)
                                        FROM loan_txns
                                        WHERE loan_doc_id = l.loan_doc_id AND txn_type = 'payment' AND
                                                EXTRACT(YEAR_MONTH FROM txn_date) <= @month) AS partially_paid_fee
                                FROM loans l
                                WHERE acc_prvdr_code = '{acc_prvdr_code}' AND EXTRACT(YEAR_MONTH FROM disbursal_Date) <= @month AND
                                        (EXTRACT(YEAR_MONTH FROM paid_date) > @month OR paid_date IS NULL) AND
                                        status NOT IN {STATUSES_BEFORE_DISBURSAL} AND
                                        product_id {not_in_fv}
                                        {ignore_write_off_condn.format('l.')}
                                ) loans_with_fees
                                """
            print(fee_os_query)

            fee_os = extract_from_query(fee_os_query, db).get('tot_fee_os')

            disb_query = f"select @month month,  l.acc_prvdr_code, count(amount) count, txn_type, sum(amount) amount from loans l, loan_txns t \
                                where l.loan_doc_id = t.loan_doc_id and  l.acc_prvdr_code = '{acc_prvdr_code}' and \
                                EXTRACT(YEAR_MONTH FROM txn_date) = @month and \
                                status not in {STATUSES_BEFORE_DISBURSAL} and \
                                product_id {not_in_fv} and txn_type = 'disbursal'"
            disb_data = extract_from_query(disb_query, db)
            tot_disbursed = disb_data.get('amount') if disb_data.get('amount') else 0


            repaid_query = f"""select @month month,  l.acc_prvdr_code, count(amount) count, txn_type, sum(ifnull(principal,0) + ifnull(fee,0) + ifnull(penalty,0) + ifnull(excess,0)) amount from loans l, loan_txns t 
                                where l.loan_doc_id = t.loan_doc_id and  l.acc_prvdr_code = '{acc_prvdr_code}' and 
                                EXTRACT(YEAR_MONTH FROM txn_date) = @month and 
                                status not in {STATUSES_BEFORE_DISBURSAL} and 
                                product_id {not_in_fv} and txn_type = 'payment'"""

            repaid_data = extract_from_query(repaid_query, db)
            tot_repaid = repaid_data.get('amount') if repaid_data.get('amount') else 0
            repay_count = repaid_data.get('count') if repaid_data.get('count') else 0

            principal_query = "select l.loan_doc_id, loan_principal principal from loans l\
                               where l.acc_prvdr_code = '{1}' and EXTRACT(YEAR_MONTH FROM disbursal_Date) <= @month\
                               and ( EXTRACT(YEAR_MONTH FROM paid_date) > @month or paid_date is null )\
                               and status not in {2} and product_id {0}\
                               {3}"


            partial_pay_query = "select l.loan_doc_id, sum(amount) partial_pay from loans l, loan_txns t\
                                 where l.acc_prvdr_code = '{1}' and l.loan_doc_id = t.loan_doc_id\
                                 and EXTRACT(YEAR_MONTH FROM disbursal_Date) <= @month\
                                 and (EXTRACT(YEAR_MONTH FROM paid_date) > @month or paid_date is null)\
                                 and EXTRACT(YEAR_MONTH FROM txn_date) <= @month and txn_type = 'payment' \
                                 and status not in {2} and product_id {0}\
                                 {3} group by l.loan_doc_id"

            os_query = f"select sum(IF(principal - IFNULL(partial_pay,0) <0, 0, principal - IFNULL(partial_pay,0))) os\
                         from ({principal_query}) pri left join ({partial_pay_query}) pp\
                         on pri.loan_doc_id = pp.loan_doc_id"

            tot_principal_os_query = os_query.format(not_in_fv,acc_prvdr_code,STATUSES_BEFORE_DISBURSAL,ignore_write_off_condn.format('l.'))
            tot_principal_os = extract_from_query(tot_principal_os_query, db).get('os')


            tot_float_vend_os_query =  os_query.format(in_fv,acc_prvdr_code,STATUSES_BEFORE_DISBURSAL,ignore_write_off_condn.format('l.'))

            tot_float_vend_os = extract_from_query(tot_float_vend_os_query, db).get('os')


            wallet_balance = get_wallet_balance(month, acc_prvdr_code)

            table_data = {'month': [month], 'country': [country_code], 'acc_prvdr_code': [acc_prvdr_code], 'wallet_balance': [wallet_balance],
                        'tot_fee_rcvd': [fee_rcvd], 'tot_penalty_rcvd': [penalty_rcvd], 'tot_fee_os': [fee_os], 
                        'tot_cust_disb': [tot_disbursed], 'tot_repaid': [tot_repaid], 'repayments_count': [repay_count], 
                        'tot_principal_os_cust': [tot_principal_os], 'tot_float_vending_os': [tot_float_vend_os]}

            table_record = pd.DataFrame(table_data)
            table_record.fillna(0, inplace=True)
            table_record.to_sql(con=db_rep, name='finance_reports', index=False, if_exists='append')




year = int(sys.argv[1]) if len(sys.argv)>1 else dt.datetime.now().year
run(year)