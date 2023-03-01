from time import strftime
import numpy
import pandas as pd
from flow_common import db_read_only_str,db_rep_str,load_env, DISBURSED, NOT_WRITTEN_OFF, WRITTEN_OFF_STATUSES
from sqlalchemy import create_engine
import sys
import datetime as dt
from reports_common import get_forex_for_date, get_ignore_written_off_condn, insert_records, map_ap_to_country, extract_from_query, get_float_vend_products, paid_after, value_of, get_table_fields
from monthly_report import get_revenue
from dateutil.relativedelta import relativedelta



load_env()

db = create_engine('mysql+mysqlconnector://'+db_read_only_str('STMT'))
db_rep = create_engine('mysql+mysqlconnector://'+db_rep_str('STMT'))
fv_products = get_float_vend_products(db)


NOT_FLOAT_VENDING = f"product_id not in {fv_products}"


def get_os_info(country_code, date, addl_condn = "", ignore_partial = False):
    

    db.execute(f"set @date = '{date}'")

    


    principal_query = f"""select IFNULL(sum(loan_principal),0) principal_os, count(loan_doc_id) os_count from loans l
                               where l.country_code = '{country_code}' and date(disbursal_Date) <= @date
                               and ( date(paid_date) > @date or paid_date is null)
                                 {addl_condn}"""
    result = extract_from_query(principal_query, db)

    partial_pay_query = f"""select IFNULL(sum(loan_principal),0) principal, IFNULL(sum(partial_pay),0) partial_pay, count(1) part_paid_count from 
                                (select IFNULL(sum(amount),0) partial_pay, loan_principal
                                   from loans l, loan_txns t
                                    where l.country_code = '{country_code}' and l.loan_doc_id = t.loan_doc_id
                                    and date(disbursal_Date) <= @date
                                    and (date(paid_date) > @date or paid_date is null)
                                    and date(txn_date) <= @date and txn_type = 'payment' 
                                    {addl_condn}
                                    group by l.loan_doc_id) p"""
    part_pay_result = extract_from_query(partial_pay_query, db)


    if(ignore_partial):
        result['principal_os'] -= part_pay_result['principal']
        result['os_count'] -= part_pay_result['part_paid_count']
    else:
        result['principal_os'] -= part_pay_result['partial_pay']    

    return result




def generate_report(month):

    db_rep.execute(f"delete from portfolio_summary where month = {month}")
    countries = map_ap_to_country(db)
    # db_rep.execute(f"delete from live_reports where date(report_date) = '{report_date}'")
    last_day_of_month = (dt.datetime.strptime(str(month),"%Y%m") + relativedelta(day=31)).strftime("%Y-%m-%d")
    for country_code, acc_prvdr_codes in countries.items():
        ignore_write_offs_condn = get_ignore_written_off_condn(country_code, last_day_of_month, db, alias='{0}')
        query = f"""SELECT SUM(loan_principal) AS total, count(1) AS count FROM loans where EXTRACT(YEAR_MONTH from disbursal_date) = '{month}' 
                     and {DISBURSED} and {NOT_FLOAT_VENDING} and country_code = '{country_code}'"""

        result = extract_from_query(query, db)
        tot_disb_amt = result.get('total')     
        tot_loans_count = result.get('count')   

 
        total_income, excess = get_revenue(month, country_code, None)

        amount_expected_query = f"""SELECT IFNULL(sum(loan_principal + flow_fee),0) expected from loans where country_code = '{country_code}' and 
                                    EXTRACT(YEAR_MONTH from due_date) = {month} and {DISBURSED} and {NOT_FLOAT_VENDING}"""
        amount_expected = extract_from_query(amount_expected_query, db).get('expected')

        amount_collected_query = f"""select IFNULL(sum(amount),0) collected from loans l, loan_txns t 
                                            where l.loan_doc_id = t.loan_doc_id and l.country_code = '{country_code}' 
                                            and EXTRACT(YEAR_MONTH from txn_date) = {month} 
                                            and EXTRACT(YEAR_MONTH from due_date) = {month}
                                            and {DISBURSED} and {NOT_FLOAT_VENDING} 
                                            and txn_type in ('payment')"""

        amount_collected = extract_from_query(amount_collected_query, db).get('collected')                                            
        collection_rate = amount_collected / amount_expected


        avg_tenor_query = f"""select IFNULL(sum(loan_principal * duration),0) total_weighted_duration, IFNULL(sum(loan_principal),0) tot_principal from loans 
            where EXTRACT(YEAR_MONTH from disbursal_date) = '{month}' 
                     and {DISBURSED} and {NOT_FLOAT_VENDING} and country_code = '{country_code}'"""

        result = extract_from_query(avg_tenor_query, db)

        avg_loan_tenor = result.get('total_weighted_duration') / result.get('tot_principal')


        #os excluding 90d+
        os_info = get_os_info(country_code, last_day_of_month, "and datediff(@date, due_date) < 90")
        total_os_amt = os_info['principal_os']
        total_os_count = os_info['os_count']

        #overall os amount - including 90d+
        overall_os_amt = get_os_info(country_code, last_day_of_month)['principal_os']
        if overall_os_amt == 0:
            delinquency_7_29 = delinquency_30_59 = delinquency_60_89 = delinquency_90 = 0
        else: 
            #delinquency figures - ignoring partial pay   
            delinquency_7_29 = get_os_info(country_code, last_day_of_month, "and DATEDIFF(@date, l.due_date) BETWEEN 7 AND 29")['principal_os']
            delinquency_30_59 = get_os_info(country_code, last_day_of_month, "and DATEDIFF(@date, l.due_date) BETWEEN 30 AND 59")['principal_os']
            delinquency_60_89 = get_os_info(country_code, last_day_of_month, "and DATEDIFF(@date, l.due_date) BETWEEN 60 AND 89")['principal_os']
            delinquency_90 = get_os_info(country_code, last_day_of_month, "and DATEDIFF(@date, l.due_date) >= 90")['principal_os']




        insert_records('portfolio_summary', db_rep, locals())


def month_range():
    sql = f"select max(month) max from portfolio_summary"
    date = extract_from_query(sql, db_rep).get('max')
    if date == None:
        start_date = '2019-02-01'
    else:
        start_date = dt.datetime.strptime(str(date), "%Y%m").strftime("%Y-%m-%d")
    date_range = pd.date_range(start=start_date, end=((dt.datetime.now() - relativedelta(months = 1)).strftime("%Y-%m-%d")))
    month_range = date_range[date_range.day==1]
    return month_range
    


if __name__ == '__main__':
    month_range = month_range()
    for month in month_range:
        generate_report(month.strftime("%Y%m"))