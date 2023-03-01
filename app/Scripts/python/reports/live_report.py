from operator import ge, index
from re import S
from time import strftime
import traceback
import numpy
import pandas as pd
from flow_common import db_read_only_str,db_rep_str,load_env,STATUSES_BEFORE_DISBURSAL, WRITTEN_OFF_STATUSES
from sqlalchemy import create_engine
import sys
import datetime as dt
from reports_common import get_forex_for_date, get_ignore_written_off_condn, insert_records, map_ap_to_country, extract_from_query, get_float_vend_products, paid_after, value_of, get_table_fields, get_currency_code, for_entity as for_e
from portfolio import get_par_and_npl, get_os_value
from dateutil.relativedelta import relativedelta


load_env()

db_eng = create_engine('mysql+mysqlconnector://'+db_read_only_str('STMT'))
db_rep_eng = create_engine('mysql+mysqlconnector://'+db_rep_str('STMT'))

db = db_eng.connect()
db_rep = db_rep_eng.connect()
ignore_write_offs_condn = ""
fv_products = get_float_vend_products(db)

DISBURSED = f" status not in {STATUSES_BEFORE_DISBURSAL} "
NOT_WRITTEN_OFF = f"(write_off_status not in {WRITTEN_OFF_STATUSES} or write_off_status is null)"
NOT_FLOAT_VENDING = f"product_id not in {fv_products}"

AVG_RETAIL_TXNS_FUNDED_PER_FA = 340
AVG_RETAIL_CUSTS_PER_AGENT = 500
AVG_CUST_REVENUE_PER_MONTH_IN_USD = 105
CAPITAL_MULT_FACTOR_FOR_AGENT = 1.25



def get_reg_cust_count(country_code, acc_prvdr_code, date):
    sql = f"""select COUNT(*) reg_count from borrowers
              where {for_e(country_code, acc_prvdr_code)}
              and date(reg_date) <= '{date}' """
    print(sql)
    return extract_from_query(sql, db).get('reg_count')              

def get_enabled_cust_count(country_code, acc_prvdr_code):
    sql=f"""select count(*) enable_count from borrowers
            where status = 'enabled' and {for_e(country_code, acc_prvdr_code)}"""
    return extract_from_query(sql, db).get('enable_count')     

def get_active_cust_count(country_code, acc_prvdr_code, date):
    sql=f"""select count(distinct(l.cust_id)) as active_count from loans l, loan_txns t where {for_e(country_code, acc_prvdr_code, alias='l')} 
            and l.loan_doc_id = t.loan_doc_id and (datediff('{date}',txn_date)) <= 30 and date(txn_date) <= '{date}'
            and txn_type = 'disbursal' and {NOT_FLOAT_VENDING}"""
    return extract_from_query(sql, db).get('active_count')     

def get_open_cust_count(country_code, acc_prvdr_code, date):
    sql=f"""select count(*) open_count from borrowers
            where profile_status = 'open' and {for_e(country_code, acc_prvdr_code)}"""
    return extract_from_query(sql, db).get('open_count')



def get_supported_cust_count(country_code, acc_prvdr_code, date):
    sql=f"""select count(*) supported_count from borrowers
            where tot_loans > 0 and {for_e(country_code, acc_prvdr_code)}
            and date(reg_date) <= '{date}' """
    return extract_from_query(sql, db).get('supported_count')    


def get_custs_with_os_fa(country_code, acc_prvdr_code, date):
    sql=f"""select count(distinct cust_id) os_cust_count from loans
                    where {DISBURSED}  {ignore_write_offs_condn.format('')} and {NOT_FLOAT_VENDING} and 
                    date(disbursal_date) <= '{date}' and {paid_after(date)}
                    and {for_e(country_code, acc_prvdr_code)}""" 
    return extract_from_query(sql, db).get('os_cust_count')    

def get_custs_with_more_than_30_day_overdue(country_code, acc_prvdr_code, date):
    sql = f"""select count( distinct cust_id) od_count from loans 
                        where {DISBURSED} {ignore_write_offs_condn.format('')} and {NOT_FLOAT_VENDING} and
                        datediff('{date}',due_date)>30 and {paid_after(date)}
                        and {for_e(country_code, acc_prvdr_code)}"""
    return extract_from_query(sql, db).get('od_count')    

