import datetime as dt
import sys
import calendar
from dateutil.relativedelta import relativedelta
import numpy as np
import pandas as pd
import traceback
from sqlalchemy import create_engine
from reports_common import get_month_forex, extract_from_query, map_fund_to_country, get_float_vend_products

from flow_common import db_rep_str, db_str, load_env, BAD_DEBT_CUTOFF_DAYS, WRITE_OFF_STATUS, STATUSES_BEFORE_DISBURSAL

load_env()
db_eng = create_engine('mysql+mysqlconnector://' + db_str('STMT'))
db_rep_eng = create_engine('mysql+mysqlconnector://' + db_rep_str("STMT"))

db = db_eng.connect()
db_rep = db_rep_eng.connect()


def get_max_os(fund_code, month):
    print("os")

    fund_query = f"select  alloc_date, country_code from capital_funds where fund_code = '{fund_code}'"
    fund_df = pd.read_sql(sql=fund_query, con=db)
    alloc_date = fund_df['alloc_date'][0]
    alloc_date = dt.datetime.strptime(str(alloc_date), '%Y-%m-%d %H:%M:%S')
    country_code = fund_df['country_code'][0]
    start_date = dt.datetime.strptime(str(month), '%Y%m')
    end_date = start_date + relativedelta(day=31)
    prins_oss = [0]
    if (alloc_date > start_date):
        start_date = alloc_date
    while start_date != end_date:
        princ_os = get_os_value('country_code', country_code, "", start_date)
        if (princ_os['par_loan_principal'][0] != None):
            prins_oss.append(princ_os['par_loan_principal'][0])
        start_date = start_date + dt.timedelta(1)
    max_os = max(prins_oss)
#     if (alloc_date > actual_date):
#         delta = (alloc_date - start_date)
#         delta = 30 - delta.days
#         max_os_stake = max_os_stake * (delta / 30)
    return max_os

def get_os_value(entity, entity_code, addl_condn="", date=""):
    if date != "":
        db.execute(f"set @date = '{date}'")

    principal_query = f"select @date month_end , sum(loan_principal) principal, l.{entity} from loans l\
                               where l.{entity} = '{entity_code}' and date(disbursal_Date) <= @date\
                               and ( date(paid_date) > @date or paid_date is null) and status not in {STATUSES_BEFORE_DISBURSAL} and product_id {not_in_fv} and (write_off_status not in {WRITE_OFF_STATUS} or write_off_status is null) "

    partial_pay_query = f"select @date month_end, sum(amount) partial_pay , l.{entity} from loans l, loan_txns t\
                                 where l.{entity} = '{entity_code}' and l.loan_doc_id = t.loan_doc_id\
                                 and date(disbursal_Date) <= @date\
                                 and (date(paid_date) > @date or paid_date is null)\
                                 and date(txn_date) <= @date and txn_type = 'payment' and l.status not in {STATUSES_BEFORE_DISBURSAL} and product_id {not_in_fv} and (write_off_status not in {WRITE_OFF_STATUS} or write_off_status is null)  "

    sql = f"select pri.{entity}, pp.partial_pay as partial_paid, (principal - IFNULL(partial_pay,0)) par_loan_principal\
                         				from ({principal_query + addl_condn}) pri left join ({partial_pay_query + addl_condn}) pp\
                         				on pri.month_end = pp.month_end and pri.{entity} = pp.{entity}"

    df = pd.read_sql_query(sql, con=db)

    return df


def get_yearly_returns(fund_code, month):
    fund_query = f"select alloc_amount_fc, alloc_date, fe_currency_code, country_code from capital_funds where fund_code = '{fund_code}'"
    fund_df = pd.read_sql(sql=fund_query, con=db)
    alloc_amount = fund_df['alloc_amount_fc'][0]
    alloc_date = fund_df['alloc_date'][0]

    alloc_date = dt.datetime.strptime(str(alloc_date), '%Y-%m-%d %H:%M:%S')
    report_date = dt.datetime.strptime(str(month), '%Y%m') + relativedelta(day=31)
    datediff = report_date - alloc_date
    month_diff = datediff.days / 30.42
    running_rate = 1
    if month_diff < 1:
        running_rate = month_diff
    yearly_returns = ((1 / 12) * alloc_amount) * running_rate
    return yearly_returns


