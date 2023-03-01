from cmath import inf
import sys
import datetime
from threading import active_count
import traceback
from numpy import extract
import pandas as pd

from flow_common import *
from sqlalchemy import create_engine
from portfolio import get_os_value
from reports_common import get_ignore_written_off_condn, get_month_forex, get_table_fields, insert_records, map_ap_to_country, extract_from_query, get_float_vend_products, for_entity as for_e, value_of
from dateutil.relativedelta import relativedelta

load_env()

db_eng = create_engine('mysql+mysqlconnector://' + db_read_only_str('STMT'))
db_rep_eng = create_engine('mysql+mysqlconnector://' + db_rep_str('STMT'))

db = db_eng.connect()
db_rep = db_rep_eng.connect()
ignore_write_offs_condn = ""
fv_products = get_float_vend_products(db)
NOT_FLOAT_VENDING = f"product_id not in {fv_products}"


def get_reg_cust_count(month, country_code, acc_prvdr_code):
    query = f"""SELECT count(*) as reg_cust_count FROM borrowers where {for_e(country_code, acc_prvdr_code)}
                and EXTRACT(YEAR_MONTH FROM reg_date) <= {month}"""

    reg_custs_count = extract_from_query(query, db).get('reg_cust_count')

    return reg_custs_count


def get_active_cust_count(month, country_code, acc_prvdr_code):
    # date = (datetime.datetime.strptime(month, "%Y%m") + relativedelta(day=31)).strftime("%Y-%m-%d")
    date = get_last_date(month)
    query = f"""SELECT count(distinct(l.cust_id)) as active_cust_count from loans l, loan_txns t where
            l.loan_doc_id = t.loan_doc_id and (datediff('{date}',txn_date)) <= 30 and date(txn_date) <= '{date}' {for_e(country_code, acc_prvdr_code, alias='l', prefix="and")}
            and txn_type = 'disbursal' and {NOT_FLOAT_VENDING}"""

    active_custs_count = extract_from_query(query, db).get('active_cust_count')

    return active_custs_count


def get_cust_churn_perc(month, country_code, acc_prvdr_code):
    date = datetime.datetime.strptime(month, '%Y%m')

    previous_month = (date - relativedelta(months=1)).strftime('%Y%m')
    query = f"""SELECT count(*) churn_count from 
            (SELECT b.cust_id, SUM(IF(extract(year_month from disbursal_date) = {previous_month}, 1, 0)) last_month_fas,
                              SUM(IF(extract(year_month from disbursal_date) = {month}, 1, 0)) this_month_fas 
            from borrowers b, loans l 
            where b.cust_id = l.cust_id {for_e(country_code, acc_prvdr_code, alias='b', prefix="and")}
            and l.{DISBURSED} and {NOT_FLOAT_VENDING}
            group by b.cust_id having last_month_fas > 0 and this_month_fas = 0) x"""

    cust_churn_count = extract_from_query(query, db).get('churn_count')
    last_month_active_count = get_active_cust_count(previous_month, country_code, acc_prvdr_code)
    if (last_month_active_count == 0):
        return 0, 0
    else:
        cust_churn_perc = cust_churn_count / last_month_active_count

    return cust_churn_count, cust_churn_perc


def get_tot_disb_val(month, country_code, acc_prvdr_code):
    query = f"""SELECT IFNULL(SUM(amount),0) AS tot_disb_val, count(distinct l.loan_doc_id) AS tot_disb_count
            FROM loans l, loan_txns t where l.loan_doc_id = t.loan_doc_id and 
            EXTRACT(YEAR_MONTH from txn_date) = {month} {for_e(country_code, acc_prvdr_code, alias = 'l', prefix="and")} 
            and {DISBURSED} and {NOT_FLOAT_VENDING} and txn_type = 'disbursal'"""

    result = extract_from_query(query, db)
    total_disbursal_value = result.get('tot_disb_val')
    total_disbursal_count = result.get('tot_disb_count')

    return total_disbursal_value, total_disbursal_count