def get_cust_percentages(country_code, acc_prvdr_code, date):
    sql = f"""select SUM(IF(gender ='male',1,0))/COUNT(*) male_perc, 
                        SUM(IF(gender ='female',1,0))/COUNT(*) female_perc,
                        SUM(IF(TIMESTAMPDIFF(YEAR, dob, '{date}')<=30,1,0))/COUNT(*) youth_perc 
                        from borrowers b, persons p where b.owner_person_id = p.id
                        and date(b.reg_date) <= '{date}' {for_e(country_code, acc_prvdr_code, alias='b', prefix=' and')}"""
    result = extract_from_query(sql, db)
    male_perc = result.get('male_perc')
    female_perc = result.get('female_perc')
    youth_perc = result.get('youth_perc')
    return male_perc, female_perc, youth_perc

def get_disbursed_value_and_count(country_code, acc_prvdr_code, date):
    sql = f"""select IFNULL(sum(amount), 0) disb_amount, IFNULL(count(distinct l.loan_doc_id),0) tot_fas from loans l, loan_txns t
                    where {DISBURSED} and {NOT_FLOAT_VENDING} and l.loan_doc_id = t.loan_doc_id and txn_type = 'disbursal' and 
                    date(txn_date) <= '{date}'  and
                    {for_e(country_code, acc_prvdr_code, alias= 'l')}"""
    result = extract_from_query(sql, db)
    tot_disb_amt = result.get('disb_amount')    
    tot_disb_fas = result.get('tot_fas')    
    return tot_disb_amt, tot_disb_fas


def get_income_values(country_code, acc_prvdr_code, date):
    sql = f"""select sum(amount) gtv
                        from loans l, loan_txns t where 
                        l.loan_doc_id = t.loan_doc_id and {DISBURSED} and {NOT_FLOAT_VENDING} and
                        date(disbursal_date) <= '{date}' and date(txn_date) <= '{date}' and 
                        txn_type in ('disbursal','payment')
                        and {for_e(country_code, acc_prvdr_code, alias='l')}"""
    gross_txn_val =  extract_from_query(sql, db).get('gtv') 

    sql = f"""
            select IFNULL(sum(revenue),0) revenue from (select l.loan_doc_id, sum(ifnull(fee,0) + ifnull(penalty,0) + ifnull(excess,0)) revenue from loans l, loan_txns t where l.loan_doc_id = t.loan_doc_id
              and {DISBURSED} and {NOT_FLOAT_VENDING}
              and date(txn_date) <= '{date}'
              and txn_type in ('payment') 
              and {for_e(country_code, acc_prvdr_code, alias='l')} group by l.loan_doc_id) p """
    revenue = extract_from_query(sql, db).get('revenue')


    sql = f"""select IFNULL(sum(amount),0) excess_reversed from  loans l, loan_txns t where l.loan_doc_id = t.loan_doc_id
              and {DISBURSED} and {NOT_FLOAT_VENDING}
              and date(txn_date) <= '{date}'
              and txn_type = 'excess_reversal'
              and {for_e(country_code, acc_prvdr_code, alias='l')}"""
    excess_reversed = extract_from_query(sql, db).get('excess_reversed')
    revenue -= excess_reversed
    return gross_txn_val, revenue, excess_reversed

def get_avg_fa_info(country_code, acc_prvdr_code, date):
    sql = f"""select avg(loan_principal) avg_fa_size, 
                   avg(duration) avg_fa_dur, avg(paid_fee) avg_fa_fee from loans
                   where {DISBURSED} and {NOT_FLOAT_VENDING}
                   and date(disbursal_date) <= '{date}' 
                   and {for_e(country_code, acc_prvdr_code)}"""
    result = extract_from_query(sql, db)
    avg_fa_size = result.get('avg_fa_size')
    avg_fa_dur = result.get('avg_fa_dur')
    avg_fa_fee = result.get('avg_fa_fee')
    return avg_fa_size, avg_fa_dur, avg_fa_fee