def table_exists():
    table_list = pd.read_sql(con=db_rep, sql="SHOW TABLES").iloc[:, 0].tolist()
    if 'bonds_monthly' not in table_list:
        sql = f"CREATE TABLE bonds_monthly (\
                id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,\
                `month` bigint DEFAULT NULL,\
                `country` text,\
                `fund_code` text,\
                `tot_fee_rcvd` double DEFAULT NULL,\
                `tot_principal_os` double DEFAULT NULL,\
                `tot_disbursed` double DEFAULT NULL,\
                `commission` double DEFAULT NULL,\
                `bad_debts` double DEFAULT NULL,\
                `bad_debts_recovered` double DEFAULT NULL,\
                `license_fee` double DEFAULT NULL,\
                `net_returns` double DEFAULT NULL,\
                `cust_revenue` double DEFAULT NULL,\
                `people_benefited` double DEFAULT NULL,\
                `tot_retail_txn_value` double DEFAULT NULL,\
                'num_of_txn' int unsigned DEFAULT NULL,\
                `male_perc` double(5,3) DEFAULT NULL,\
                `female_perc` double(5,3) DEFAULT NULL,\
                `current_alloc_cust` int unsigned DEFAULT NULL\
                )"

        db_rep.execute(sql)


def generate_monthly_data():
    table_exists()
    countries = map_fund_to_country(db)
    print(countries)
    for country_code, fund_codes in countries.items():

        for fund_code in fund_codes:

            month_range = get_range(fund_code)
            fund_df = pd.read_sql(sql=f"select EXTRACT(YEAR_MONTH FROM alloc_date) as month, date(alloc_date) as alloc_date, alloc_amount, alloc_amount_fc, profit_rate, forex,\
                                                    date(calc_alloc_date) as calc_alloc_date, EXTRACT(YEAR_MONTH FROM calc_alloc_date) as calc_month,\
                                                    IFNULL(license_rate,0) as license_rate, fund_type, fe_currency_code, current_alloc_cust\
                                                     from capital_funds where fund_code = '{fund_code}'", con=db)
            alloc_date = fund_df['alloc_date'][0]
            disb_till_last_month = flow_in_till_now  = flow_fee_till_last_month = commission_till_last_month = bd_debts_till_last_month = bd_debt_rcvd_till_last_month = 0
            curr_disb = flow_fee_test= tot_bad_debts_recovered= bond_comm = bad_debts = curr_flow_fee = curr_commission = curr_license_fee = curr_bd_debts = curr_bd_debt_rcvd = 0
            for month in month_range:
                end_of_month = dt.datetime.strptime(str(month), '%Y%m') + relativedelta(day=31)
                end_of_month = end_of_month.strftime('%Y-%m-%d')

                db.execute(f"set @month = {month}")

                disb_query = f"select IFNULL(sum(amount),0) disb_amount from loans l, loan_txns t \
                                    where l.loan_doc_id = t.loan_doc_id and  l.fund_code = '{fund_code}' and \
                                    EXTRACT(YEAR_MONTH FROM txn_date) <= @month and txn_type = 'disbursal'"

                disb_count_query = f"select count(amount) disb_count from loans l, loan_txns t \
                                     where l.loan_doc_id = t.loan_doc_id and  l.fund_code = '{fund_code}' and \
                                     EXTRACT(YEAR_MONTH FROM txn_date) <= @month and txn_type = 'disbursal'"

                tot_disb_fund = extract_from_query(disb_query, db).get('disb_amount')
                tot_disb_count = extract_from_query(disb_count_query, db).get('disb_count')
                curr_disb = tot_disb_fund - disb_till_last_month
                disb_till_last_month += curr_disb

                principal_query = f"select @month month , sum(loan_principal) principal, l.fund_code from loans l\
                                where l.fund_code = '{fund_code}' and EXTRACT(YEAR_MONTH FROM disbursal_Date) <= @month\
                                and ( EXTRACT(YEAR_MONTH FROM paid_date) > @month or paid_date is null )"

                partial_pay_query = f"select @month month, sum(amount) partial_pay , l.fund_code from loans l, loan_txns t\
                                    where l.fund_code = '{fund_code}' and l.loan_doc_id = t.loan_doc_id\
                                    and EXTRACT(YEAR_MONTH FROM disbursal_Date) <= @month\
                                    and (EXTRACT(YEAR_MONTH FROM paid_date) > @month or paid_date is null)\
                                    and EXTRACT(YEAR_MONTH FROM txn_date) <= @month and txn_type = 'payment' "

                os_query = f"select pri.month, pri.fund_code, principal, partial_pay, (principal - IFNULL(partial_pay,0)) os\
                            from ({principal_query}) pri left join ({partial_pay_query}) pp\
                            on pri.month = pp.month and pri.fund_code = pp.fund_code"

                tot_principal_os = extract_from_query(os_query, db).get('os')

                # Get Net Return based on Fund Type
                fund_type = fund_df['fund_type'][0]
                if fund_type == 'fixed_coupon':
                    fee_rcvd = total_commission = bond_comm = license_fee = bad_debts = bad_debts_recovered = None
                    fund_returns = get_yearly_returns(fund_code, month)
                    flow_inv_curr_month = flow_return_rate = fund_return_rate = 0

                else:

                    total_flow_fee_rcvd_query = f"select IFNULL(sum(flow_fee),0) tot_fee_rcvd\
                                                    from loans l where\
                                                    paid_date is not null and date(disbursal_date) >= '{alloc_date}'\
                                                    and EXTRACT(YEAR_MONTH FROM paid_date) <= @month \
                                                    and country_code = '{country_code}'"
                    total_flow_fee_rcvd = extract_from_query(total_flow_fee_rcvd_query, db).get('tot_fee_rcvd')
                    curr_flow_fee = total_flow_fee_rcvd - flow_fee_till_last_month
                    flow_fee_till_last_month += curr_flow_fee


                    '''tot_disb_query = f"select IFNULL(sum(amount),0) amount from loans l, loan_txns t \
                                       where l.loan_doc_id = t.loan_doc_id and date(txn_date) >= '{alloc_date}'\
                                       and EXTRACT(YEAR_MONTH FROM txn_date) <= @month and txn_type = 'disbursal'\
                                       and l.country_code = '{country_code}'"
                    tot_disb = extract_from_query(tot_disb_query, db).get('amount')'''


                    alloc_month = fund_df['month'][0]
                    total_commission_query = f"select IFNULL(sum(total_paid),0) as commission from commissions where country_code = '{country_code}' and month >= {alloc_month} and month <= @month"
                    total_commission = extract_from_query(total_commission_query, db, ).get('commission')
                    total_commission = total_commission if total_commission else 0

                    curr_commission = total_commission - commission_till_last_month
                    commission_till_last_month += curr_commission

                    license_fee_rate = fund_df['license_rate'][0]
                    license_fee = (total_flow_fee_rcvd * license_fee_rate)
                    curr_license_fee = curr_flow_fee * license_fee_rate

                    bd_principal_query = f"select IFNULL(sum(loan_principal),0) tot_principal from loans \
                                                 where date(disbursal_date) >= '{alloc_date}' and date(disbursal_date) <= '{end_of_month}' \
                                                 and datediff('{end_of_month}', date(due_date)) >= {BAD_DEBT_CUTOFF_DAYS} \
                                                 and (datediff(paid_date, due_date) >= {BAD_DEBT_CUTOFF_DAYS} or paid_date is null) \
                                                 and country_code ='{country_code}'"
                    bd_principal = extract_from_query(bd_principal_query, db).get('tot_principal')

                    bd_pp_query = f"""select IFNULL(sum(amount),0) tot_paid
                                                from loans l, loan_txns t
		                                        where l.loan_doc_id = t.loan_doc_id
                                                and txn_type = 'payment' and date(txn_date) <= '{end_of_month}'
                                                and date(disbursal_date) >= '{alloc_date}' and date(disbursal_date) <= '{end_of_month}'
                                                and datediff('{end_of_month}', date(due_date)) >= {BAD_DEBT_CUTOFF_DAYS}
                                                and (datediff(paid_date, due_date) >= {BAD_DEBT_CUTOFF_DAYS} or paid_date is null)
                                                and datediff(txn_date, due_date) <= {BAD_DEBT_CUTOFF_DAYS}
                                                and l.country_code ='{country_code}'"""


                    # and txn_date <= date_add(due_date, interval {BAD_DEBT_CUTOFF_DAYS} day)

                    # and date(due_date) >= '{alloc_date}' \
                    # and (datediff(paid_date, due_date) >= {BAD_DEBT_CUTOFF_DAYS} or paid_date is null) \
                    # and datediff('{end_of_month}', date(due_date)) >= {BAD_DEBT_CUTOFF_DAYS} \
                    # and l.country_code = '{country_code}'"
                    bd_pp = extract_from_query(bd_pp_query, db)
                    tot_part_paid = bd_pp.get('tot_paid')
                    tot_bad_debts = bd_principal - tot_part_paid
                    curr_bd_debts = tot_bad_debts - bd_debts_till_last_month
                    bd_debts_till_last_month += curr_bd_debts



                    bad_debts_recovered_query = f"""select IFNULL(sum(amount),0) recovered, IFNULL(sum(if(paid_date < '{end_of_month}', flow_fee, 0)),0) fee_paid
                                                from loans l, loan_txns t
		                                        where l.loan_doc_id = t.loan_doc_id and txn_date <= '{end_of_month}'
                                                and txn_type = 'payment' and date(disbursal_date) >= '{alloc_date}'
                                                and datediff(txn_date, due_date) > {BAD_DEBT_CUTOFF_DAYS}
                                                and l.country_code = '{country_code}'"""

                    # and txn_date > date_add(due_date, interval {BAD_DEBT_CUTOFF_DAYS} day) \
                    bd_res = extract_from_query(bad_debts_recovered_query, db)
                    tot_recovered = bd_res.get('recovered')
                    fee_recovered = bd_res.get('fee_paid')
                    fee_recovered =  fee_recovered  if fee_recovered else 0
                    tot_bad_debts_recovered = tot_recovered - fee_recovered
                    curr_bd_debt_rcvd = tot_bad_debts_recovered - bd_debt_rcvd_till_last_month
                    bd_debt_rcvd_till_last_month += curr_bd_debt_rcvd


                    # if(fund_df['calc_alloc_date'][0]):
                    #     adjust_perc = get_adjustment_percent(fund_df['alloc_date'][0],fund_df['calc_alloc_date'][0])
                    #     fee_rcvd -= (adjust_perc * fee_rcvd)
                    #     bond_comm -= (adjust_perc * bond_comm)
                    #     license_fee -= (adjust_perc * license_fee)
                    #     bad_debts -= (adjust_perc * bad_debts)
                    #     bad_debts_recovered -= (adjust_perc * bad_debts_recovered)
                    flow_expenses = total_commission  + license_fee
                    nett_bad_debts = tot_bad_debts - tot_bad_debts_recovered

                    flow_in_until_end_of_month = total_flow_fee_rcvd - flow_expenses -  nett_bad_debts

                    flow_in_curr_month = flow_in_until_end_of_month - flow_in_till_now
                    flow_in_till_now += flow_in_curr_month

                    # net_returns = fee_rcvd - bond_comm - license_fee - float(bad_debts) + bad_debts_recovered
                    flow_inv_curr_month = get_max_os(fund_code, month)

                    flow_return_rate = flow_in_curr_month / flow_inv_curr_month

                    fund_returns = fund_df['alloc_amount_fc'][0] * flow_return_rate

                    fund_return_rate = fund_returns / (flow_in_curr_month / fund_df['forex'][0])


                # Social Return Data
                cust_count_query = f"select count(*) count from client_performance_funds where fund_code = '{fund_code}'"
                cust_count = extract_from_query(cust_count_query, db_rep).get('count')

                fund_currency_code = fund_df['fe_currency_code'][0]

                country_currency_code_query = f"select currency_code from markets where country_code = '{country_code}'"
                country_currency_code = extract_from_query(country_currency_code_query, db).get('currency_code')

                cust_revenue = 0.8 * cust_count * 105 * get_month_forex('USD', fund_currency_code, month,
                                                                        db) * get_month_forex(fund_currency_code,
                                                                                              country_currency_code,
                                                                                              month, db)

                people_benefited = cust_count * 500

                tot_retail_txn_value = tot_disb_fund * 1.25

                num_of_txn = tot_disb_count * 340

                gender_perc_query = f"select ((select count(*) from client_performance_funds where gender ='female' and fund_code = '{fund_code}' ) / count(*)) female_perc, ((select count(*) from client_performance_funds where gender ='Male' and fund_code = '{fund_code}') / count(*)) male_perc  from client_performance_funds where fund_code = '{fund_code}'"
                gender_perc_df = pd.read_sql(sql=gender_perc_query, con=db_rep)
                male_perc = gender_perc_df['male_perc'][0]
                female_perc = gender_perc_df['female_perc'][0]

                current_alloc_cust = fund_df['current_alloc_cust'][0]

                table_data = {'month': [month], 'country': [country_code], 'fund_code': [fund_code],
                              'bad_debts_recovered': [curr_bd_debt_rcvd * fund_return_rate],
                              'tot_fee_rcvd': [curr_flow_fee * fund_return_rate], 'tot_principal_os': [tot_principal_os],
                              'current_alloc_cust': [current_alloc_cust],
                              'tot_disbursed': [curr_disb], 'commission': [curr_commission * fund_return_rate], 'bad_debts': [curr_bd_debts * fund_return_rate],
                              'license_fee': [curr_license_fee * fund_return_rate],
                              'net_returns': [fund_returns], 'cust_revenue': [cust_revenue],
                              'people_benefited': [people_benefited],
                              'tot_retail_txn_value': [tot_retail_txn_value], 'num_of_txn': [num_of_txn],
                              'male_perc': [male_perc], 'female_perc': [female_perc]}

                table_record = pd.DataFrame(table_data)
                pd.options.display.float_format = '{:.2f}'.format
                table_record.to_sql(con=db_rep, name='bonds_monthly', index=False, if_exists='append')


