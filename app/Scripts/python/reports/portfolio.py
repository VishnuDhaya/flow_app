env = {}
import pandas as pd
import calendar
import  datetime
import mysql.connector
from dateutil.relativedelta import relativedelta
from flow_common import DISBURSED, db_str,db_rep_str,load_env,WRITE_OFF_STATUS
from reports_common import get_ignore_written_off_condn, map_fund_to_country, map_ap_to_country, get_float_vend_products
from sqlalchemy import create_engine
import sys
import traceback




load_env()
PAR_DAYS = [5,10,15,30,60,90,120,180,270]
db_eng = create_engine('mysql+mysqlconnector://'+db_str('STMT'))
db_rep_eng = create_engine('mysql+mysqlconnector://'+db_rep_str('STMT'))
db = db_eng.connect()
db_rep = db_rep_eng.connect()
#db_rep.execute("DELETE FROM portfolio_risks")


def table_exists(table):
    table_list = pd.read_sql(con=db_rep, sql="SHOW TABLES").iloc[:,0].tolist()
    if table not in table_list:
        if table == 'portfolio_risks':
            sql = f"CREATE TABLE {table} (\
                id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,\
                country_code varchar(5),\
                acc_prvdr_code varchar(5) DEFAULT NULL,\
                par_loan_principal double DEFAULT 0,\
                par_days bigint DEFAULT 0,\
                date text,\
                partial_paid double DEFAULT 0,\
                fee double DEFAULT 0,\
                percentage double DEFAULT 0,\
                total_os_principal double DEFAULT 0\
                )"

        elif table == 'portfolio_risks_funds':
            sql = f"CREATE TABLE {table} (\
                id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,\
                country_code varchar(5),\
                fund_code varchar(20) DEFAULT NULL,\
                par_loan_principal double DEFAULT 0,\
                par_days bigint DEFAULT 0,\
                date text,\
                partial_paid double DEFAULT 0,\
                fee double DEFAULT 0,\
                percentage double DEFAULT 0,\
                total_os_principal double DEFAULT 0\
                )"

        elif table == 'portfolio_risks_markets':
            sql = f"CREATE TABLE {table} (\
                id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,\
                country_code varchar(5),\
                par_loan_principal double DEFAULT 0,\
                par_days bigint DEFAULT 0,\
                date text,\
                partial_paid double DEFAULT 0,\
                fee double DEFAULT 0,\
                percentage double DEFAULT 0,\
                total_os_principal double DEFAULT 0\
                )"

        elif table == 'bad_debts':
            sql = f"CREATE TABLE bad_debts (\
                id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,\
                    country_code varchar(5),\
                    acc_prvdr_code varchar(5) DEFAULT NULL,\
                    fund_code varchar(20) DEFAULT NULL,\
                    date text,\
                    bad_debts_amount double DEFAULT 0\
                    )"
        db_rep.execute(sql)    



def calculate_bad_debts(par_loan_principal_list):
    bad_debts = ( 0.1 * ( par_loan_principal_list.get(30,0) - par_loan_principal_list.get(60,0) ) ) + \
                ( 0.5 * ( par_loan_principal_list.get(60,0) - par_loan_principal_list.get(90,0) ) ) + \
                par_loan_principal_list.get(90,0) 

    return bad_debts


