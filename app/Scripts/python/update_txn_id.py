import pandas as pd
from sqlalchemy import create_engine
import os

from flow_common import db_str, load_env, env, get_unique_files 


load_env()

def fetch():

	db = create_engine('mysql+mysqlconnector://' + db_str('STMT'))
	print(db)
	

	file_name = r'../../../storage/data/FA Txn IDs.xlsx' 
	dirname = os.path.dirname(__file__)
	file_name = os.path.join(dirname, file_name)

	df = pd.read_excel(file_name,skiprows=[0,2],usecols=['FA ID','Disb. Trx. ID','Rpt trx ID'],na_filter=False)
	df = pd.DataFrame(df)
	
	for index,row in df.iterrows():
		try:
			FA_ID = row['FA ID']

			disbursal_txn_id_xl = str(row['Disb. Trx. ID'])

			payment_txn_id_xl = str(row['Rpt trx ID'])
			
			txn_id_db = "select id, txn_id,txn_type from loan_txns where loan_doc_id ='{}' ".format(row['FA ID'])
			
			txn_id_db = pd.read_sql_query(txn_id_db , con = db)

			txns_db = pd.DataFrame(txn_id_db)

			
			for index,txn_db in txns_db.iterrows():
				if (txn_db['txn_id'] is None or txn_db['txn_id'] != disbursal_txn_id_xl) and txn_db['txn_type'] == 'disbursal': 
					if (disbursal_txn_id_xl != ""):
						update_query = "update loan_txns set txn_id ='{}' where id = '{}'".format(disbursal_txn_id_xl,txn_db['id'])
						print(update_query)
						with db.begin() as conn:
							conn.execute(update_query)
							print("Disbursal_txn_id updated")
				elif(txn_db['txn_id'] is None or txn_db['txn_id'] != payment_txn_id_xl) and txn_db['txn_type'] == 'payment': 
					if(payment_txn_id_xl != ""):
		   				update_query = "update loan_txns set txn_id ='{}' where id = '{}'".format(payment_txn_id_xl,txn_db['id'])
		   				print(update_query)
		   				with db.begin() as conn:
		   					conn.execute(update_query)
		   					print("Payment txn_id updated")

			# if (txn_db['txn_id'] is None or txn_db['txn_id'] == disbursal_txn_id_xl) and txn_db['txn_type'] == 'disbursal':
			#    		print("here")
			# 	elif (txn_db['txn_id'] is None or txn_db['txn_id'] == payment_txn_id_xl) and txn_db['txn_type'] == 'payment':
			#  		print("update")
				
			# if pd.isnull(disbursal_txn_id_xl):
			# 		print("here")
			# payment_txn_id_db = "select txn_id from loan_txns where loan_doc_id ='{}' and txn_type = 'payment'".format(row['FA ID'])			
			
			# disbursal_txn_id_db = pd.read_sql_query(disbursal_txn_id_db , con = db)
			# disbursal_txn_id_db = pd.DataFrame(disbursal_txn_id_db)

			# payment_txn_id_db = pd.read_sql_query(payment_txn_id_db , con = db)
			# payment_txn_id_db = pd.DataFrame(payment_txn_id_db)

			# for index,payment_txn_id_db in payment_txn_id_db.iterrows():
			# 	if payment_txn_id_db['txn_id'] is None:
			# 		payment_txn_id_db.to_sql('loan_txns', con = db_rep, if_exists='append', chunksize = 500, index = False)
			# 	else:
			# 		print("update")

			# for index,disbursal_txn_id_db in disbursal_txn_id_db.iterrows():
			# 	if disbursal_txn_id_db['txn_id'] is None:
			# 		print("here")
			# 	else:
			# 		print("update")
			# 	print the first 5 rows

		except Exception as e:
		 	raise e
	return "Updated successfully"

fetch()