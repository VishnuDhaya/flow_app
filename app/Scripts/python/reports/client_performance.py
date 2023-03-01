env = {}
import pandas as pd
import mysql.connector
from flow_common import db_str,db_rep_str,load_env
from sqlalchemy import create_engine

load_env()


def fetch():

	db = create_engine('mysql+mysqlconnector://'+db_str('STMT'))

	db_rep = create_engine('mysql+mysqlconnector://'+db_rep_str('STMT'))

	db_rep.execute("DELETE FROM client_performance")

	sqlcountrCode="Select country_code from markets"
	sqlcountrCode_read = pd.read_sql_query(sqlcountrCode,  con = db);
	countryCodeDF=pd.DataFrame(sqlcountrCode_read)

	for index, cntryCode in countryCodeDF.iterrows():
		
		sqlAccPrvdrCode="""Select acc_prvdr_code from acc_providers where country_code = '{}' and biz_account = 1""".\
		format(cntryCode['country_code'])
		sqlAccPrvdrCode_read = pd.read_sql_query(sqlAccPrvdrCode,  con = db);
		accProviderDF=pd.DataFrame(sqlAccPrvdrCode_read)
		

		for index, accPrvdr in accProviderDF.iterrows():

			borrowerSql = """select acc_number,cust_id,ongoing_loan_doc_id,biz_name, owner_person_id,dp_rel_mgr_id  from borrowers
				   		where acc_prvdr_code ='{}' """.\
				   		format(accPrvdr['acc_prvdr_code'])

			borrowerSql_read = pd.read_sql_query(borrowerSql,  con = db);
			borrowerSql_readDF=pd.DataFrame(borrowerSql_read)

			

			for index, borrower in borrowerSql_readDF.iterrows():

				if borrower['dp_rel_mgr_id'] is not  None:

					borrowergenderSql = """select gender from persons
					   		where id='{}' """.\
					   		format(borrower['owner_person_id'])

					borrowerGenderSql_read = pd.read_sql_query(borrowergenderSql,  con = db);
					borrowerenderSql_readDF=pd.DataFrame(borrowerGenderSql_read)

					personSql = """select first_name , last_name from persons
					   		where id='{}'""".\
					   		format(borrower['dp_rel_mgr_id'])

					personSql_read = pd.read_sql_query(personSql,  con = db);
					personSql_readDF=pd.DataFrame(personSql_read)

					borrowerFASql = """select sum(duration) as duration,count(duration) as count_duration,count(loan_doc_id) as totalFA,sum(loan_principal) as totalAmt,sum(flow_fee) as totalFee,biz_name,dp_rel_mgr_id from loans
								where cust_id='{}'""".\
						format(borrower['cust_id'])

					borrowerFASql_read = pd.read_sql_query(borrowerFASql,  con = db);
					borrowerFASql_readDF=pd.DataFrame(borrowerFASql_read)

					check_nan = borrowerFASql_readDF['totalAmt'].isnull().values.any()



					borrowerlateFASql = """select count(loan_doc_id) as totalLatefa from loans
										where cust_id='{}' and paid_date> due_date""".\
										format(borrower['cust_id'])

					borrowerlateFASql_read = pd.read_sql_query(borrowerlateFASql,  con = db);
					borrowerlateFASql_readDF=pd.DataFrame(borrowerlateFASql_read)


					if personSql_readDF['last_name'][0] is   None:
							personSql_readDF['last_name'][0]=''

					if borrower['ongoing_loan_doc_id'] is not   None:
						borrower['ongoing_loan_doc_id']='1'
					else:
						borrower['ongoing_loan_doc_id']='0'

					borrowerenderSql_readDF.at[0,'country_code'] = cntryCode['country_code']
					borrowerenderSql_readDF.at[0,'acc_prvdr_code'] = accPrvdr['acc_prvdr_code']
					borrowerenderSql_readDF.at[0,'acc_number'] = borrower['acc_number']
					borrowerenderSql_readDF.at[0,'ongoing_loan'] = borrower['ongoing_loan_doc_id']
					borrowerenderSql_readDF.at[0,'biz_name'] = borrower['biz_name'] 
					# borrowerenderSql_readDF.at[0,'rel_mgr_name'] = borrowerFASql_readDF['dp_rel_mgr_id'][0]
					borrowerenderSql_readDF.at[0,'rel_mgr_name'] = personSql_readDF['first_name'][0] + ' ' +personSql_readDF['last_name'][0] 

					if(check_nan):
						borrowerenderSql_readDF.at[0,'total_FA'] = 0
						borrowerenderSql_readDF.at[0,'total_Amt'] = 0
						borrowerenderSql_readDF.at[0,'total_Fee'] = 0
						borrowerenderSql_readDF.at[0,'total_Fee_usd'] = 0
						borrowerenderSql_readDF.at[0,'per_adv_revenue'] = 0
						borrowerenderSql_readDF.at[0,'avg_fa_size'] = 0
						borrowerenderSql_readDF.at[0,'avg_fa_duration'] = 0
						borrowerenderSql_readDF.at[0,'total_Late_FA'] = 0
						borrowerenderSql_readDF.at[0,'assume_income'] =0
						borrowerenderSql_readDF.at[0,'total_Late_FA_perc']=0
						borrowerenderSql_readDF.at[0,'fee_per_income']=0
			
					else:
						borrowerenderSql_readDF.at[0,'total_FA'] = borrowerFASql_readDF['totalFA'][0] 
						borrowerenderSql_readDF.at[0,'total_Amt'] = borrowerFASql_readDF['totalAmt'][0]
						borrowerenderSql_readDF.at[0,'total_Fee'] = borrowerFASql_readDF['totalFee'][0]

						if  borrowerFASql_readDF['totalFee'][0] is  None:
							borrowerenderSql_readDF.at[0,'total_Fee_usd'] = 0
						else :
							total_Fee_usd=borrowerFASql_readDF['totalFee'][0]/3722
							borrowerenderSql_readDF.at[0,'total_Fee_usd'] = total_Fee_usd

						borrowerenderSql_readDF.at[0,'per_adv_revenue'] = total_Fee_usd/borrowerFASql_readDF['totalFA'][0]

						if  borrowerFASql_readDF['totalAmt'][0] is  None:
							avg_fa_size=0
						elif borrowerFASql_readDF['totalFA'][0] is None:
							avg_fa_size=0
						else:
							avg_fa_size=borrowerFASql_readDF['totalAmt'][0]/borrowerFASql_readDF['totalFA'][0]

						borrowerenderSql_readDF.at[0,'avg_fa_size'] = avg_fa_size
						avg_fa_duration=borrowerFASql_readDF['duration'][0]/borrowerFASql_readDF['count_duration'][0]
						borrowerenderSql_readDF.at[0,'avg_fa_duration'] = avg_fa_duration
						borrowerenderSql_readDF.at[0,'total_Late_FA'] = borrowerlateFASql_readDF['totalLatefa'][0]
						assume_income=(avg_fa_size *avg_fa_duration * 0.015 * borrowerFASql_readDF['totalFA'][0])/3722
						borrowerenderSql_readDF.at[0,'assume_income'] =assume_income
						

						if borrowerlateFASql_readDF['totalLatefa'][0] !=0:

							borrowerenderSql_readDF.at[0,'total_Late_FA_perc'] = borrowerlateFASql_readDF['totalLatefa'][0]/borrowerFASql_readDF['totalFA'][0]

						else:
							borrowerenderSql_readDF.at[0,'total_Late_FA_perc']=0


						if total_Fee_usd is None:

							borrowerenderSql_readDF.at[0,'fee_per_income']=0
							

						else:
							borrowerenderSql_readDF.at[0,'fee_per_income'] = total_Fee_usd/assume_income
						
						
				print(borrowerenderSql_readDF)
				borrowerenderSql_readDF.to_sql('client_performance', con = db_rep, if_exists='append', chunksize = 500, index = False)	

				
	return True;

fetch()
