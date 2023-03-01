from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from time import sleep

import sqlalchemy
from sqlalchemy import create_engine
import sys
import pandas as pd
import mysql.connector
from mysql.connector.errors import Error 
from mysql.connector.errors import IntegrityError
from mysql.connector import IntegrityError 
from mysql.connector import errorcode
from selenium_helpers import wait_for_visibility, find_if_exists_by_xpath, find_if_exists_by_link_text, initialize, save_screenshot
from flow_common import db_str, load_env
from datetime import datetime
from pytz import timezone
import json
import numpy as np
import traceback
import re

load_env()
# import pymysql
# import socket
# socket.getaddrinfo('192.168.0.14', 3306)
# pip install mysqlclient

# sudo apt install libmysqlclient-dev
driver = None
logged_in = False
sub_path = 'UGA/stmts/CCA'

stmt_ngin = create_engine('mysql+mysqlconnector://' + db_str(), pool_size=10)

def login(username,password):
	global logged_in

	driver.get("https://chapchap.co/app/#!/login")
	sleep(3);
	username_textbox = driver.find_element_by_name("username")
	username_textbox.send_keys(username)
	password_textbox = driver.find_element_by_name("password")
	password_textbox.send_keys(password)

	button = driver.find_element_by_xpath("//button[@id='login-submit']")
	driver.execute_script("arguments[0].click();", button)

def check_cr_dr(record):
	stmt_txn_type = record['stmt_txn_type']
	dr_amt, cr_amt, amount = 0, 0, 0
	if(stmt_txn_type == 'used stock'):
		stmt_txn_type = 'debit'
		dr_amt = record['amount']

	elif((stmt_txn_type == 'received stock') or ('received' in stmt_txn_type)):
		stmt_txn_type = 'credit'
		cr_amt = record['amount']	
	else:
		stmt_txn_type = 'unknown';
		amount = record['amount'] 

	return pd.Series([stmt_txn_type, dr_amt, cr_amt, amount])

def rename(txn_df):
	txn_df.rename(columns={'Type':'stmt_txn_type',
							'Amount':'amount',
							'Transaction ID': 'stmt_txn_id',
							'Date' : 'stmt_txn_date',
							'Description' : 'descr',
							},inplace=True)
	txn_df = txn_df.astype({'stmt_txn_id': str})

def parse_date(date):
	from datetime import datetime
	#="01/03/2018 01:03:43 AM"
	if (':' in date):
		return datetime.strptime(date, '%a, %d %b %Y %H:%M:%S')	
	else: 
		raise Exception("")

reversal_token_list = ['Reversal']



def transform(txn_df):
	
	txn_df['amount'] = txn_df['amount'].apply(to_num)
	txn_df['balance'] = txn_df['balance'].apply(to_num)
	txn_df['stmt_txn_date'] = txn_df['stmt_txn_date'].apply(parse_date)	
	#txn_df['amount'] = txn_df['amount'].apply(lambda x : np.absolute(x))
	txn_df[['stmt_txn_type', 'dr_amt', 'cr_amt', 'amount']] = txn_df.apply(check_cr_dr, axis=1)
	# txn_df['stmt_txn_type'] = txn_df.apply(chk_type, axis=1)
	# txn_df['descr_contains'] = txn_df.apply(get_descr_contains, axis=1)
	txn_df['import_id'] = run_id
	txn_df['account_id'] = account_id
	txn_df['acc_prvdr_code'] = acc_prvdr_code
	txn_df['network_prvdr_code'] = network_prvdr_code
	txn_df['acc_number'] = acc_number
	txn_df['country_code'] = 'UGA'

	return txn_df