def get_os_value(entity, entity_code, addl_condn = "", date = ""):

    if date != "":
        db.execute(f"set @date = '{date}'")
    fv_products = get_float_vend_products(db)    

    principal_query = f"select l.loan_doc_id, loan_principal principal from loans l\
                               where l.{entity} = '{entity_code}' and date(disbursal_Date) <= @date\
                               and product_id not in {fv_products}\
                               and ( date(paid_date) > @date or paid_date is null) and {DISBURSED}"

    partial_pay_query = f"select l.loan_doc_id, sum(amount) partial_pay from loans l, loan_txns t\
                                 where l.{entity} = '{entity_code}' and l.loan_doc_id = t.loan_doc_id\
                                 and date(disbursal_Date) <= @date and product_id not in {fv_products}\
                                 and (date(paid_date) > @date or paid_date is null)\
                                 and date(txn_date) <= @date and txn_type = 'payment' and {DISBURSED} "

    sql = f"select sum(IF(principal - IFNULL(partial_pay,0) <0, 0, principal - IFNULL(partial_pay,0))) par_loan_principal, \
                                        sum(IF(partial_pay > principal, principal, partial_pay)) partial_paid \
                         				from ({principal_query + addl_condn}) pri left join ({partial_pay_query + addl_condn} group by l.loan_doc_id) pp\
                         				on pri.loan_doc_id = pp.loan_doc_id"
    df = pd.read_sql_query(sql, con = db);


    return df


def get_par_and_npl(country_code, month_end, entity, entity_code = None):
    ignore_write_offs_condn = get_ignore_written_off_condn(country_code, month_end, db, alias='l.')
    entity_code = country_code if entity == 'country_code' and entity_code is None else entity_code
    par_loan_principal_list = dict()
    par_df = pd.DataFrame()

    db.execute(f"set @date = '{month_end}'")
    total_os_principal_df = get_os_value(entity, entity_code, ignore_write_offs_condn);
    total_os_principal = total_os_principal_df['par_loan_principal'][0]

    for par_day in PAR_DAYS:
            print(month_end)

            date_diff = f"and DATEDIFF(@date, l.due_date) > {par_day} "
            par_os_principal_df = get_os_value(entity, entity_code, date_diff + ignore_write_offs_condn);
            par_os_principal_df['par_days'] = par_day
            par_os_principal_df['date'] = month_end
                        #par_os_principal_df['partial_paid'] = par_os_principal_df['partial_pay'][0]


            fee_query = f"select @date month_end , sum(flow_fee) fee, l.acc_prvdr_code from loans l\
                               where l.acc_prvdr_code = '{entity_code}' and date(disbursal_Date) <= @date\
                               and ( date(paid_date) > @date or paid_date is null) and DATEDIFF(@date, l.due_date) > {par_day} {ignore_write_offs_condn}"

            fee = pd.read_sql_query(fee_query, con=db )
            par_os_principal_df['fee'] = fee['fee'][0]
            par_os_principal_df[entity] = entity_code
            if par_os_principal_df['par_loan_principal'][0] is not None:
                par_os_principal_df['percentage'] = float(par_os_principal_df['par_loan_principal'][0]/total_os_principal)
                par_loan_principal = par_os_principal_df['par_loan_principal'][0]
            else :
                par_os_principal_df['percentage'] = 0
                par_os_principal_df['par_loan_principal'] = 0
                par_loan_principal = 0

            par_os_principal_df['total_os_principal'] = total_os_principal
            par_loan_principal_list.update({par_day: par_loan_principal})

            par_os_principal_df['country_code'] = country_code
            par_df = pd.concat([par_df,par_os_principal_df], ignore_index=True)
            print(par_df)

            # par_os_principal_df.to_sql('portfolio_risks', con = db_rep, if_exists='append', index = False)

    bad_debts = calculate_bad_debts(par_loan_principal_list)
    bad_debts_data = {'country_code': [country_code], entity: [entity_code], 'date': month_end, 'bad_debts_amount': [bad_debts]}
    bad_debts_record = pd.DataFrame(bad_debts_data)
    # bad_debts_record.to_sql('bad_debts',if_exists="append", con = db_rep, index=False)
    return par_df,bad_debts_record

def fetch_for_aps(last_day):
    print("STARTED AP PAR SCRIPT")

    table_exists('portfolio_risks')

    country_codes = map_ap_to_country(db)
    month_range = get_month_range('portfolio_risks') if last_day is None else [last_day]


    for country_code, ap_codes in country_codes.items():

        print(country_code)
        

        for ap_code in ap_codes:

            for month_end in month_range:
                par_loan_principal_list = dict()
                par_df, npl_df = get_par_and_npl(country_code, month_end, 'acc_prvdr_code', ap_code)
                par_df.to_sql('portfolio_risks', con = db_rep, if_exists='append', index = False)
                npl_df.to_sql('bad_debts',if_exists="append", con = db_rep, index=False)