def get_write_off_info(country_code, acc_prvdr_code, date):
    sql = f"""select sum(write_off_amount) write_off_amt, sum(recovery_amount) rcvry_amt, 
                          count(distinct w.loan_doc_id) write_off_count from loan_write_off w, loans l
                          where l.loan_doc_id = w.loan_doc_id and {DISBURSED}
                          and {NOT_FLOAT_VENDING} and date(w.appr_date) <= '{date}' and
                          l.country_code = '{country_code}' and l.acc_prvdr_code = '{acc_prvdr_code}'"""
    result = extract_from_query(sql, db)
    write_off_amt = result.get('write_off_amt')
    rcvry_amt = result.get('rcvry_amt')
    write_off_count = result.get('write_off_count')
    return write_off_amt, rcvry_amt, write_off_count

def get_os_amounts(country_code, acc_prvdr_code, date):
    entity_code = 'country_code' if acc_prvdr_code is None else 'acc_prvdr_code'
    entity = country_code if acc_prvdr_code is None else acc_prvdr_code	
    db.execute(f"set @date = '{date}'")
    princ_os = get_os_value(entity_code, entity, ignore_write_offs_condn.format('l.'), date)
    princ_os = princ_os['par_loan_principal'][0]

    fee_os_query = f"""SELECT SUM(IF(due_fee - COALESCE(partially_paid_fee, 0) < 0, 0, due_fee - COALESCE(partially_paid_fee, 0))) AS fee_os, count(loan_doc_id) count
                                FROM (
                                SELECT loan_doc_id, flow_fee AS due_fee,
                                        (SELECT SUM(fee)
                                        FROM loan_txns
                                        WHERE loan_doc_id = l.loan_doc_id AND txn_type = 'payment' AND
                                                date(txn_date) <= '{date}') AS partially_paid_fee
                                FROM loans l
                                WHERE {for_e(country_code, acc_prvdr_code)} AND date(disbursal_date) <= '{date}' AND
                                        (date(paid_date) > '{date}' OR paid_date IS NULL) AND
                                        {DISBURSED} and {NOT_FLOAT_VENDING}
                                        {ignore_write_offs_condn.format('l.')}
                                ) loans_with_fees
                                """

    os_info = extract_from_query(fee_os_query, db)
    fee_os = os_info.get('fee_os')
    os_count = os_info.get('count')


    return princ_os, fee_os, os_count

def get_max_os_value(country_code, acc_prvdr_code, date, current_os = None):
    sql = f"""select principal_os max_os, report_date from live_reports where principal_os = 
                (select max(principal_os) from live_reports where
                date(report_date) <= '{date}' and
                {for_e(country_code, acc_prvdr_code, True)})
                order by report_date desc limit 1"""
    result = extract_from_query(sql, db_rep)
    if((result.get('max_os') is None) or (current_os is not None and result.get('max_os') < current_os)):
        return f"{current_os},{date}"

    return f"{result.get('max_os')},{result.get('report_date')}"

def get_ontime_repayment_rate(country_code, acc_prvdr_code, date):
    sql = f"""select sum(ontime_count)/sum(settled_count) ontime_rate from live_reports where 
            date(report_date) <= '{date}'
           {for_e(country_code, acc_prvdr_code, True, prefix='and')}"""
    return extract_from_query(sql, db_rep).get('ontime_rate')

def get_max_ontime_repayment_rate(country_code, acc_prvdr_code, date):
    sql = f"""select ontime_repayment_rate, report_date from live_reports where ontime_repayment_rate = 
                (select max(ontime_repayment_rate) from live_reports where
                date(report_date) <= '{date}'
                {for_e(country_code, acc_prvdr_code, True, prefix='and')}) 
                order by report_date desc limit 1"""
    result = extract_from_query(sql, db_rep)
    return f"{result.get('ontime_repayment_rate')},{result.get('report_date')}"

