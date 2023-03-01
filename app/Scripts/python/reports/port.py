env = {}
import pandas as pd
import mysql.connector
from flow_common import db_str,db_rep_str,load_env,WRITE_OFF_STATUS
from sqlalchemy import create_engine
from reports_common import get_float_vend_products, get_ignore_written_off_condn
from datetime import datetime as dt

load_env()
PAR_DAYS = [5,10,15,30,60,90,120,180,270]

def fetch():

	db = create_engine('mysql+mysqlconnector://'+db_str('STMT'))
	db_rep = create_engine('mysql+mysqlconnector://'+db_rep_str('STMT'))

	db_rep.execute("DELETE FROM portfolio_risk")
	

	sqlcountrCode="Select country_code from markets"
	sqlcountrCode_read = pd.read_sql_query(sqlcountrCode,  con = db);
	countryCodeDF=pd.DataFrame(sqlcountrCode_read)
	fv_products = get_float_vend_products(db)    


	for index, cntryCode in countryCodeDF.iterrows():
		
		sqlAccPrvdrCode="Select acc_prvdr_code from acc_providers where country_code='{}' and biz_account = 1".format(cntryCode['country_code'])
		ssqlAccPrvdrCode_read = pd.read_sql_query(sqlAccPrvdrCode,  con = db);
		accPrvdrDF=pd.DataFrame(ssqlAccPrvdrCode_read)
		current_date = dt.now().strftime("%Y-%m-%d")
		ignore_write_offs_condn = get_ignore_written_off_condn(cntryCode['country_code'], current_date, db, alias='')
		

		for index, accPrvdr in accPrvdrDF.iterrows():
			
			
			
			for par_day in PAR_DAYS:

				loanprincipalsql ="""select sum(if(flow_fee >= current_os_amount, 0, current_os_amount - flow_fee)) as total from loans 
									where status in ('ongoing','due','overdue') and product_id not in {}
 									and country_code = '{}' and  acc_prvdr_code = '{}' {} """.\
				format(fv_products ,cntryCode['country_code'], accPrvdr['acc_prvdr_code'], ignore_write_offs_condn)


				loanprincipalsql_read = pd.read_sql_query(loanprincipalsql,  con = db);
				loanprincipalDF=pd.DataFrame(loanprincipalsql_read)
				totLoanPricpal=loanprincipalDF['total'][0]
				

				sql = """select  country_code, acc_prvdr_code, sum(if(flow_fee >= current_os_amount, 0, current_os_amount - flow_fee)) as par_loan_principal from loans 
							where status in ('ongoing','due','overdue') and product_id not in {} and DATEDIFF(CURDATE(), due_date) > '{}' and 
							country_code = '{}' and  acc_prvdr_code = '{}' {}""".\
							format(fv_products, par_day, cntryCode['country_code'], accPrvdr['acc_prvdr_code'], ignore_write_offs_condn)


				loan_products = pd.read_sql_query(sql,  con = db);
				loans_df=pd.DataFrame(loan_products)
				loans_df['par_days'] = par_day
				
				if loans_df['par_loan_principal'][0] is not None:
					loans_df['percentage'] = (loans_df['par_loan_principal'][0]/totLoanPricpal);
				else :
					loans_df.at[0,'par_loan_principal']=0
					loans_df.at[0,'country_code']=cntryCode['country_code']
					loans_df.at[0,'acc_prvdr_code']=accPrvdr['acc_prvdr_code']
					loans_df['percentage'] =0;

					
				print(loans_df)
				loans_df.to_sql('portfolio_risk', con = db_rep, if_exists='append', chunksize = 500, index = False)

		
	return True;

fetch()