def load_df(txn_df , is_row_df = False, row_index = 0):
	from sqlalchemy import exc
	num_rows = len(txn_df)
	txn_df = txn_df.drop(columns=['Unnamed: 5'])

	#Iterate one row at a time
	for i in range(num_rows):
		try:
			txn_df.iloc[i:i+1].to_sql('account_stmts', con = stmt_ngin, if_exists='append', chunksize = 500, index = False,
										dtype={'stmt_txn_id': sqlalchemy.types.NVARCHAR(length=40), 	
										'stmt_txn_type' : sqlalchemy.types.NVARCHAR(length=32),
										'descr' : sqlalchemy.types.NVARCHAR(length=100),
								
									})
		except exc.IntegrityError as e:
			err = e.orig.args

			if('Duplicate entry' in err[1]):
				pass
			else:
				raise(err)	

def load_df_each_row(txn_df):
	for index, row  in txn_df.iterrows():
		try:
			row_df = pd.DataFrame(row).transpose()
			load_df(row_df, True ,index)
		except Exception as e:
			raise e
	
def get_txn_table():

	balance = find_if_exists_by_xpath("//div[@class = 'ng-scope']/div/div/div/h5", 16).text
	balance = balance.replace("UGX" ,"").replace(",","")
	sleep(8)
	find_if_exists_by_link_text("Transactions").click()
	btn = find_if_exists_by_xpath("//div[@ng-show = 'callSuccess']/div/div/div/div/button[4]", 16)
	driver.execute_script("arguments[0].click();", btn)	

	txn_df = pd.read_html(driver.page_source,flavor = None)

	txn_df = pd.DataFrame(txn_df[0])
	txn_df['balance'] = np.nan
	txn_df.at[1,'balance'] = balance
	txn_df = txn_df.drop([0], axis = 0)
	rename(txn_df)
	return txn_df


def isnum(value):
	if value == '':
		return False
	try:
		import math
		return not math.isnan(float(value))
	except:
		return True

def to_num(amt):
	if(type(amt) is str):
		amt = amt.strip()
		
	if isnum(amt):
		return float(amt)
	else:
		return 0.0	

def clean_column(data):
	
	if(data.startswith("=\"")):
		data = data.replace("=", "")
		data = data.replace("\"", "")
	
	return data	

def mark_reversal(record):
	txn_df.loc[txn_df['stmt_txn_id'] == record['stmt_txn_id'], 'is_reversal'] = True

def close_all():
    
    if(logged_in):
        driver.find_element_by_css_selector("li.dropdown-nav").click()
        driver.find_element_by_link_text("Log Out").click()
    
    if(driver):
        driver.close()
        driver.quit()


def main(username,password):

	try:
		global driver, status, screenshot_path
		status, screenshot_path, exc = "uninitiated", "", ""
		time_zone = timezone('Africa/Kampala')
		driver = initialize()
		login(username,password)
		txn_df = get_txn_table()
		txn_df = transform(txn_df)	
		load_df(txn_df)
		status = "success"
	except Exception as e:
		exc = repr(e) + '\n' + traceback.format_exc()
		exc = re.sub('"','\\"',exc)
		status = 'failure'
		screenshot_path = save_screenshot(base_path, sub_path , status + '_exception', str(run_id))
		now = datetime.now(time_zone)
		now = now.strftime("%Y-%m-%d %H:%M:%S")
		query = "update float_acc_stmt_imports set status='failed', end_time='{}', exception = \"{}\" where id = {}".format(now, exc, run_id)
		stmt_ngin.execute(query)
	finally:
		close_all()

	return status, screenshot_path, exc


if(len(sys.argv) > 2):
	username = sys.argv[1]
	password = sys.argv[2]
	account_id = sys.argv[3]
	run_id = sys.argv[4]
	acc_number = sys.argv[5]
	acc_prvdr_code = sys.argv[6]
	network_prvdr_code = sys.argv[7]
else:

	data = json.loads(sys.argv[1])

	username = data.get('username')
	password = data.get('password')
	account_id = data.get('account_id')
	run_id = data.get('import_id')
	acc_number = data.get('acc_number')
	acc_prvdr_code = data.get('acc_prvdr_code')
	network_prvdr_code = data.get('network_prvdr_code')
	base_path = data.get('storage_path')

     
status, screenshot_path, exception = main(username,password)
response = {'status' : status, 'screenshot_path' : screenshot_path, 'traceback' : exception}

print(json.dumps(response))







