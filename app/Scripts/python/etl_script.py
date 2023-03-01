from __future__ import print_function
import mysql.connector
from mysql.connector.errors import Error 
from mysql.connector.errors import IntegrityError
from xlrd import XLRDError
from mysql.connector import IntegrityError 
from mysql.connector import errorcode
import numpy as np
import pandas as pd
import xlrd
import datetime
from sqlalchemy import create_engine
import sqlalchemy
import os
import sys
import logging
from flow_common import db_str, load_env, env, get_unique_files 
import time


#from html_to_xls import  to_xls
#from bs4 import BeautifulSoup

processed_files = []
unprocessed_files = {}
empty_files = {}
dup_files = {}

now = datetime.datetime.now()
logging.basicConfig(level=logging.DEBUG)
logger = logging.getLogger(__name__)


def fetch(sql, database):
	
	db = mysql.connector.connect(
													host="192.168.73.166",
													user="FlowUser",
													password="flowapi",
													database= database
													)
	cur = db.cursor()
	cur.fetch(sql)

	sql_data = pd.DataFrame(cur.fetchall())
	sql_data.columns = cur.column_names
	db.close()
	return sql_data

def fetch_txn(sql):
	return fetch(sql, "flow_api_test")

def fetch_calc(sql):
	return fetch(sql, "flow_credit_calc")

def get_float(record):
    if (record['descr'] == 'NT Comm Batch Payout') or (record['descr'] == 'Transfer Comm') or ('Airtel' in record['descr']):
        if 'Withdrawal' in record['descr'] or 'Comm' in record['descr']:
            return record['cr_amt']
        else:
            return 0.0
    else:
        return record['cr_amt']

def get_comms(record):
	
    if record['descr'] == "NT Comm Batch Payout":
    	
    	if record['cr_amt']:
        	return record['cr_amt']
    	elif record['dr_amt']:
    		return record['dr_amt']
    else:
        return 0.0

# Function to flag non-float investment transactions
def chk_float(amt):
    if amt == 0:
        return False
    else:
        return True

def rename(txn_df):
	txn_df.rename(columns={'Dr. Amount':'dr_amt',
										'Cr. Amount':'cr_amt',
										'Transaction ID': 'txn_id',
										'Transaction Date' : 'txn_date',
										'Description' : 'descr',
										'Balance'	 : 'balance',
										'ID' : 'data_prvdr_cust_id'
										},inplace=True)
	
	txn_df = txn_df.astype({'txn_id': str, 'data_prvdr_cust_id' : str})
	
	txn_df.drop(['Product ID'],axis=1,inplace=True)

def isnum(value):
	if value == '':
		return False
	try:
		import math
		return not math.isnan(float(value))
	except:
		return True


def to_num(amt):
	if(type(amt) is str or type(amt) is unicode):
		amt = amt.strip()
	
	if isnum(amt):
		return float(amt)
	else:
		return 0.0	

def parse_date(date):
	from datetime import datetime
	#="01/03/2018 01:03:43 AM"
	
	return datetime.strptime(date, '%d/%m/%Y %I:%M:%S %p')

def convert(txn_df):
	txn_df['dr_amt'] = txn_df['dr_amt'].apply(to_num)
	txn_df['cr_amt'] = txn_df['cr_amt'].apply(to_num)
	txn_df['balance'] = txn_df['balance'].apply(to_num)

'''def db_str(ds):
	username = env[ds+'_DB_USERNAME']
	password = env[ds+'_DB_PASSWORD']
	host = env[ds+'_DB_HOST']
	db_name = env[ds+'_DB_DATABASE']
	
	return "{0}:{1}@{2}/{3}".format(username, password, host, db_name)

'''

def validate(txn_df):
	file_dp_cust_id = txn_df['data_prvdr_cust_id'][0]
	if(dp_cust_id != file_dp_cust_id):
		print("$$$$$")
		print("Data Provider Cust ID in file does not match with the supplied Data Provider Cust ID")
		quit()

def extract_file(file_name):
	#with open(file_name, 'r') as file:
		#data = file.read().replace('&nbsp;', '')

	try:
		txn_df = pd.read_excel(file_name, flavor = None)
	except XLRDError as error:		
		message = getattr(error, "message", repr(error))
		if (message.startswith("Unsupported format, or corrupt file:")):
			txn_df = pd.read_html(file_name, flavor = None)[0]			
		else:	
			raise error	
	except Exception as error:
		raise error
	
	#txn_df = pd.DataFrame(txn_df)
	
	rename(txn_df)
	return txn_df


def clean_column(data):
	
	if(data.startswith("=\"")):
		data = data.replace("=", "")
		data = data.replace("\"", "")
	
	return data	
	
def clean(txn_df):
	obj_cols = txn_df.dtypes[txn_df.dtypes == 'object'].index.tolist()
	
	for obj_col in obj_cols:
		txn_df[obj_col] = txn_df[obj_col].apply(clean_column)
	return txn_df

