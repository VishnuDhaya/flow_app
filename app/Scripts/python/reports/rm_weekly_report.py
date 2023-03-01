env = {}
import pandas as pd
from flow_common import db_str,db_rep_str,load_env, db_read_only_str
from sqlalchemy import create_engine
from datetime import date, timedelta
from dateutil.relativedelta import relativedelta, MO, SU
from functools import reduce
import sys
from dateutil.parser import parse
from simple_email import send_simple_mail
import traceback

load_env()

db_rep = create_engine('mysql+mysqlconnector://'+db_rep_str('REPORT'))
db_read_only = create_engine('mysql+mysqlconnector://'+db_read_only_str('READ_ONLY'))
db_rep = db_rep.connect()

if len(sys.argv) > 1 :
    today = sys.argv[1]
    today = parse(today)
else:
    today = date.today()

last_monday = today + relativedelta(weekday=MO(-3)) 
last_sunday = today + relativedelta(weekday=SU(-2)) 

current_monday = today + relativedelta(weekday=MO(-2))
current_sunday = today + relativedelta(weekday=SU(-1)) 
active_date = current_monday - timedelta(30)

def run():
    
    try:
        trans = db_rep.begin()
        
        data_exists()
        merge_all_report()
        # raise Exception('Failed in the update query')
        trans.commit()
        
    except Exception as error:
        trans.rollback()
        message = traceback.format_exc()
        send_simple_mail('RM WEEKLY REPORT EXCEPTION', message)

    finally:
	    db_rep.close()

def data_exists():
    
    report_data = f"select count(1) rm_count from rm_weekly_report where start_date ='{current_monday}' and end_date ='{current_sunday}'"

    report_data_DF = pd.read_sql_query(report_data,  con = db_rep)
    
    report_data_count = report_data_DF.loc[0, 'rm_count']
    
    if report_data_count > 0:
        db_rep.execute(f"delete from rm_weekly_report where start_date ='{current_monday}' and end_date ='{current_sunday}'")
    insert_data()

def insert_data():

    flow_rm = "select person_id from app_users where status ='enabled' and role_codes= 'relationship_manager'"
    flow_rm_DF = pd.read_sql_query(flow_rm,  con = db_read_only)
    
    flow_rm_list = flow_rm_DF['person_id'].to_list()
    
    rm_id = []
    first_name = []
    last_name = []
    start_date = []
    end_date = []
    for item in flow_rm_list:
        person = f"select first_name, last_name from persons where id ={item}"
        person_DF = pd.read_sql_query(person,  con = db_read_only)
        
        rm_id.append(item)
        first_name.append(person_DF.loc[0, 'first_name'])
        last_name.append(person_DF.loc[0, 'last_name'])
        start_date.append(current_monday)
        end_date.append(current_sunday)
    
    person_data = {'rm_id' : rm_id, 'first_name' : first_name , 'last_name' : last_name, 'start_date' : start_date , 'end_date' : end_date}
    table_record = pd.DataFrame(person_data)
    
    table_record.to_sql(con=db_rep, name='rm_weekly_report', index=False, if_exists='append')    

def total_customers():
    total_and_enabled_cust = "select flow_rel_mgr_id as rm_id, \
            count(if(status = 'enabled', 1, null)) `tot_enabled_cust`, \
            count(1) `tot_cust` from borrowers  group by flow_rel_mgr_id order by rm_id;"

    total_and_enabled_cust = pd.read_sql_query(total_and_enabled_cust,  con = db_read_only)
    
    return total_and_enabled_cust

def calc_sow_and_eow_od():
    rm_id = []
    eow_od = []

    od_fas = f"select flow_rel_mgr_id as rm_id, \
    count(if((paid_date is null or paid_date > '{last_sunday}') and date(due_date) <= '{last_sunday}', loan_doc_id, null)) `sow_od_fas`,\
    count(if(paid_date > due_date and  date(paid_date) >= '{current_monday}' and date(paid_date) <= '{current_sunday}', 1, null)) as `fas_od_settled`,\
    count(if(((paid_date is null or paid_date > '{current_sunday}' ) and date(due_date) >= '{current_monday}' and date(due_date) <= '{current_sunday}' ) , 1, null)) as `fas_fresh_od`,\
    count(if((paid_date is null or paid_date > '{current_sunday}') and date(due_date) <= '{current_monday}', loan_doc_id, null)) `eow_od_fas`\
    from loans group by flow_rel_mgr_id order by rm_id;"
    
    
    od_fas_DF = pd.read_sql_query(od_fas,  con = db_read_only)

    return od_fas_DF