def get_range(fund_code):
    rs = pd.read_sql(sql=f"select IFNULL(max(month),0) as month from bonds_monthly where fund_code = '{fund_code}' ",
                     con=db_rep)
    if rs['month'][0] == 0:
        start_date = pd.read_sql(
            sql=f"select EXTRACT(YEAR_MONTH FROM alloc_date) as month from capital_funds where fund_code = '{fund_code}'",
            con=db)
        date = dt.datetime.strptime(str(start_date['month'][0]), '%Y%m')

    else:
        date = dt.datetime.strptime(str(rs['month'][0]), "%Y%m") + relativedelta(months=1)

    now = dt.datetime.now()
    month_range = []
    while ((date.year * 100) + date.month < (now.year * 100) + now.month):
        month_range.append((date.year * 100) + date.month)
        date = date + relativedelta(months=1)
    return month_range


# year = int(sys.argv[1]) if len(sys.argv)>1 else dt.datetime.now().year
# month = int(sys.argv[1]) if len(sys.argv)>2 else None
# run(year, month)
try:
    transaction = db_rep.begin()
    fv_products = get_float_vend_products(db)
    not_in_fv = f"not in {fv_products}"
    generate_monthly_data()
    transaction.commit()
except Exception as e:
    transaction.rollback()
    traceback.print_exc()

db.close()
db_rep.close()

db_eng.dispose()
db_rep_eng.dispose()