def get_fas_settled_count(month, country_code, acc_prvdr_code):
    query = f"""SELECT count(*) as fa_settled_count from loans where {for_e(country_code, acc_prvdr_code)}
            and status = 'settled' and EXTRACT(YEAR_MONTH from paid_date) = {month}
            and {NOT_FLOAT_VENDING}"""

    fas_settled_count = extract_from_query(query, db).get('fa_settled_count')

    return fas_settled_count


def get_gross_txn_val(month, country_code, acc_prvdr_code):
    query = f"""SELECT sum(amount) AS gross_txn_val from loan_txns t, loans l where {for_e(country_code, acc_prvdr_code, alias='l')}
            and {DISBURSED} and l.loan_doc_id = t.loan_doc_id  
            and EXTRACT(YEAR_MONTH from txn_date) = {month} and txn_type in 
            {TXN_TYPES} and {NOT_FLOAT_VENDING}"""

    gross_txn_val = extract_from_query(query, db).get('gross_txn_val')

    return gross_txn_val


def get_val_os_eom(month, country_code, acc_prvdr_code):
    entity = 'country_code' if acc_prvdr_code is None else 'acc_prvdr_code'
    entity_code = country_code if acc_prvdr_code is None else acc_prvdr_code
    # date = (datetime.datetime.strptime(month, "%Y%m") + relativedelta(day=31)).strftime("%Y-%m-%d")
    date = get_last_date(month)
    db.execute(f"set @date = '{date}'")
    princ_os = get_os_value(entity, entity_code, ignore_write_offs_condn.format("l."), date)

    principal_os_val = princ_os['par_loan_principal'][0]

    return principal_os_val


def get_fee_count_os(month, country_code, acc_prvdr_code):

    fee_os_query = f"""SELECT SUM(IF(due_fee - COALESCE(partially_paid_fee, 0) < 0, 0, due_fee - COALESCE(partially_paid_fee, 0))) AS fee_os, count(loan_doc_id) count
                                FROM (
                                SELECT loan_doc_id, flow_fee AS due_fee,
                                        (SELECT SUM(fee)
                                        FROM loan_txns
                                        WHERE loan_doc_id = l.loan_doc_id AND txn_type = 'payment' AND
                                                EXTRACT(YEAR_MONTH FROM txn_date) <= {month}) AS partially_paid_fee
                                FROM loans l
                                WHERE {for_e(country_code, acc_prvdr_code)} AND EXTRACT(YEAR_MONTH FROM disbursal_Date) <= {month} AND
                                        (EXTRACT(YEAR_MONTH FROM paid_date) > {month} OR paid_date IS NULL) AND
                                        {DISBURSED} and {NOT_FLOAT_VENDING}
                                        {ignore_write_offs_condn.format('l.')}
                                ) loans_with_fees
                                """

    os_info = extract_from_query(fee_os_query, db)
    fee_os = os_info.get('fee_os')
    os_count = os_info.get('count')

    return fee_os, os_count