def get_par_npl_values(country_code, acc_prvdr_code, date):
    entity_code = 'country_code' if acc_prvdr_code is None else 'acc_prvdr_code'
    par_df, npl_df = get_par_and_npl(country_code, date, entity_code, acc_prvdr_code)
    par_df = par_df.set_index('par_days')
    par15 = par_df.loc[15,'par_loan_principal']
    par30 = par_df.loc[30,'par_loan_principal']
    par60 = par_df.loc[60,'par_loan_principal']
    par90 = par_df.loc[90,'par_loan_principal']
    npl = npl_df.loc[0,'bad_debts_amount']
    return par15, par30, par60, par90, npl

def get_od_values(country_code, acc_prvdr_code, date):
    princ_sql = f"""select loan_doc_id, loan_principal principal from loans 
                               where date(disbursal_date) <= '{date}' and {paid_after(date)}
                               and datediff('{date}', due_date) > 1  
                               {ignore_write_offs_condn.format('')} and 
                               {NOT_FLOAT_VENDING} and {DISBURSED} and
                               {for_e(country_code, acc_prvdr_code)}"""
    
    partial_pay_sql = f"""select l.loan_doc_id, IFNULL(sum(amount),0) partial_pay from loans l, loan_txns t 
                               where l.loan_doc_id = t.loan_doc_id and date(disbursal_date) <= '{date}'
                               and {paid_after(date)} and datediff('{date}', due_date) > 1 and date(txn_date) <= '{date}'
                               and txn_type in ('payment') 
                                {ignore_write_offs_condn.format('l.')} and
                               {NOT_FLOAT_VENDING} and {DISBURSED}
                               and {for_e(country_code, acc_prvdr_code, alias='l')}
                               group by l.loan_doc_id"""

    od_sql = f"""select sum(IF(principal - IFNULL(partial_pay,0) <0, 0, principal - IFNULL(partial_pay,0))) od,
                        count(pri.loan_doc_id) count
                from ({princ_sql}) pri left join ({partial_pay_sql}) pp
                on pri.loan_doc_id = pp.loan_doc_id"""

    result = extract_from_query(od_sql, db)
    od_count = result.get('count')
    od_amount = result.get('od')
    return od_amount, od_count

def get_settled_ontime_count(country_code, acc_prvdr_code, date):
    settled_ontime_q = f"""select count(loan_doc_id) settled_count, SUM(IF(date(paid_date) <= DATE_ADD(date(due_date), INTERVAL 1 day), 1, 0)) ontime_count
                               from loans where date(paid_date) = '{date}'  {ignore_write_offs_condn.format('')} and {NOT_FLOAT_VENDING}
                               and {for_e(country_code, acc_prvdr_code)}"""
    result = extract_from_query(settled_ontime_q, db)
    settled_count = result.get('settled_count')
    ontime_count = result.get('ontime_count')
    return settled_count, ontime_count

def get_last_threemonth_active_cust_count(country_code, acc_prvdr_code, date):
    now = dt.datetime.strptime(date, "%Y-%m-%d")
    last_three_month_date= now - relativedelta(months=3)

    sql = f"""select IFNULL(count(distinct cust_id), 0) cust_count
                    from loans where date(paid_date) >= '{last_three_month_date}' and date(paid_date)<= date('{date}') {for_e(country_code, acc_prvdr_code, prefix="and")} and {DISBURSED} and {NOT_FLOAT_VENDING}"""
    result = extract_from_query(sql, db)
    cust_id = result.get('cust_count')

    return cust_id


def get_tot_rm_count_of_last_three_month_active_cust(country_code,acc_prvdr_code,date):
        now = dt.datetime.strptime(date, "%Y-%m-%d")
        last_three_month_date= now - relativedelta(months=3)

        sql = f"""select IFNULL(count(distinct  flow_rel_mgr_id), 0) flow_rel_mgr_id
                    from loans where date(paid_date)  >= '{last_three_month_date}' and date(paid_date)<= date('{date}') {for_e(country_code, acc_prvdr_code, prefix="and")} and {DISBURSED} and {NOT_FLOAT_VENDING}"""
        result = extract_from_query(sql, db)
        flow_rel_mgr_id = result.get('flow_rel_mgr_id')
     
        return flow_rel_mgr_id

