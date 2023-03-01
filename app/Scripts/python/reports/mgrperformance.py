env = {}
import pandas as pd
import mysql.connector
from flow_common import db_str,db_rep_str,load_env
from sqlalchemy import create_engine
import calendar
import  datetime

load_env()
month_year=[]
year_month=[]
db = create_engine('mysql+mysqlconnector://'+db_str('STMT'))
db_rep = create_engine('mysql+mysqlconnector://'+db_rep_str('STMT'))

db_rep.execute("DELETE FROM master_performances")
month = '02'
year = '2021'



def get_month_range():
    month_range = []
    start_year = 2019
    this_year = datetime.date.today().year
    this_month = datetime.date.today().month
    # print(this_year)
    for year in range(start_year, this_year + 1):
    	last_month = this_month if(year == this_year) else 12
    	for month in range(1, last_month + 1):
    		day = datetime.date(year, month, calendar.monthrange(year, month)[-1])
    		month_range.append(day)
    return month_range

month_range = get_month_range()


def get_all_data_providers():
		
    sqldataProviderCode = f"Select data_prvdr_code, country_code from data_prvdrs"
    dataProviderCodeRS = pd.read_sql_query(sqldataProviderCode,  con = db);
    return pd.DataFrame(dataProviderCodeRS)

def get_all_rms(which_rms, ass_entity):
	personSql = f"select id, first_name, last_name, mobile_num from persons \
			   		where associated_with = '{which_rms}' and associated_entity_code = '{ass_entity}' and status = 'enabled'"

	personSql_read = pd.read_sql_query(personSql,  con = db);
	return pd.DataFrame(personSql_read)

def get_reg_cus_count(rel_mgr_id,month,year):
	reg_cust_Sql = f"select count(distinct id) as reg_cust from borrowers \
				where dp_rel_mgr_id = '{rel_mgr_id}' and month(reg_date) = '{month}' and year(reg_date) ='{year}'  "
	   			
	reg_cust_Sql_RS = pd.read_sql_query(reg_cust_Sql,  con = db);
	return pd.DataFrame(reg_cust_Sql_RS)

def get_tot_loan_disbursed(personid,month,year):
	loanDisbSql = f"select  count(distinct id) as tot_disbursed , sum(loan_principal) as tot_amount_disbursed,dp_rel_mgr_id from loans \
				where dp_rel_mgr_id = '{personid}' and status not in ('pending_disbursal','void', 'hold') and month(disbursal_date) = '{month}' and year(disbursal_date) ='{year}'"
	loanDisbSql_RS = pd.read_sql_query(loanDisbSql,  con = db);
	return pd.DataFrame(loanDisbSql_RS)

def get_tot_paid_late_loans(personid,month,year):
	loanPaidLateSql = f"select  count(distinct id) as tot_paidLate from loans \
			where dp_rel_mgr_id = '{personid}' and status = 'settled' and paid_date > due_date and month(paid_date) = '{month}' and year(paid_date) ='{year}' "
	loanPaidLateSql_RS = pd.read_sql_query(loanPaidLateSql,  con = db);
	return pd.DataFrame(loanPaidLateSql_RS)

def get_tot_loans_paid_ontime(personid,month,year):
	loanPaidTimeSql = f"select  count(distinct id) as tot_loan_paid_ontime from loans\
				where dp_rel_mgr_id = '{personid}' and status = 'settled' and paid_date <= due_date and month(paid_date) = '{month}' and year(paid_date) ='{year}'"
	loanPaidTimeSql_RS = pd.read_sql_query(loanPaidTimeSql,  con = db);
	return pd.DataFrame(loanPaidTimeSql_RS)

def get_os_fa_count(personid, lastdayofmonth):

	sql = f"select count(id) os_fa_count from loans where  disbursal_date <= '{lastdayofmonth}' and (paid_date >'{lastdayofmonth}' or paid_date is null) and \
				dp_rel_mgr_id={personid}"


	resultset = pd.read_sql_query(sql,  con = db);
	return pd.DataFrame(resultset)


def get_overdue_fa_count(personid, lastdayofmonth):

	sql = f"select count(id) od_fa_count from loans where  disbursal_date <= '{lastdayofmonth}' and (paid_date >'{lastdayofmonth}' or paid_date is null) and due_date < '{lastdayofmonth}' and dp_rel_mgr_id={personid}"						
	resultset = pd.read_sql_query(sql,  con = db);
	return pd.DataFrame(resultset)


def fetch():

	dataProviderDF = get_all_data_providers()
	for date in month_range:

		for index, (data_prvdr_code, country_code) in dataProviderDF.iterrows():
			partner_rms = get_all_rms('data_prvdr', data_prvdr_code)
			
			for index, person in partner_rms.iterrows():
				reg_customer = get_reg_cus_count(person['id'], date.month, date.year)
				tot_loan_disbursed = get_tot_loan_disbursed(person['id'], date.month, date.year)
				tot_paid_late_loans = get_tot_paid_late_loans(person['id'], date.month, date.year)
				tot_loans_paid_ontime = get_tot_loans_paid_ontime(person['id'], date.month, date.year)
				os_fa_count = get_os_fa_count(person['id'], date)
				od_fa_count = get_overdue_fa_count(person['id'], date)

				if person['last_name'] is  None:
					person['last_name'] = ''
				

				tot_loan_disbursed.at[0,'name'] = person['first_name'] +' '+ person['last_name']
				tot_loan_disbursed.at[0,'country_code'] = country_code
				tot_loan_disbursed.at[0,'data_prvdr_code'] = data_prvdr_code
				tot_loan_disbursed.at[0,'dp_rel_mgr_mobile_num'] = person['mobile_num']
				tot_loan_disbursed.at[0,'tot_mergent'] = reg_customer['reg_cust'][0]
				tot_loan_disbursed.at[0,'dp_rel_mgr_id'] = person['id']
				tot_loan_disbursed.at[0,'tot_loan_outstand'] = os_fa_count['os_fa_count'][0]
				tot_loan_disbursed.at[0,'tot_loan_overdue'] = od_fa_count['od_fa_count'][0]
				tot_loan_disbursed.at[0,'tot_loan_paidLate'] = tot_paid_late_loans['tot_paidLate'][0]
				tot_loan_disbursed.at[0,'lastdate'] = date
				tot_loan_disbursed.at[0,'tot_paid_ontime'] = tot_loans_paid_ontime['tot_loan_paid_ontime'][0]
				totpaid=tot_loans_paid_ontime['tot_loan_paid_ontime'][0]+tot_paid_late_loans['tot_paidLate'][0]
				if tot_loan_disbursed['tot_disbursed'][0]!=0:
					tot_loan_disbursed.at[0,'tot_loan_paid_ontime'] = tot_loans_paid_ontime['tot_loan_paid_ontime'][0] / totpaid
				else:
					tot_loan_disbursed.at[0,'tot_loan_paid_ontime']=0
				print(tot_loan_disbursed)
				tot_loan_disbursed.to_sql('master_performances', con = db_rep, if_exists='append', chunksize = 500, index = False)
							


	
		
				
				
	return True;

fetch()