def get_od_values(month, country_code, acc_prvdr_code):
    # date = (datetime.datetime.strptime(month, "%Y%m") + relativedelta(day=31)).strftime("%Y-%m-%d")
    date = get_last_date(month)
    princ_sql = f"""select loan_doc_id, loan_principal principal from loans 
                               where date(disbursal_date) <= '{date}' and 
                               (date(paid_date) > '{date}' or paid_date is null)
                               and datediff('{date}', due_date) > 1 
                               {ignore_write_offs_condn.format('')} and {NOT_FLOAT_VENDING} and {DISBURSED}
                               and {for_e(country_code, acc_prvdr_code)}"""

    partial_pay_sql = f"""select l.loan_doc_id, IFNULL(sum(amount),0) partial_pay from loans l, loan_txns t 
                               where l.loan_doc_id = t.loan_doc_id and date(disbursal_date) <= '{date}'
                               and (date(paid_date) > '{date}' or paid_date is null)
                               and datediff('{date}', due_date) > 1 and date(txn_date) <= '{date}'
                               and txn_type in ('payment')
                               {ignore_write_offs_condn.format('l.')} and {NOT_FLOAT_VENDING} and {DISBURSED}
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


def get_max_os_val(month, country_code, acc_prvdr_code, current_os):
    query = f"""select IFNULL(os_val_eom,0) max_os, month from monthly_mgmt_reports where os_val_eom = 
                (select max(os_val_eom) from monthly_mgmt_reports where
                month <= {month} and {for_e(country_code, acc_prvdr_code, True)})
                order by month desc limit 1"""

    result = extract_from_query(query, db_rep)

    if (current_os != None and (result.get('max_os') is None or result.get('max_os') < current_os)):
        return f"{current_os},{month}"

    return f"{result.get('max_os')},{result.get('month')}"


def get_ontime_repay_rate(month, country_code, acc_prvdr_code):
    query = f"""SELECT sum(ontime_count)/sum(settled_count) AS ontime_rate from live_reports where 
                EXTRACT(YEAR_MONTH from report_date) = {month} {for_e(country_code, acc_prvdr_code, prefix= "and")} """
    ontime_repayment_rate = extract_from_query(query, db_rep).get('ontime_rate')

    return ontime_repayment_rate


def get_new_overdue_count(month, country_code, acc_prvdr_code):
    # date = datetime.datetime.strptime(month, '%Y%m')
    date = get_last_date(month)
    query = f"""SELECT count(loan_doc_id) as overdue_count from loans where {for_e(country_code, acc_prvdr_code)} 
            and date(due_date) >= (LAST_DAY('{date}') - INTERVAL 1 MONTH - INTERVAL 5 DAY)
            {ignore_write_offs_condn.format('')} and {NOT_FLOAT_VENDING} and {DISBURSED}
            and datediff('{date}', due_date) > 5
            and (date(paid_date) > '{date}' or paid_date is null)"""

    new_overdue_count = extract_from_query(query, db).get('overdue_count')

    return new_overdue_count


def get_new_overdue_perc(month, country_code, acc_prvdr_code, new_overdue_count):
    query = f"""SELECT count(loan_doc_id) as due_count from loans where EXTRACT(YEAR_MONTH from due_date) = '{month}' 
            {for_e(country_code, acc_prvdr_code, prefix="and")} and {DISBURSED} and {NOT_FLOAT_VENDING}"""

    curr_month_due_count = extract_from_query(query, db).get('due_count')

    new_overdue_perc =  0 if new_overdue_count / curr_month_due_count == inf else new_overdue_count / curr_month_due_count

    return new_overdue_perc


def get_new_overdue_val(month, country_code, acc_prvdr_code):
    date = datetime.datetime.strptime(month, '%Y%m')
    date = get_last_date(month)
    entity = 'country_code' if acc_prvdr_code is None else 'acc_prvdr_code'
    principal = f"""SELECT sum(loan_principal) as loan_principal, {entity} from loans where {for_e(country_code, acc_prvdr_code)}
                and date(due_date) >= (LAST_DAY('{date}') - INTERVAL 1 MONTH - INTERVAL 5 DAY)
                {ignore_write_offs_condn.format('')} and {DISBURSED} and {NOT_FLOAT_VENDING}
                and datediff('{date}', due_date) > 5
                and (date(paid_date) > '{date}' or paid_date is null)"""

    partial_pay = f"""SELECT sum(amount) as pp, l.{entity} from loans l, loan_txns t where {for_e(country_code, acc_prvdr_code, alias='l')} and 
                l.loan_doc_id = t.loan_doc_id and date(due_date) >= (LAST_DAY('{date}') - INTERVAL 1 MONTH - INTERVAL 5 DAY) 
                and (date(paid_date) > '{date}' or paid_date is null)
                and date(txn_date) <= '{date}' and datediff('{date}', due_date) > 5 and txn_type in ('payment') and {DISBURSED}
                {ignore_write_offs_condn.format('l.')} and {NOT_FLOAT_VENDING}"""

    os_query = f"""SELECT loan_principal, pp, (IFNULL(loan_principal,0) - IFNULL(pp,0)) AS os from ({principal}) as principal
                left join ({partial_pay}) as pp on principal.{entity} = pp.{entity}"""

    val_os = extract_from_query(os_query, db).get('os')

    return val_os


def get_revenue(month, country_code, acc_prvdr_code):
    revenue_condns = f"""l.loan_doc_id = t.loan_doc_id
              and {DISBURSED} and {NOT_FLOAT_VENDING}
              and EXTRACT(YEAR_MONTH from txn_date) = {month}
              and txn_type in ('payment') 
              and {for_e(country_code, acc_prvdr_code, alias='l')}"""

    query = f""" select sum(ifnull(fee,0) + ifnull(penalty,0) + ifnull(excess,0)) month_revenue from loans l, loan_txns t where {revenue_condns}"""
    revenue = extract_from_query(query, db).get('month_revenue')

    sql = f"""select IFNULL(sum(amount),0) excess_reversed from  loans l, loan_txns t where l.loan_doc_id = t.loan_doc_id
              and {DISBURSED} and {NOT_FLOAT_VENDING}
              and EXTRACT(YEAR_MONTH from txn_date) = {month}
              and txn_type = 'excess_reversal'
              and {for_e(country_code, acc_prvdr_code, alias='l')}"""
    excess_reversed = extract_from_query(sql, db).get('excess_reversed')

    revenue -= excess_reversed

    return revenue, excess_reversed


def get_biz_supported_count(month, country_code, acc_prvdr_code):
    query = f"""SELECT count(*) AS supported_count from borrowers where tot_loans > 0 and {for_e(country_code, acc_prvdr_code)} 
                and EXTRACT(YEAR_MONTH from reg_date) <= {month}"""

    biz_supported_count = extract_from_query(query, db).get('supported_count')

    return biz_supported_count


def get_cust_percentages(month, country_code, acc_prvdr_code):
    # date = datetime.datetime.strptime(month, '%Y%m')
    date = get_last_date(month)
    query = f"""SELECT sum(IF(TIMESTAMPDIFF(Month,dob,'{date}') <= 360,1,0))/count(*) AS youth_perc,
            sum(IF(gender = 'female',1,0))/count(*) AS female_perc from borrowers b, persons p where 
            b.owner_person_id = p.id {for_e(country_code, acc_prvdr_code, alias='b', prefix="and")} 
            and EXTRACT(YEAR_MONTH from reg_date) <= {month}"""
    result = extract_from_query(query, db)
    youth_perc = result.get('youth_perc')
    female_perc = result.get('female_perc')

    return youth_perc, female_perc


def get_revenue_by_small_biz(month, country_code, active_cust_count):
    query = f"SELECT currency_code from markets where country_code = '{country_code}'"

    currency_code = extract_from_query(query, db).get('currency_code')
    forex_usd = get_month_forex('USD', currency_code, month, db)

    revenue_gen_by_biz = active_cust_count * AVG_CUST_REVENUE_PER_MONTH_IN_USD * forex_usd

    return revenue_gen_by_biz


def get_fee_earned_per_cust_and_fa(month, country_code, acc_prvdr_code, only_fee_earned = False):
    sql = f"""select IFNULL(sum(flow_fee), 0) fee, IFNULL(count(distinct cust_id), 0) cust_count,
                IFNULL(count(distinct loan_doc_id), 0) fa_count 
                from loans where {for_e(country_code, acc_prvdr_code)}
                and EXTRACT(YEAR_MONTH from paid_date) = {month} and {DISBURSED} and {NOT_FLOAT_VENDING}"""

    result = extract_from_query(sql, db)
    fee_earned = result.get('fee')
    if only_fee_earned:
        return fee_earned
    cust_count = result.get('cust_count')
    fa_count = result.get('fa_count')
    fee_per_cust = fee_earned / cust_count
    fee_per_fa = fee_earned / fa_count
    return fee_per_cust, fee_per_fa


def get_tot_cust_count_and_tot_fa_count_of_current_month(month,country_code,acc_prvdr_code):
    sql = f"""select IFNULL(count(distinct cust_id), 0) cust_count,
                    IFNULL(count(distinct loan_doc_id), 0) fa_count 
                    from loans where EXTRACT(YEAR_MONTH from paid_date) = {month} {for_e(country_code, acc_prvdr_code, prefix="and")} and {DISBURSED} and {NOT_FLOAT_VENDING}"""
    result = extract_from_query(sql, db)
    cust_count = result.get('cust_count')
    fa_count = result.get('fa_count')

    return cust_count, fa_count

def get_tot_rm_count_of_current_month_active_cust(month,country_code,acc_prvdr_code):
        sql = f"""select IFNULL(count(distinct  flow_rel_mgr_id), 0) flow_rel_mgr_id
                    from loans where EXTRACT(YEAR_MONTH from paid_date) = {month} {for_e(country_code, acc_prvdr_code, prefix="and")} and {DISBURSED} and {NOT_FLOAT_VENDING}"""
        result = extract_from_query(sql, db)
        flow_rel_mgr_id = result.get('flow_rel_mgr_id')
     

        return flow_rel_mgr_id

def get_last_date(month):
    date = (datetime.datetime.strptime(month, "%Y%m") + relativedelta(day=31)).strftime("%Y-%m-%d")
    crnt_date = datetime.datetime.now()
    crnt_month = crnt_date.strftime("%Y%m")
    if str(month) == str(crnt_month):
        date = datetime.datetime.now()
    return date
# TODO
# def get_rm_count(month, country_code, acc_prvdr_code):
#     sql = f"""SELECT count(distinct(flow_rel_mgr_id)) rm_count 
#               from borrowers where {for_e(country_code, acc_prvdr_code)}"""


