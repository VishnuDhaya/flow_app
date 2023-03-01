env = {}
import pandas as pd
import mysql.connector
from flow_common import db_str,db_rep_str,load_env
from sqlalchemy import create_engine

load_env()


def fetch():

	db = create_engine('mysql+mysqlconnector://'+db_str('STMT'))
	db_rep = create_engine('mysql+mysqlconnector://'+db_rep_str('STMT'))

	db_rep.execute("DELETE FROM revpercustomer")

	sqlcountrCode="Select country_code from markets"
	sqlcountrCode_read = pd.read_sql_query(sqlcountrCode,  con = db);
	countryCodeDF=pd.DataFrame(sqlcountrCode_read)

	for index, cntryCode in countryCodeDF.iterrows():
		
		sqldataProviderCode="""Select acc_prvdr_code from acc_providers where country_code = '{}' and biz_account = 1""".\
		format(cntryCode['country_code'])
		sqldataProviderCode_read = pd.read_sql_query(sqldataProviderCode,  con = db);
		dataProviderDF=pd.DataFrame(sqldataProviderCode_read)
		

		for index, datapvdr in dataProviderDF.iterrows():


			customerfeeSql = """select count(distinct cust_id) tot_customer, sum(flow_fee) as flow_fee,date_format(paid_date,'%Y %M') as rev_date, acc_prvdr_code from loans 
								where status NOT IN ('voided','hold', 'pending_disbursal', 'pending_mnl_dsbrsl') and acc_prvdr_code = '{}' and paid_date is not null  group by DATE_FORMAT(paid_date, '%M %Y') order by DATE_FORMAT(paid_date, '%Y %m');""".\
				   		format( datapvdr['acc_prvdr_code']) 

			customerfeeSql_read = pd.read_sql_query(customerfeeSql,  con = db);
			customerfeeSql_readDF=pd.DataFrame(customerfeeSql_read)

			customerfeeSql_readDF.fillna(0, inplace=True)
			customerfeeSql_readDF['cum_flow_fee']=customerfeeSql_readDF['flow_fee'].cumsum()
			customerfeeSql_readDF['cum_customer']=customerfeeSql_readDF['tot_customer'].cumsum()

			customerfeeSql_readDF['country_code']=cntryCode['country_code']


			print(customerfeeSql_readDF)
			customerfeeSql_readDF.to_sql('revpercustomer', con = db_rep, if_exists='append', chunksize = 500, index = False)	
				
	return True;

fetch()