def overdue_calls_report():
    
    od_calls = f"""select call_logs.call_logger_id as rm_id, count(1) od_calls, \
    count(distinct cust_id) od_calls_cust from call_logs where json_contains(call_purpose, '["overdue_follow-ups"]') and \
    date(call_start_time) >= '{current_monday}' and date(call_start_time) <= '{current_sunday}' group by rm_id order by rm_id;"""

    od_calls_DF = pd.read_sql_query(od_calls,  con = db_read_only)
    
    return od_calls_DF

def all_calls_report():
    rm_id = []
    sow_active_cust = []

    all_logs = f"""select call_logs.call_logger_id as rm_id, count(1) all_calls, count(distinct cust_id) all_calls_cust from call_logs where date(call_start_time) >= '{current_monday}' and date(call_start_time) <= '{current_sunday}' group by rm_id order by rm_id;"""

    all_logs_DF = pd.read_sql_query(all_logs,  con = db_read_only)

    active_cust = f"""select flow_rel_mgr_id as rm_id, count(cust_id) `active_cust` from borrowers where date(last_loan_date) >= '{active_date}'  and date(last_loan_date) <= '{current_monday}' group by flow_rel_mgr_id  order by rm_id;"""

    active_cust_DF = pd.read_sql_query(active_cust,  con = db_read_only)

    disabled_od_cust = f"""select b.flow_rel_mgr_id as rm_id, count(b.cust_id) `od_disabled_cust` from borrowers b join loans l on b.cust_id = l.cust_id where  datediff(curdate(),due_date) > 30 and date(last_loan_date) <= '{current_monday}' and b.status = 'disabled' and l.status = 'overdue' group by b.flow_rel_mgr_id order by rm_id;"""

    disabled_od_cust_DF = pd.read_sql_query(disabled_od_cust,  con = db_read_only)
    
    active_and_disabled_od = pd.merge(active_cust_DF, disabled_od_cust_DF, how='outer')
    
    for index, row in active_and_disabled_od.iterrows():
        active_and_disabled = row.active_cust + row.od_disabled_cust
        rm_id.append(row.rm_id)
        sow_active_cust.append(active_and_disabled)
        
    table_data = {'rm_id' : rm_id, 'sow_active_cust': sow_active_cust}
    sow_active_cust_DF = pd.DataFrame(table_data)
    
    total_and_enabled_cust = total_customers()
    
    data_frames = [sow_active_cust_DF, all_logs_DF, total_and_enabled_cust]

    df_merged = reduce(lambda  left,right: pd.merge(left,right,on=['rm_id'], how='outer'), data_frames)
    
    return df_merged
    
    
def repayment_rate_report():

    repaid_days_sql = f"""select flow_rel_mgr_id as rm_id, count(if(DATEDIFF(paid_date, due_date) <= 0 ,1, null)) `fas_repaid_ontime`, count(if(DATEDIFF(paid_date, due_date) = 1 ,1, null)) `fas_repaid_1d_late`, count(if(DATEDIFF(paid_date, due_date) = 2 ,1, null)) `fas_repaid_2d_late`, count(if(DATEDIFF(paid_date, due_date) = 3 ,1, null)) `fas_repaid_3d_late`, count(if(DATEDIFF(paid_date, due_date) > 3 ,1, null)) `fas_repaid_3_plus_d_late`  from loans where date(paid_date) >= '{current_monday}' and date(paid_date) <= '{current_sunday}' group by flow_rel_mgr_id  order by rm_id;"""
    repaid_days = pd.read_sql_query(repaid_days_sql,  con = db_read_only)
    
    return repaid_days