def get_global_max_os(cnrt_max, max_now_usd):
    crnt_max_ar = cnrt_max.split(",")
    max_now_ar = max_now_usd.split(",")
    max_month = crnt_max_ar[1]
    if max_month == "None":
        return f"{None}, {None}"
    max_os_query = f"select max_os_val,country_code from monthly_mgmt_reports where month = {max_month} and country_code != '*'"
    max_os_result = pd.read_sql(max_os_query, db_rep)
    new_max_usd = 0
    for index, result in max_os_result.iterrows():
        country_c = result['country_code']
        query = f"SELECT currency_code from markets where country_code = '{country_c}'"
        currency_code = extract_from_query(query, db).get('currency_code')
        currency_code =  currency_code
        print(f"{country_c} - usd - {currency_code} -{max_month} -{month}")
        forex_usd = get_month_forex('USD', currency_code, month, db)
        new_max = result['max_os_val']
        new_max_ar = new_max.split(",")
        if new_max_ar[0] != "None":
            new_max_usd += float(new_max_ar[0]) / forex_usd
    if new_max_usd >= float(max_now_ar[0]):
        return f"{round(new_max_usd,3)},{max_month}"
    else:
        return max_now_usd



def generate_report(month):
    country_codes = map_ap_to_country(db)
    db_rep.execute(f"delete from monthly_mgmt_reports where month = {month}")

    for country_code, acc_prvdr_codes in country_codes.items():
        if country_code == "RWA" and int(month) < 202205:
            continue
        global ignore_write_offs_condn
        end_of_month = (datetime.datetime.strptime(month, "%Y%m") + relativedelta(day=31)).strftime("%Y-%m-%d")
        ignore_write_offs_condn = get_ignore_written_off_condn(country_code, end_of_month, db, alias='{0}')
        acc_prvdr_codes.append(None)
        for acc_prvdr_code in acc_prvdr_codes:
            cust_reg_count = get_reg_cust_count(month, country_code, acc_prvdr_code)

            cust_active_count = get_active_cust_count(month, country_code, acc_prvdr_code)

            cust_churn_count, cust_churn_perc = get_cust_churn_perc(month, country_code, acc_prvdr_code)

            tot_disb_val, tot_disb_count = get_tot_disb_val(month, country_code, acc_prvdr_code)

            tot_fa_settled_count = get_fas_settled_count(month, country_code, acc_prvdr_code)

            gross_txn_val = get_gross_txn_val(month, country_code, acc_prvdr_code)

            os_val_eom = get_val_os_eom(month, country_code, acc_prvdr_code)

            os_fee_eom, os_count_eom = get_fee_count_os(month, country_code, acc_prvdr_code)

            od_amount, od_count = get_od_values(month, country_code, acc_prvdr_code)

            max_os_val = get_max_os_val(month, country_code, acc_prvdr_code, os_val_eom)

            ontime_repayment_rate = get_ontime_repay_rate(month, country_code, acc_prvdr_code)

            new_overdue_count = get_new_overdue_count(month, country_code, acc_prvdr_code)

            due_perc = get_new_overdue_perc(month, country_code, acc_prvdr_code, new_overdue_count)

            new_overdue_val = get_new_overdue_val(month, country_code, acc_prvdr_code)

            revenue, excess_reversed = get_revenue(month, country_code, acc_prvdr_code)

            biz_supported_count = get_biz_supported_count(month, country_code, acc_prvdr_code)

            youth_perc, female_perc = get_cust_percentages(month, country_code, acc_prvdr_code)

            retail_txn_count = tot_disb_count * AVG_RETAIL_TXNS_FUNDED_PER_FA

            retail_txn_val = tot_disb_val * CAPITAL_MULT_FACTOR_FOR_AGENT

            revenue_by_small_biz = get_revenue_by_small_biz(month, country_code, cust_active_count)

            people_benefited = cust_active_count * AVG_RETAIL_CUSTS_PER_AGENT

            fee_per_cust, fee_per_fa = get_fee_earned_per_cust_and_fa(month, country_code, acc_prvdr_code)

            cust_count_for_rev_calc,fa_count=get_tot_cust_count_and_tot_fa_count_of_current_month(month,country_code,acc_prvdr_code)

            rev_per_cust=revenue/cust_count_for_rev_calc

            rm_count_for_rev_calc=get_tot_rm_count_of_current_month_active_cust(month,country_code,acc_prvdr_code)

            rev_per_rm=revenue/rm_count_for_rev_calc


            run_at = datetime.datetime.now()

            insert_records('monthly_mgmt_reports', db_rep, locals())

    generate_report_global(month)