def get_last_threemonth_revenue(country_code,acc_prvdr_code,date):

    now = dt.datetime.strptime(date, "%Y-%m-%d")
    last_three_month_date= now - relativedelta(months=3)

    sql = f"""
            select IFNULL(sum(revenue),0) revenue from (select l.loan_doc_id, SUM(IFNULL(fee,0) + IFNULL(penalty,0) + IFNULL(excess,0)) revenue from loans l, loan_txns t where l.loan_doc_id = t.loan_doc_id
              and {DISBURSED} and {NOT_FLOAT_VENDING}
              and date(txn_date) >= '{last_three_month_date}' and date(paid_date)<= date('{date}')
              and txn_type in ('payment') 
              and {for_e(country_code, acc_prvdr_code, alias='l')} group by l.loan_doc_id) p """
    revenue = extract_from_query(sql, db).get('revenue')


    sql = f"""select IFNULL(sum(amount),0) excess_reversed from  loans l, loan_txns t where l.loan_doc_id = t.loan_doc_id
              and {DISBURSED} and {NOT_FLOAT_VENDING}
              and date(txn_date) >= '{last_three_month_date}' and date(paid_date)<= date('{date}')
              and txn_type = 'excess_reversal'
              and {for_e(country_code, acc_prvdr_code, alias='l')}"""
    excess_reversed = extract_from_query(sql, db).get('excess_reversed')
    revenue -= excess_reversed
    return revenue


def generate_report(date_obj):
    report_date = date_obj.strftime("%Y-%m-%d")
    run_at = (dt.datetime.now()).strftime("%Y-%m-%d %H:%M:%S")
    countries = map_ap_to_country(db)
    db_rep.execute(f"delete from live_reports where date(report_date) = '{report_date}'")
    for country_code, acc_prvdr_codes in countries.items(): 
        global ignore_write_offs_condn
        ignore_write_offs_condn = get_ignore_written_off_condn(country_code, report_date, db, alias="{0}")
        currency_code = get_currency_code(country_code, db)
        acc_prvdr_codes.append(None)
        for acc_prvdr_code in acc_prvdr_codes:


            
            forex_usd = get_forex_for_date('USD', currency_code, report_date, db)

            supported_custs = get_supported_cust_count(country_code, acc_prvdr_code, report_date)
            
            tot_disb_amt, tot_disb_fas = get_disbursed_value_and_count(country_code, acc_prvdr_code, report_date)

            cust_revenue = supported_custs * AVG_CUST_REVENUE_PER_MONTH_IN_USD * forex_usd

            people_benefited = supported_custs * AVG_RETAIL_CUSTS_PER_AGENT
 
            tot_retail_txn_count = tot_disb_fas * AVG_RETAIL_TXNS_FUNDED_PER_FA
            
            tot_retail_txn_val = tot_disb_amt * CAPITAL_MULT_FACTOR_FOR_AGENT
            
            reg_count = get_reg_cust_count(country_code, acc_prvdr_code, report_date)

            enable_count = get_enabled_cust_count(country_code, acc_prvdr_code)
            
            active_count = get_active_cust_count(country_code, acc_prvdr_code, report_date)
            
            open_count = get_open_cust_count(country_code, acc_prvdr_code, report_date)

            
            custs_w_os_fa = get_custs_with_os_fa(country_code, acc_prvdr_code, report_date)
            
            custs_w_30d_od = get_custs_with_more_than_30_day_overdue(country_code, acc_prvdr_code, report_date)
            
            male_perc, female_perc, youth_perc = get_cust_percentages(country_code, acc_prvdr_code, report_date)
            
            
            gross_txn_value, revenue, excess_reversed = get_income_values(country_code, acc_prvdr_code, report_date)
            
            avg_fa_size, avg_fa_duration, avg_fa_fee = get_avg_fa_info(country_code, acc_prvdr_code, report_date)
            
            write_off_amt, recovery_amt, write_off_count = get_write_off_info(country_code, acc_prvdr_code, report_date)
            
            par15, par30, par60, par90, npl = get_par_npl_values(country_code, acc_prvdr_code, report_date)

            principal_os, fee_os, os_count = get_os_amounts(country_code, acc_prvdr_code, report_date)
            
            max_os = get_max_os_value(country_code, acc_prvdr_code, report_date, principal_os)
            
            ontime_repayment_rate = get_ontime_repayment_rate(country_code, acc_prvdr_code, report_date)

            max_ontime_repay_rate = get_max_ontime_repayment_rate(country_code, acc_prvdr_code, report_date)

            settled_count, ontime_count = get_settled_ontime_count(country_code, acc_prvdr_code, report_date)
            
            
            od_amount, od_count = get_od_values(country_code, acc_prvdr_code, report_date)

            cust_count_for_rev_calc=get_last_threemonth_active_cust_count(country_code,acc_prvdr_code,report_date)
            
            last_threemonth_revenue=get_last_threemonth_revenue(country_code,acc_prvdr_code,report_date)

            rev_per_cust=last_threemonth_revenue/cust_count_for_rev_calc

            rm_count_for_rev_calc=get_tot_rm_count_of_last_three_month_active_cust(country_code,acc_prvdr_code,report_date)

            rev_per_rm=revenue/rm_count_for_rev_calc
            

            insert_records('live_reports', db_rep, locals())

    generate_daily_metrics_global(report_date)