def od_perc_report():
    
    perc_od_sow_arr = []
    perc_od_eow_arr = []
    rm_id = []

    od_fas_DF = calc_sow_and_eow_od()
    
    os_fas = f"select flow_rel_mgr_id as rm_id, \
    count(if((paid_date is null or date(paid_date) > '{last_sunday}') and date(disbursal_date) <= '{last_sunday}', loan_doc_id, null)) `sow_os_fas`, \
    count(if((paid_date is null or date(paid_date) > '{current_sunday}') and date(disbursal_date) <= '{current_sunday}', loan_doc_id, null)) `eow_os_fas` \
    from loans group by flow_rel_mgr_id order by rm_id"

    os_fas_DF = pd.read_sql_query(os_fas,  con = db_read_only)
    
    os_and_od_fas = pd.merge(od_fas_DF, os_fas_DF, how='outer')
    
    for index, row in os_and_od_fas.iterrows():
        if row.sow_od_fas > 0 :
            perc_od_sow = (row.sow_od_fas / row.sow_os_fas) * 100

        if row.eow_od_fas > 0 :
            perc_od_eow = (row.eow_od_fas / row.eow_os_fas) * 100

        rm_id.append(row.rm_id)
        perc_od_sow_arr.append(perc_od_sow)
        perc_od_eow_arr.append(perc_od_eow)

    table_data = {'rm_id' : rm_id, 'sow_od_perc' : perc_od_sow_arr, 'eow_od_perc' : perc_od_eow_arr}
    od_perc_DF = pd.DataFrame(table_data)
    od_perc_DF = od_perc_DF.round(1)
    
    fas_ppaid = f"""select flow_rel_mgr_id as rm_id, \
    count(distinct t.loan_doc_id) `fas_ppaid`, sum(amount) `ppaid_amt`  from loans l, loan_txns t \
    where t.loan_doc_id = l.loan_doc_id and (paid_date is null or paid_date > '{current_sunday}') and date(txn_date) >= '{current_monday}' \
    and date(txn_date) <= '{current_sunday}' and txn_type = 'payment' group by l.flow_rel_mgr_id order by rm_id;"""

    fas_ppaid_DF = pd.read_sql_query(fas_ppaid,  con = db_read_only)
    
    data_frames = [os_and_od_fas, od_perc_DF, fas_ppaid_DF]

    df_merged = reduce(lambda  left,right: pd.merge(left,right,on=['rm_id'], how='outer'), data_frames)
    
    return df_merged