def generate_report_global(month):
    results = pd.read_sql(f"select * from monthly_mgmt_reports where month = {month} and acc_prvdr_code is null",
                          db_rep)
    loc = True
    cust_count = fa_count = 0
    if loc:
        cust_reg_count = cust_active_count = cust_churn_count = tot_disb_count = tot_fa_settled_count = os_count_eom = od_count = new_overdue_count = biz_supported_count = retail_txn_count = 0
        people_benefited = tot_disb_val = gross_txn_val = revenue_by_small_biz = retail_txn_val = excess_reversed = revenue = new_overdue_val  = od_amount = os_fee_eom = os_val_eom = fee_earned =0
        max_os_val = "0,0"
        country_code = '*'
        acc_prvdr_code = None

        for index, result in results.iterrows():
            country_c = result['country_code']
            query = f"SELECT currency_code from markets where country_code = '{country_c}'"
            currency_code = extract_from_query(query, db).get('currency_code')
            forex_usd = get_month_forex('USD', currency_code, month, db)
            cust_reg_count += result['cust_reg_count']
            cust_active_count += result['cust_active_count']
            tot_disb_count += result['tot_disb_count']
            tot_fa_settled_count += result['tot_fa_settled_count']
            os_count_eom += result['os_count_eom']
            od_count += result['od_count']
            new_overdue_count += result['new_overdue_count']
            biz_supported_count += result['biz_supported_count']
            retail_txn_count += result['retail_txn_count']
            people_benefited += result['people_benefited']
            tot_disb_val += result['tot_disb_val'] / forex_usd
            gross_txn_val += result['gross_txn_val'] / forex_usd
            os_val_eom += (result['os_val_eom'] if result['os_val_eom'] is not None else 0) / forex_usd
            os_fee_eom += (result['os_fee_eom'] if result['os_fee_eom'] is not None else 0) / forex_usd
            od_amount += (result['od_amount'] if result['od_amount'] is not None else 0) / forex_usd
            max_os_val = get_global_max_os(result["max_os_val"], max_os_val)
            new_overdue_val += (result['new_overdue_val'] if result['new_overdue_val'] is not None else 0) / forex_usd
            revenue += result['revenue'] / forex_usd
            fee_earned += get_fee_earned_per_cust_and_fa(month, result['country_code'], acc_prvdr_code, True) / forex_usd
            excess_reversed += (result['excess_reversed'] if result['excess_reversed'] is not None else 0) / forex_usd
            retail_txn_val += result['retail_txn_val'] / forex_usd
            revenue_by_small_biz += result['revenue_by_small_biz'] / forex_usd
        ontime_repayment_rate = get_ontime_repay_rate(month, country_code, acc_prvdr_code)
        cust_churn_count, cust_churn_perc = get_cust_churn_perc(month, country_code, acc_prvdr_code)
        due_perc = get_new_overdue_perc(month, country_code, acc_prvdr_code, new_overdue_count)
        youth_perc, female_perc = get_cust_percentages(month, country_code, acc_prvdr_code)
        cust_count_for_rev_calc, fa_count = get_tot_cust_count_and_tot_fa_count_of_current_month(month ,country_code, acc_prvdr_code)
        rm_count_for_rev_calc = get_tot_rm_count_of_current_month_active_cust(month ,country_code, acc_prvdr_code)
        fee_per_cust, fee_per_fa = (fee_earned / cust_count_for_rev_calc), (fee_earned / fa_count)
        rev_per_rm=revenue/rm_count_for_rev_calc
        rev_per_cust=revenue/cust_count_for_rev_calc
        run_at = datetime.datetime.now()

        insert_records('monthly_mgmt_reports', db_rep, locals())