def transform(txn_df, file):
	
	
	
	convert(txn_df)
	
	txn_df['comms'] = txn_df.apply(get_comms, axis=1)
	
	# Create a Float Investment columns, excluding 'NT Comm Batch Payout', 'Transfer Comm' and 'Airtel' transactions
	txn_df['float_amt'] = txn_df.apply(get_float, axis=1)

	txn_df['is_float'] = txn_df['float_amt'].apply(chk_float)
	
	txn_df['txn_date'] = txn_df['txn_date'].apply(parse_date)

	txn_df['dr_amt'] = txn_df['dr_amt'].apply(lambda x : np.absolute(x))
	txn_df['country_code'] = country_code
	txn_df['dp_code'] = dp_code
	txn_df['run_id'] = run_id

	

	#print(file)
	#txn_df['file_name'] = file.replace(path,'')
	# TODO Check if Borower exist in TXN DB
	return txn_df
	

def load_df(txn_df , is_row_df = False, row_index = 0):
	try:
		txn_df.drop(['Product ID'],axis=1,inplace=True)
		txn_df.to_sql('cust_acc_stmts', con = stmt_ngin, if_exists='append', index = False,
									dtype={'txn_id': sqlalchemy.types.NVARCHAR(length=40), 
									'data_prvdr_cust_id' : sqlalchemy.types.NVARCHAR(length=40),
									'descr' : sqlalchemy.types.NVARCHAR(length=100)})
									
	except Exception as e:
		if (e.__class__.__name__ == 'IntegrityError' ):
			if( is_row_df):
				log_dup_record(file, row_index)
			elif(mode == 'batch'):
				dup_files[file] = "FILE HAS DUPLICATE"
			else:	
				load_df_each_row(txn_df)
		else:
			raise e
		print("$$$$$")	
		print(e.__class__.__name__ + " : Unable to process ETL")	
		quit()

def log_dup_record(file, index):

	#dup_files[file] = dup_files.get(file, []).append(index)
	lst = dup_files.get(file, [])
	lst.append(index) 
	dup_files[file] = lst
	
			
def load_df_each_row(txn_df):
	for index, row  in txn_df.iterrows():
		try:
			row_df = pd.DataFrame(row).transpose()
			load_df(row_df, True ,index)
		except Exception as e:
			raise e

############## START ##################
load_env()
mode = 'batch'

#run_id = now.strftime('%y%m%d%H%M%S%f')
run_id = int(round(time.time() * 1000))
#run_id = now.strftime('%y%m%d%H%M')
path = sys.argv[1]
country_code = sys.argv[2]
dp_cust_id = sys.argv[3]
dp_code =  sys.argv[4]
purge =  sys.argv[5]
#path ='/home/sateesh/Documents/PROJECTS/python/2019-06-29/2019 04 12/AccountTransaction-153.xlsx'
allFiles = []
	

if os.path.isdir(path):
	allFiles = get_unique_files(path)
elif os.path.isfile(path):
	allFiles = [path]

if not allFiles:
	print("$$$$$")
	print("No files exist. Please choose correct path")
	quit()
#print(db_str('STMT'))
stmt_ngin = create_engine('mysql+mysqlconnector://' + db_str('STMT'))
#print(stmt_ngin)
i = 0
tot_files = len(allFiles);
for file in allFiles:
	i = i + 1
	#print('[' + str(i) + '/' + str(tot_files) +  ']  '+ file)
	try:
		
		txn_df = extract_file(file)

		if file.endswith('.xls'):
			txn_df = clean(txn_df)

		validate(txn_df)
		txn_df = transform(txn_df, file)
		load_df(txn_df)
		processed_files.append(file)
	except Exception as error: 
		#logging.exception("")
		message = getattr(error, "message", repr(error))

		if (message.startswith("No tables found") or message.startswith('File size is 0 bytes')):
			empty_files[file] = message
			print("$$$$$")
			print("EMPTY : "+  message)
			quit()
		else:
			logging.exception("")
			message =  error.__class__.__name__ + " : " + message
			print("$$$$$")
			print("UNPROCESSED : "+  message)
			quit()
			unprocessed_files[file] = message
'''
print("\n----------DUPLICATE CONTENT FILES-----------------")
print(len(dup_files))
print(dup_files)
print("\n----------OTHER  UNPROCESSED FILES-----------------")
print(len(unprocessed_files))
print(unprocessed_files)

print("\n----------EMPTY FILES-----------------")
print(len(empty_files))
print(empty_files)


print("\n----------PROCESSED FILES-----------------")
print(len(processed_files))
print(processed_files)

print(datetime.datetime.now())
'''
#txn_df = pd.concat(txn_df, axis = 0, ignore_index = True)
print("#####")
print(run_id)