def fetch_for_countries(last_day):
    print("STARTED COUNTRY PAR SCRIPT")

    table_exists('portfolio_risks_markets')
    country_codes = map_ap_to_country(db)
    month_range = get_month_range('portfolio_risks_markets') if last_day is None else [last_day]

    for country_code, ap_codes in country_codes.items():

        for month_end in month_range:
            par_df, npl_df = get_par_and_npl(country_code, month_end, 'country_code')
            par_df.to_sql('portfolio_risks', con = db_rep, if_exists='append', index = False)
            npl_df.to_sql('bad_debts',if_exists="append", con = db_rep, index=False)




def fetch_for_funds(last_day):
    print("STARTED FUND PAR SCRIPT")
    table_exists('portfolio_risks_funds')
    countries = map_fund_to_country(db)
    for country_code, fund_codes in countries.items():
        for fund_code in fund_codes:
            month_range = get_month_range_funds(fund_code) if last_day is None else [last_day]

            for month_end in month_range:
                par_df, npl_df = get_par_and_npl(country_code, month_end, 'fund_code', fund_code)
                print(par_df)
                par_df.to_sql('portfolio_risks_funds', con = db_rep, if_exists='append', index = False)
                npl_df.to_sql('bad_debts',if_exists="append", con = db_rep, index=False)






def get_month_range(table):
    
    sql = f"select max(date(date)) as last_date from {table}"


    query_result = pd.read_sql(sql,  con = db_rep)
    start_date = query_result['last_date'][0]
    start_date = datetime.date(2019, 1, 1) if start_date is None else datetime.datetime.strptime(str(start_date),"%Y-%m-%d")
    month_range = get_range(start_date)
    return month_range


def get_month_range_funds(fund_code):
    sql = f"select max(date(date)) as last_date from portfolio_risks_funds where fund_code = '{fund_code}'"
    query_result = pd.read_sql(sql,  con = db_rep)
    start_date = query_result['last_date'][0]
    if start_date is None:
        start_date = pd.read_sql(sql = f"select alloc_date from capital_funds where fund_code = '{fund_code}'", con=db)
        print(str(start_date['alloc_date'][0]))
        start_date = datetime.datetime.strptime(str(start_date['alloc_date'][0]),'%Y-%m-%d %H:%M:%S')
        start_date = start_date - relativedelta(months=1)
    else:
        start_date = datetime.datetime.strptime(str(start_date),"%Y-%m-%d")
    month_range = get_range(start_date)
    return month_range



def get_range(start_date):
    month_range = []
    this_year = datetime.date.today().year
    this_month = datetime.date.today().month
    start_year = start_date.year
    print(start_year)
    for year in range(start_year, this_year + 1):
        if start_date is None or year != start_year:
            start_month = 1		
        else:
            start_month = start_date.month + 1

        last_month = this_month if(year == this_year) else 13
        for month in range(start_month, last_month):
            day = datetime.date(year, month, calendar.monthrange(year, month)[-1])
            month_range.append(day)
    return month_range

if __name__ == "__main__":
    if len(sys.argv)>1:
        yyyymm = sys.argv[1] 
        date = datetime.datetime.strptime(yyyymm, "%Y%m")
        month_end = datetime.date(date.year, date.month, calendar.monthrange(date.year, date.month)[-1])
    else:
        month_end = None

    try:
        transaction = db_rep.begin()
        table_exists('bad_debts')
        fetch_for_aps(month_end)
        # fetch_for_countries(month_end)
        # fetch_for_funds(month_end)
        transaction.commit()
    except Exception as e:
        transaction.rollback()
        traceback.print_exc()


    db.close()
    db_rep.close()

    db_eng.dispose()
    db_rep_eng.dispose()