def get_month_range(till_now = False):
    sql = f"select max(month) max from monthly_mgmt_reports"
    date = extract_from_query(sql, db_rep).get('max')
    crnt_date = datetime.datetime.now() - relativedelta(days=1)
    crnt_month = crnt_date.strftime("%Y%m")
    if str(date) == str(crnt_month):
        date = datetime.datetime.now() - relativedelta(months=1, day=31)
        date = date.strftime("%Y%m")
    if date is None:
        start_date = '2018-12-01'
    else:
        start_date = (datetime.datetime.strptime(str(date), "%Y%m") + relativedelta(day=31)).strftime("%Y-%m-%d")
    if till_now:
        date_range = pd.date_range(start=start_date,
                                end=((datetime.datetime.now()).strftime("%Y-%m-%d")))
    else:
        date_range = pd.date_range(start=start_date,
                                end=((datetime.datetime.now() - relativedelta(months=1)).strftime("%Y-%m-%d")))
    month_range = date_range[date_range.day == 1]
    return month_range


if __name__ == "__main__":
    try:
        transaction = db_rep.begin()
        if len(sys.argv) > 1:
            month = sys.argv[1]
            generate_report(month)
        else:
            month_range = get_month_range(True)
            for month in month_range:
                month = month.strftime("%Y%m")
                generate_report(month)
        transaction.commit()
    except Exception as e:
        transaction.rollback()
        traceback.print_exc()

    db.close()
    db_rep.close()

    db_eng.dispose()
    db_rep_eng.dispose()
