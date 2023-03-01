from selenium.common.exceptions import TimeoutException 
from time import sleep
from sqlalchemy import create_engine
import sys
import mysql.connector
from selenium_helpers import wait_for_visibility, find_if_exists_by_xpath, find_if_exists_by_link_text, initialize, save_screenshot
from mysql.connector.errors import Error 
from mysql.connector.errors import IntegrityError
from mysql.connector import IntegrityError 
from mysql.connector import errorcode
import pandas as pd
import sqlalchemy
import logging
from pytz import timezone
from datetime import datetime
import pytz
import json
from flow_common import db_str, load_env
import traceback
import re


load_env()
driver = None
logged_in = False
sub_path = 'UGA/stmts/UEZM'

stmt_ngin = create_engine('mysql+mysqlconnector://' + db_str(), pool_size=10)


def rename(txn_df):
	txn_df.rename(columns={'Debit':'dr_amt',
							'Credit':'cr_amt',
							'Trans. ID': 'stmt_txn_id',
							'Trans. Datetime' : 'stmt_txn_date',
							'Trans. Description' : 'descr',
							'Running Balance'	 : 'balance',
							'Terminal' : 'ref_account_terminal'
							},inplace=True)
	
	return txn_df

def login(username, password, driver):
	global logged_in
	driver.get("https://ug.ezeemoney.biz/SC/NoTACLogin.aspx")
	sleep(1)
	driver.find_element_by_id('txtUserID').send_keys(username)
	driver.find_element_by_id('txtPassword').send_keys(password)
	driver.find_element_by_id('btnLogin').click()

def load_inner_table(skip_last_two_rows):

	table = find_if_exists_by_xpath("//table[@id = 'MainContent_gvData']")

	if(table):
		table_txns = table.get_attribute("outerHTML")
		txn_df = pd.read_html(table_txns)
		#txn_df = pd.read_html(driver.page_source, attrs={'id': 'MainContent_gvData'})
		txn_df = pd.DataFrame(txn_df[0])
		row_count = txn_df.shape[0]

		if(skip_last_two_rows):
			txn_df = txn_df.iloc[0:row_count-2,0:8]

		txn_df = txn_df.drop(columns=['Card ID'])
		rename(txn_df)
		txn_df = transform(txn_df)
		load_df(txn_df)
	else:
		# close_all();
		#raise Exception("There is no transactions for today")
		pass

def get_txn_table():

	find_if_exists_by_link_text('Transaction Details').click()
	find_if_exists_by_link_text('Account Transaction').click()
	sleep(1)
	eastern = timezone('Africa/Kampala')
	loc_dt = datetime.now(eastern)
	today_date = loc_dt.strftime('%d/%m/%Y')
	start_date = driver.find_element_by_id('MainContent_txtFromDate')
	start_date.clear()
	start_date.send_keys(today_date)
	#end_date = driver.find_element_by_id('MainContent_txtToDate')
	#end_date.clear()
	#end_date.send_keys(today_date)
	
	driver.find_element_by_id('MainContent_btnSearch').click()
	
	current_page_xpath = "//table[contains(@id, 'MainContent_gvData')]//tr[last()]//table//span"
	current_page = find_if_exists_by_xpath(current_page_xpath)
	if(not current_page): # Pagination does not exist
		load_inner_table(False)
	else: 
		current_page = current_page.text
		next_active_link_xpath = "//table[contains(@id, 'MainContent_gvData')]//tr[last()]//table//span//following::a[1]"
		next_active_link = find_if_exists_by_xpath(next_active_link_xpath)
		while(next_active_link != None):
			try:
				load_inner_table(True)
				next_active_link.click()
				
				while(current_page == find_if_exists_by_xpath(current_page_xpath).text):
					sleep(1)
				current_page = find_if_exists_by_xpath(current_page_xpath).text
				next_active_link = find_if_exists_by_xpath(next_active_link_xpath)
				load_inner_table(True)
				
			
			
			except TimeoutException:
			
				break

def parse_date(date):
	#"19/01/2021 11:08:58"
	if ('/' in date):
		return datetime.strptime(date,'%d/%m/%Y %H:%M:%S')	
	else: 
		raise Exception("")


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
		return abs(float(amt))
	else:
		return 0.0	

def chk_type(record):
	dr_amt, cr_amt, amount = 0, 0, 0
	if(record['cr_amt'] == 0):
		return 'debit'
	elif(record['dr_amt'] == 0):
		return 'credit'

def to_num_cr_amt(record):
	record['cr_amt'] = to_num(record['cr_amt'])
	descr = record['descr']
	if (descr.startswith('Col/FLOW')):
		return record['cr_amt']+1765
	else:
		return record['cr_amt']

def transform(txn_df):
	
	txn_df['cr_amt'] = txn_df.apply(to_num_cr_amt,axis=1)
	txn_df['dr_amt'] = txn_df['dr_amt'].apply(to_num)
	txn_df['stmt_txn_date'] = txn_df['stmt_txn_date'].apply(parse_date)
	txn_df['balance'] = txn_df['balance'].apply(to_num)	
	txn_df['stmt_txn_type'] = txn_df.apply(chk_type, axis=1)
	txn_df['stmt_txn_id'] = txn_df['stmt_txn_id'].astype(str).str.replace('\.0', '')
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

	
	#Iterate one row at a time
	for i in range(num_rows):
		try:
			txn_df.iloc[i:i+1].to_sql('account_stmts', con = stmt_ngin, if_exists='append', 
				chunksize = 500, index = False)
		except exc.IntegrityError as e:
			err = e.orig.args
			if('Duplicate entry' in err[1]):
				pass
			else:
				raise(err)	

def close_all():
    
    if(logged_in):
        find_if_exists_by_xpath("[@id = 'btnLogout']").click()
    
    if(driver):
        driver.close()
        driver.quit()


def main(username,password):

	try:
		global driver, status, screenshot_path
		status, screenshot_path, exc = "uninitiated", "", ""
		time_zone = timezone('Africa/Kampala')
		driver = initialize()
		login(username, password, driver)
		get_txn_table()
		# txn_df = transform(txn_df)
		# load_df(txn_df)
		status = "success"
		# print("success")
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
	password = data.get('password_stmt')
	account_id = data.get('account_id')
	acc_number = data.get('acc_number')
	acc_prvdr_code = data.get('acc_prvdr_code')
	network_prvdr_code = data.get('network_prvdr_code')
	run_id = data.get('import_id')
	base_path = data.get('storage_path')

if __name__ == '__main__':
	status, screenshot_path, exception = main(username,password)
	response = {'status' : status, 'screenshot_path' : screenshot_path, 'traceback' : exception}

	print(json.dumps(response))

		