def get_max_os_for_global():
    report_date_qry = f"""select min(substring_index(y.amt,',',-1)) rpt_dt from (select concat(sum(principal_os),',',
                      report_date) amt from live_reports where acc_prvdr_code is null and country_code!= '*' group
                      by report_date) y where convert(substring_index(y.amt,',',1),SIGNED) IN (select max(amt)
                      report_date from (select sum(principal_os) amt from live_reports where acc_prvdr_code is null
                      and country_code!= '*' group by report_date)x); """
    report_date_extract = extract_from_query(report_date_qry, db_rep)
    max_os_report_date = report_date_extract.get('rpt_dt')
    results = pd.read_sql(
        f"""select convert(substring_index(max_os, ',', 1),SIGNED) amt, country_code from live_reports where
        report_date = '{max_os_report_date}' and acc_prvdr_code is null and country_code!= '*'; """, db_rep)
    max_os_amount = 0
    for index, result in results.iterrows():
        country_c = result['country_code']
        query = f"SELECT currency_code from markets where country_code = '{country_c}'"
        currency_code = extract_from_query(query, db).get('currency_code')
        forex_usd = get_forex_for_date(currency_code, 'USD', max_os_report_date, db)
        max_os_amount += result['amt'] * forex_usd

    max_os = str(round(max_os_amount, 5)) + "," + str(max_os_report_date)
    return max_os