def overdue_amount_report():
    sow_od_amt = f"select pri.rm_id, (os_amt - IFNULL(partial_pay,0)) sow_od_amt from \
        (select flow_rel_mgr_id as rm_id, sum(loan_principal + flow_fee) `os_amt` from loans l \
        where  (paid_date is null or paid_date > '{last_sunday}') and due_date <= '{last_sunday}' \
        group by flow_rel_mgr_id  order by rm_id) pri \
        left join \
        (select flow_rel_mgr_id as rm_id, sum(amount) partial_pay from loans l, loan_txns t \
        where l.loan_doc_id = t.loan_doc_id and (paid_date is null or paid_date > '{last_sunday}') and txn_date <= '{last_sunday}' and txn_type = 'payment' \
        group by l.flow_rel_mgr_id  order by rm_id) pp \
        on pri.rm_id = pp.rm_id;"

    sow_od_amt_DF = pd.read_sql_query(sow_od_amt,  con = db_read_only)

    eow_od_amt =f"select pri.rm_id, (os_amt - IFNULL(partial_pay,0)) eow_od_amt from \
        (select flow_rel_mgr_id as rm_id, sum(loan_principal + flow_fee) `os_amt` from loans l \
        where (paid_date is null or paid_date > '{current_sunday}') and due_date <= '{current_sunday}' \
        group by flow_rel_mgr_id  order by rm_id) pri \
        left join \
        (select flow_rel_mgr_id as rm_id, sum(amount) partial_pay from loans l, loan_txns t \
        where l.loan_doc_id = t.loan_doc_id and (paid_date is null or paid_date > '{current_sunday}') and \
        txn_date <= '{current_sunday}' and txn_type = 'payment' group by l.flow_rel_mgr_id  order by rm_id) pp \
        on pri.rm_id = pp.rm_id;"

    eow_od_amt_DF = pd.read_sql_query(eow_od_amt,  con = db_read_only)

    new_overdue_amount = f"select pri.rm_id, (os_amt - IFNULL(partial_pay,0)) `fresh_od_amt` from \
        (select flow_rel_mgr_id as rm_id, sum(loan_principal + flow_fee) `os_amt` from loans l \
        where  (paid_date is null or paid_date > '{current_sunday}') and date(due_date) >= '{current_monday}' \
        and date(due_date) < '{current_sunday}' group by flow_rel_mgr_id order by rm_id) pri \
        left join \
        (select flow_rel_mgr_id as rm_id, sum(amount) partial_pay from loans l, loan_txns t \
        where l.loan_doc_id = t.loan_doc_id and (paid_date is null or paid_date > '{current_sunday}') and txn_date >= '{current_monday}' and \
        txn_date <= '{current_sunday}' and txn_type = 'payment' group by l.flow_rel_mgr_id  order by rm_id) pp \
        on pri.rm_id = pp.rm_id;"

    new_overdue_amount_DF = pd.read_sql_query(new_overdue_amount,  con = db_read_only)

    od_settled_amount = f"select flow_rel_mgr_id as rm_id, sum(amount) `settled_od_amt` from loans l, loan_txns t \
        where l.loan_doc_id = t.loan_doc_id and paid_date > due_date and txn_date >= '{current_monday}' and \
        txn_date <= '{current_sunday}' and txn_type = 'payment' group by l.flow_rel_mgr_id  order by rm_id;"

    od_settled_amount_DF = pd.read_sql_query(od_settled_amount,  con = db_read_only)

    data_frames = [sow_od_amt_DF, eow_od_amt_DF, new_overdue_amount_DF, od_settled_amount_DF]

    df_merged = reduce(lambda  left,right: pd.merge(left,right,on=['rm_id'], how='outer'), data_frames)
    return df_merged
    
def merge_all_report():
    
    od_calls = overdue_calls_report()
    all_calls = all_calls_report()
    
    repaid_days = repayment_rate_report()
    od_perc = od_perc_report()
    repaid_amount = overdue_amount_report()
    
    data_frames = [od_calls, all_calls, repaid_days, od_perc, repaid_amount]

    rm_records = reduce(lambda  left,right: pd.merge(left,right,on=['rm_id'], how='outer'), data_frames)
    
    rm_records = rm_records.fillna(0)
    
    for index, row in rm_records.iterrows():
        update_query = f"update rm_weekly_report set od_calls = {row.od_calls}, od_calls_cust = {row.od_calls_cust}, sow_active_cust = {row.sow_active_cust}, all_calls = {row.all_calls}, all_calls_cust = {row.all_calls_cust}, tot_enabled_cust = {row.tot_enabled_cust}, tot_cust = {row.tot_cust}, fas_repaid_ontime = {row.fas_repaid_ontime}, fas_repaid_1d_late = {row.fas_repaid_1d_late}, fas_repaid_2d_late = {row.fas_repaid_2d_late}, fas_repaid_3d_late = {row.fas_repaid_3d_late}, fas_repaid_3_plus_d_late = {row.fas_repaid_3_plus_d_late}, sow_od_fas = {row.sow_od_fas}, sow_od_perc = {row.sow_od_perc}, fas_od_settled = {row.fas_od_settled}, fas_fresh_od = {row.fas_fresh_od}, fas_ppaid = {row.fas_ppaid}, eow_od_fas = {row.eow_od_fas}, eow_od_perc = {row.eow_od_perc}, sow_od_amt = {row.sow_od_amt}, ppaid_amt = {row.ppaid_amt}, fresh_od_amt = {row.fresh_od_amt}, settled_od_amt = {row.settled_od_amt}, eow_od_amt = {row.eow_od_amt} where start_date = '{current_monday}' and end_date = '{current_sunday}' and rm_id = {row.rm_id}"

        db_rep.execute(update_query)

run()