def generate_daily_metrics_global(report_date):
    results = pd.read_sql(f"select * from live_reports where date(report_date) = '{report_date}' and acc_prvdr_code "
                          f"is null and country_code != '*'", db_rep)
    country_code = '*'
    acc_prvdr_code = None
    count = 0
    run_at = (dt.datetime.now()).strftime("%Y-%m-%d %H:%M:%S")
    supported_custs = tot_disb_amt = tot_disb_fas = cust_revenue = people_benefited = tot_retail_txn_count = tot_retail_txn_val = reg_count = enable_count = active_count =0
    open_count = custs_w_os_fa = custs_w_30d_od = gross_txn_value = excess_reversed = revenue = fa_size = fa_duration = 0
    fa_fee = write_off_amt = recovery_amt = write_off_count = par15 = par30 = par60 = par90 = npl = 0
    principal_os = fee_os = os_count = max_os = settled_count = ontime_count = od_amount = od_count = 0
    for index, result in results.iterrows():
        count += 1
        country_c = result['country_code']
        query = f"SELECT currency_code from markets where country_code = '{country_c}'"
        currency_code = extract_from_query(query, db).get('currency_code')
        forex_usd = get_forex_for_date(currency_code, 'USD', report_date, db)
        supported_custs += result['supported_custs']
        tot_disb_amt += result['tot_disb_amt'] * forex_usd
        tot_disb_fas += result['tot_disb_fas']
        cust_revenue += result['cust_revenue'] * forex_usd
        people_benefited += result['people_benefited']
        tot_retail_txn_count += result['tot_retail_txn_count']
        tot_retail_txn_val += result['tot_retail_txn_val'] * forex_usd
        reg_count += result['reg_count']
        enable_count += result['enable_count']
        active_count += result['active_count']
        open_count += result['open_count']
        custs_w_os_fa += result['custs_w_os_fa']
        custs_w_30d_od += result['custs_w_30d_od']
        gross_txn_value += result['gross_txn_value'] * forex_usd
        revenue += result['revenue'] * forex_usd
        excess_reversed += result['excess_reversed'] * forex_usd
        fa_size += result['avg_fa_size'] * forex_usd
        fa_duration += result['avg_fa_duration']
        fa_fee += result['avg_fa_fee'] * forex_usd
        write_off_amt += (result['write_off_amt'] if result['write_off_amt'] else 0) * forex_usd
        recovery_amt += (result['recovery_amt'] if result['recovery_amt'] else 0) * forex_usd
        write_off_count += (result['write_off_count'] if result['write_off_count'] else 0)
        par15 += result['par15'] * forex_usd
        par30 += result['par30'] * forex_usd
        par60 += result['par60'] * forex_usd
        par90 += result['par90'] * forex_usd
        npl += result['npl'] * forex_usd
        principal_os += result['principal_os'] * forex_usd
        fee_os += result['fee_os'] * forex_usd
        os_count += result['os_count']
        settled_count += result['settled_count'] if result['settled_count'] else 0
        ontime_count += result['ontime_count'] if result['ontime_count'] else 0
        od_amount += result['od_amount'] * forex_usd
        od_count += result['od_count']
    avg_fa_size = fa_size/count
    avg_fa_duration = fa_duration/count
    avg_fa_fee = fa_fee/count
    max_os = get_max_os_for_global()
    male_perc, female_perc, youth_perc = get_cust_percentages(country_code, acc_prvdr_code, report_date)
    ontime_repayment_rate = get_ontime_repayment_rate(country_code, acc_prvdr_code, report_date)
    max_ontime_repay_rate = get_max_ontime_repayment_rate(country_code, acc_prvdr_code, report_date)

    insert_records('live_reports', db_rep, locals())


def generate_daily_metrics():
    now = (dt.datetime.now() - dt.timedelta(1)).strftime("%Y-%m-%d")
    start_date = get_start_date()
    date_range = pd.date_range(start=start_date, end=now)
    for report_date in date_range:
        report_date = report_date.strftime("%Y-%m-%d")
        run_at = (dt.datetime.now()).strftime("%Y-%m-%d %H:%M:%S")
        countries = map_ap_to_country(db)
        df = pd.read_sql(f"select id from live_reports where date(report_date) = '{report_date}'", db_rep)
        if not df.empty:
            continue
        for country_code, acc_prvdr_codes in countries.items():
            acc_prvdr_codes.append(None)
            for acc_prvdr_code in acc_prvdr_codes:

                settled_count, ontime_count = get_settled_ontime_count(country_code, acc_prvdr_code, report_date)

                principal_os, fee_os, os_count = get_os_amounts(country_code, acc_prvdr_code, report_date)

                ontime_repayment_rate = get_ontime_repayment_rate(country_code, acc_prvdr_code, report_date)

                insert_records('live_reports', db_rep, locals())
        


def get_start_date():
    sql = f"select max(date(report_date)) last_date from live_reports"
    query_result = pd.read_sql(sql,  con = db_rep)
    start_date = query_result['last_date'][0]
    if start_date is None:
        start_date = '2018-12-27'
    else:
        start_date = dt.datetime.strptime(str(start_date), "%Y-%m-%d") + dt.timedelta(1)
    return start_date



if __name__ == '__main__':
    try:
        transaction = db_rep.begin()
        args = {index: arg for index, arg in enumerate(sys.argv)}
        if args.get(1) == 'initialize':
            generate_daily_metrics()
        else:
            date = dt.datetime.now() - dt.timedelta(1)
            generate_report(date)
        transaction.commit()
    except Exception as e:
        transaction.rollback()
        traceback.print_exc()

    db.close()
    db_rep.close()

    db_eng.dispose()
    db_rep_eng.dispose()



