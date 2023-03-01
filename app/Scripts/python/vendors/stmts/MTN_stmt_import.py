import sys
import pandas as pd
import mysql.connector
import json
from flow_common import db_str, load_env, env
from selenium_helpers import initialize, find_if_exists_by_xpath, save_screenshot
from time import sleep
from numpy import double
from selenium.webdriver.common.action_chains import ActionChains
from datetime import datetime, time, timedelta
from pytz import timezone
from sqlalchemy import create_engine
from sqlalchemy.engine import create_engine
# from flask import Flask
# from flask_mail import Mail,  Message
import traceback
import re
import logging

driver = None
logged_in = False
load_env()
no_of_times = 50
logger = None
mydb = mycursor = None
stmt_ngin = create_engine('mysql+mysqlconnector://' + db_str(), pool_size=10)

# def send_mail(subject, body, recipients):

#     app = Flask(__name__)
#     sender_mail = env['MAIL_FROM_ADDRESS'].strip("'")
#     print(env['MAIL_USERNAME'])
#     app.config['MAIL_SERVER']= env['MAIL_HOST']
#     app.config['MAIL_USERNAME'] = env['MAIL_USERNAME'].strip("'")
#     app.config['MAIL_PASSWORD'] = env['MAIL_PASSWORD'].strip("'")
#     app.config['MAIL_USE_SSL'] = True
#     app.config['MAIL_PORT'] = env['MAIL_PORT']
#     mail = Mail(app)
#     msg = Message(body = body, subject = subject, sender = sender_mail, recipients = recipients)
#     with app.app_context():      
#     mail.send(msg)

def connect_db():

	global mydb, mycursor

	if mycursor is not None:
		mycursor.close()
	if mydb is not None:
		mydb.close()
	mydb = mysql.connector.connect(
		host = env['DB_HOST'],
		user = env['DB_USERNAME'],
		password = env['DB_PASSWORD'],
		database = env['DB_DATABASE']
	)
	mycursor = mydb.cursor()

def db_select(sql):
	global mycursor

	if mycursor is None:
		connect_db()

	mycursor.execute(sql)
	result = mycursor.fetchall()

	return result
	



def rename(txn_df):

	txn_df.rename(columns={'Id':'stmt_txn_id',
							'Date': 'stmt_txn_date',
							'Note/Message': 'description',
							'From': 'from',
							'From Name': 'from_name',
							'To': 'to',
							'To Name': 'to_name',
							'Amount': 'amount',
							'Balance': 'balance'
							},inplace=True)

	return txn_df

def load_inner_table(duplicate_count, country_code):

	# table = find_if_exists_by_xpath("//div[contains(@class,'v-table v-treetable')]/div[2]/div/table[contains(@class,'v-table-table')]")
	table = find_if_exists_by_xpath("//div[contains(@id, 'VIEW_ACCOUNT_TABLE_TRANSACTIONS_SUCCESSFUL')]")

	if(table):
		table_txns = table.get_attribute("outerHTML")
		txn_df = pd.read_html(table_txns)
		table_title = pd.DataFrame(txn_df[0])
		table_data = pd.DataFrame(txn_df[1])
		table_data.columns = table_title.iloc[0]

		# txn_df = table_data[['Id', 'Date', 'Note/Message', 'From', 'From Name', 'To Name', 'Amount', 'Balance']]
		txn_df = table_data.loc[:, ['Id', 'Date', 'Note/Message', 'From', 'From Name', 'To', 'To Name', 'Amount', 'Balance']]
		txn_df.dropna(inplace=True,how = "all")

		if(not txn_df.empty):
			rename(txn_df)
			txn_df = transform(txn_df, country_code)
			duplicate_count = load_df(txn_df, duplicate_count, country_code)
	else:
		#raise Exception("There is no transactions for today")
		pass

	return duplicate_count

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
		import re
		amt =  re.sub('[^-.0-9]+', '', (amt.strip('UGX')))
	if isnum(amt):
		return abs(double(amt))
	else:
		return 0.00

def get_flow_account_id():

	flow_account_id_xpath = '//div[contains(@id,"BreadCrumb0")]/span/span' # "FLOW UGANDA  LIMITED - 256776523375"
	flow_account_id_text = find_if_exists_by_xpath(flow_account_id_xpath).text
	flow_account_id = flow_account_id_text.split(" ")
	return flow_account_id[-1]

def get_msisdn(from_id):

	MSISDN = ""
	if(not pd.isnull(from_id)):
		MSISDN = from_id.split(':') # MSISDN "FRI:256776523375/MSISDN"
		MSISDN = MSISDN[1]
		MSISDN = MSISDN.split('/')
		MSISDN = MSISDN[0]

	return MSISDN

def get_name(record):
 
	description = str(record['description'])

	if(record['dr_amt'] == 0):
		name = record['from_name']
	elif(record['cr_amt'] == 0):
		name = record['to_name']

	if(description == ''):
		return '/' + name
	else:
		return description + '/' + name

def to_num_dr_amt(record):

	# from_name = record['from_name']
	from_id = record['from']
	MSISDN = get_msisdn(from_id)
	flow_current_account_id = get_flow_account_id()
	record['dr_amt'] = to_num(record['amount'])

	if(MSISDN == flow_current_account_id):
		return record['dr_amt']
	else:
		return 0.0

def to_num_cr_amt(record):

	# from_name = record['from_name']
	from_id = record['from']
	MSISDN = get_msisdn(from_id)
	flow_current_account_id = get_flow_account_id()
	record['cr_amt'] = to_num(record['amount'])

	if(MSISDN != flow_current_account_id):
		return record['cr_amt']
	else:
		return 0.0

def get_txn_type(record):

	if(record['cr_amt'] == 0):
		return 'debit'
	elif(record['dr_amt'] == 0):
		return 'credit'

def get_new_row(last_row_id):

	global no_of_times
	global new_id, new_otp

	for i in range(no_of_times):

		sleep(1)
		new_row = fetch_last_row()

		for x in new_row:
			new_id = x[0]
			new_otp = x[1]

		if(new_id > last_row_id):
			return True

def update_status_to_taken():

	global new_otp
	global new_id

	connect_db()
	sql = "UPDATE otps SET status = 'taken' WHERE id = {}".format(new_id)
	mycursor.execute(sql)
	mydb.commit()

def update_status_to_login():

	global new_otp, new_id

	connect_db()
	sql = "UPDATE otps SET status = 'verified' WHERE id = {} AND status = 'taken'".format(new_id)
	mycursor.execute(sql)
	mydb.commit()

def fetch_last_row():

	connect_db()
	sql = "SELECT id, otp FROM otps where status = 'received' and otp_type = 'disb_portal_verify' and entity_id = '{}' ORDER BY id DESC LIMIT 1".format(agent_id)
	mycursor.execute(sql)
	myresult = mycursor.fetchall()
	mydb.commit()
	return myresult

def fetch_new_row(login_time, agent_id):

	connect_db()
	sql = "SELECT id, otp FROM otps where status = 'received' and otp_type = 'disb_portal_verify' and entity_id = '{}' and generate_time >= '{}' ORDER BY id DESC LIMIT 1".format(agent_id, login_time)
	mycursor.execute(sql)
	myresult = mycursor.fetchall()
	return myresult

def get_last_txn_date_time(account_id):

	connect_db()
	sql = "SELECT stmt_txn_date FROM account_stmts where account_id = '{}' and recon_status = '72_pending_stmt_import' ORDER BY stmt_txn_date ASC LIMIT 1".format(account_id)
	result = db_select(sql)

	if result:
		sql = "SELECT stmt_txn_date from account_stmts where account_id = '{}' and source = 'stmt' and stmt_txn_date < '{}' ORDER BY stmt_txn_date DESC LIMIT 1".format(account_id, result[0][0])
		result = db_select(sql)

	if not result :
		sql = "SELECT stmt_txn_date FROM account_stmts where account_id = '{}' ORDER BY stmt_txn_date DESC LIMIT 1".format(account_id)
		result = db_select(sql)

	return result[0][0]

def update_event_time(event_type):
	
	current_date_time = datetime.combine(datetime.now(time_zone), time(datetime.now(time_zone).hour, datetime.now(time_zone).minute, datetime.now(time_zone).second))
	format_date_time = current_date_time.strftime("%Y-%m-%d %H:%M:%S")
	connect_db()
	sql = "UPDATE float_acc_stmt_imports set event_logs = JSON_MERGE_PATCH(IFNULL(`event_logs`, '{}'), JSON_OBJECT('{}', '{}')) where id = '{}'".format('{}',event_type, format_date_time, import_id)
	mycursor.execute(sql)
	mydb.commit()
	   
def insert_pagination_event(event_type):

	connect_db()
	sql = "update float_acc_stmt_imports set event_logs = JSON_MERGE_PATCH(event_logs, JSON_OBJECT('{}', JSON_ARRAY())) where id = '{}'".format(event_type, import_id)
	mycursor.execute(sql)
	mydb.commit()

def update_pagination_event_time(event_type):

	current_date_time = datetime.combine(datetime.now(time_zone), time(datetime.now(time_zone).hour, datetime.now(time_zone).minute, datetime.now(time_zone).second))
	format_date_time = current_date_time.strftime("%Y-%m-%d %H:%M:%S")
	connect_db()
	sql = "update float_acc_stmt_imports set event_logs = JSON_MERGE_PATCH(event_logs, JSON_ARRAY_APPEND(event_logs,'$.{}','{}')) where id = '{}'".format(event_type, format_date_time, import_id)
	mycursor.execute(sql)
	mydb.commit()

def get_sms_txn_record(txn_df):

	connect_db()

	addl_sql = ""
	country_code = txn_df['country_code']

	if txn_df['stmt_txn_type'] == "debit" or country_code == "UGA":
		addl_sql = "and stmt_txn_id = '{}'".format(txn_df['stmt_txn_id'])

	if txn_df['stmt_txn_type'] == 'credit' and country_code == 'RWA':
		addl_sql += "and ref_account_num = '{}'".format(txn_df['ref_account_num'])

	sql = "SELECT id, sms_import_status, date(stmt_txn_date), recon_status, source FROM account_stmts where source in ('sms', 'manual') and (ROUND(amount) = '{}' or TRUNCATE(amount, 0) = '{}') and account_id = '{}' and country_code = '{}' and date_format(stmt_txn_date, '%Y-%m-%d %H:%i') = '{}' {} order by id desc limit 1".format(txn_df['amount'], txn_df['amount'], txn_df['account_id'], txn_df['country_code'], txn_df['stmt_txn_date'], addl_sql)
	mycursor.execute(sql)
	myresult = mycursor.fetchall()

	return myresult

def update_sms_n_loan_txn_record(id, txn_df):

	connect_db()
	stmt_txn_id = txn_df['stmt_txn_id']
	stmt_txn_id = stmt_txn_id.item() # Convert numpy dtype to native python dtype
	query = ("update account_stmts set stmt_txn_id = %(stmt_txn_id)s  where id = %(id)s")
	mycursor.execute(query, {'stmt_txn_id': stmt_txn_id, 'id': id})
	mydb.commit()

	if(txn_df['stmt_txn_type'] == 'credit' and txn_df['country_code'] == 'RWA'):
		update_loan_txn_record(stmt_txn_id)

def update_recon_status(id, txn_df):

	connect_db()
	cur_date = datetime.now(time_zone).strftime("%Y-%m-%d %H:%M:%S")
	sql = "update account_stmts set recon_status = '80_recon_done', source = 'stmt', balance = '{}', ref_account_num = '{}', import_id = '{}', descr = '{}', updated_at = '{}' where id = '{}' and recon_status = '72_pending_stmt_import'".format(txn_df['balance'], txn_df['ref_account_num'], import_id, txn_df['descr'], cur_date, id)
	mycursor.execute(sql)
	mydb.commit()

def update_loan_txn_record(stmt_txn_id):

	connect_db()
	sql = "SELECT loan_doc_id, cr_amt FROM account_stmts where stmt_txn_id = '{}' order by id desc limit 1".format(stmt_txn_id)
	mycursor.execute(sql)
	acc_stmt_record = mycursor.fetchone()

	sql = "SELECT id from loan_txns where loan_doc_id = '{}' and amount = '{}' and txn_type = 'payment' ORDER BY id DESC LIMIT 1".format(acc_stmt_record[0], acc_stmt_record[1])
	mycursor.execute(sql)
	loan_txn = mycursor.fetchone()

	cur_date = datetime.now(time_zone).strftime("%Y-%m-%d %H:%M:%S")
	sql = "update loan_txns set txn_id = '{}', updated_at = '{}' where id = {}".format(stmt_txn_id, cur_date, loan_txn[0])
	mycursor.execute(sql)
	mydb.commit()

def get_new_otp(login_time, agent_id):

	global no_of_times
	new_id, new_otp = None, None

	for i in range(no_of_times):

		sleep(3)
		new_row = fetch_new_row(login_time, agent_id)

		for x in new_row:
			new_id = x[0]
			new_otp = x[1]
		
		if(new_id):
			return new_id, new_otp

	return new_id, new_otp

def get_last_row_id():

	id = 0
	last_row = fetch_last_row()
	for x in last_row:
		id = x[0]

	return id

def get_ref_acc_num(record):

	if(record['dr_amt'] == 0):
		return get_msisdn(record['from'])
	elif(record['cr_amt'] == 0):
		return get_msisdn(record['to'])

def transform(txn_df, country_code):

	txn_df['amount'] = txn_df['amount'].apply(to_num)	
	txn_df['dr_amt'] = txn_df.apply(to_num_dr_amt,axis=1)
	txn_df['cr_amt'] = txn_df.apply(to_num_cr_amt,axis=1)
	txn_df['balance'] = txn_df['balance'].apply(to_num)	
	txn_df['ref_account_num'] = txn_df.apply(get_ref_acc_num, axis=1)
	txn_df['import_id'] = import_id
	txn_df['account_id'] = account_id
	txn_df['acc_prvdr_code'] = acc_prvdr_code
	txn_df['network_prvdr_code'] = network_prvdr_code
	txn_df['acc_number'] = agent_id
	txn_df['stmt_txn_type'] = txn_df.apply(get_txn_type, axis=1)
	txn_df['description'].fillna('', inplace=True)
	txn_df['descr'] = txn_df.apply(get_name, axis=1)
	txn_df['country_code'] = country_code
	txn_df['source'] = 'stmt'
	txn_df['created_at'] = datetime.now(time_zone).strftime("%Y-%m-%d %H:%M:%S")

	return txn_df

def load_df(txn_df, duplicate_count, country_code):

	from sqlalchemy import exc
	txn_df = txn_df.drop(columns=['description','from', 'from_name', 'to', 'to_name'])
	num_rows = len(txn_df)	

	logger.warning('Page Started')

	for i in range(num_rows):
		
		try:
			result = get_sms_txn_record(txn_df.iloc[i])

			if len(result) != 0 and (country_code == "RWA" and txn_df.iloc[i]['stmt_txn_type'] == 'credit'):

				update_sms_n_loan_txn_record(result[0][0], txn_df.iloc[i])

			if len(result) != 0 and (result[0][1] == "done" or result[0][4] == "manual"):

				update_recon_status(result[0][0], txn_df.iloc[i])

			elif len(result) != 0 and country_code == "UGA" and result[0][1] == "in_progress":
				pass

			else:
				txn_df.iloc[i:i+1].to_sql('account_stmts', con = stmt_ngin, if_exists='append', chunksize = 500, index = False)
				duplicate_count = 0

		except exc.IntegrityError as e:
			err = e.orig.args
			if('Duplicate entry' in err[1]):
				duplicate_count += 1
				if(duplicate_count >= 40):
					raise Exception(err)
				pass
			else:
				raise Exception(err)	
	logger.warning('Page Ended')
	return duplicate_count

def login(username, password, country_code, time_zone, driver, agent_id, get_stmts = False):

	global new_id, new_otp, logged_in

	url = get_url(country_code)
	driver.get(url)

	find_if_exists_by_xpath('//input[contains(@id,"USERNAME")]', 20).send_keys(username)
	find_if_exists_by_xpath('//input[contains(@id,"PASSWORD")]', 20).send_keys(password)

	today = datetime.now(time_zone)
	login_time = (today.strftime("%Y-%m-%d %H:%M:%S"))

	find_if_exists_by_xpath('//div[contains(@id,"LOGIN_BUTTON")]').click()
   # last_row_id = get_last_row_id()

	if(find_if_exists_by_xpath('//div/table/tbody/tr[contains(@class, "v-selected")]/td/div/div[contains(@id, "VIEW_ACCOUNT_LIST_TABLE_0_account.id")]', 6)):
		logged_in = True

	elif(find_if_exists_by_xpath("//div[contains(@class,'v-window')]/div[contains(@class,'popupContent')]")):
		update_event_time('otp_request_time') if get_stmts == False else ""
			
		# print("Fetching OTP...")
		new_id, otp = get_new_otp(login_time, agent_id)

		if(otp):
			update_event_time('otp_wait_time') if get_stmts == False else ""     
			update_status_to_taken()
			find_if_exists_by_xpath('//input[contains(@id, "VIEW_SECONDARY_LOGIN_MESSAGE")]').send_keys(otp)
			find_if_exists_by_xpath('//div[contains(@id, "VIEW_SECONDARY_LOGIN_BUTTON_OK")]').click()
			incorrect_otp_msg = find_if_exists_by_xpath('//div[contains(@class, "v-Notification error v-Notification-error")]/div[contains(@class, "popupContent")]/div[contains(@class,"gwt-HTML")]/h1')
			if(incorrect_otp_msg):
				raise Exception(incorrect_otp_msg.text)
			else:
				logged_in = True
				update_status_to_login()
		else:
			raise Exception("OTP Not Received")
	else:
		auth_failed_msg = find_if_exists_by_xpath('//div[contains(@class, "v-caption v-caption-error-label")]/div[contains(@class, "v-captiontext")]')
		raise Exception(auth_failed_msg.text)


def get_txn_table(from_date, country_code):

	duplicate_count = 0

	update_event_time('login_time')

	find_if_exists_by_xpath('//div/table/tbody/tr[contains(@class, "v-selected")]/td/div/div[contains(@id, "VIEW_ACCOUNT_LIST_TABLE_0_account.id")]').click()

	sleep(4)

	fromdate_input = find_if_exists_by_xpath('//div[contains(@id, "VIEW_ACCOUNT_TRANSACTION_HISTORY_FIELD_FROM_DATE")]/input[contains(@class, "v-datefield-textfield")]')
	fromdate_input.clear()
	fromdate_input.send_keys(from_date)

	# todate_input = find_if_exists_by_xpath('//div[contains(@id, "VIEW_ACCOUNT_TRANSACTION_HISTORY_FIELD_TO_DATE")]/input[contains(@class, "v-datefield-textfield")]')
	# todate_input.clear()
	# todate_input.send_keys("2023-01-18 14:31")

	find_if_exists_by_xpath('//div[contains(@id,"VIEW_ACCOUNT_TRANSACTION_HISTORY_BUTTON_SEARCH")]').click()

	next_active_link_xpath = "//div[contains(@id, 'VIEW_ACCOUNT_TABLE_TRANSACTIONS_SUCCESSFUL+PAGED_TREE_TABLE_NEXT_BUTTON')]"
	next_active_link = find_if_exists_by_xpath(next_active_link_xpath)

	insert_pagination_event('pagination_time')

	if(not next_active_link):
		sleep(2)
		load_inner_table(duplicate_count, country_code)
		update_pagination_event_time('pagination_time')
	else: 
		recordno = get_record_numbers()
		sleep(2)
		duplicate_count = load_inner_table(duplicate_count, country_code)
		update_pagination_event_time('pagination_time')
		while(next_active_link):
			new_recordno = recordno
			next_active_link.click()
			while(recordno == new_recordno):
				sleep(1)
				new_recordno = get_record_numbers()
			recordno = new_recordno
			# print(recordno)
			sleep(2)
			load_inner_table(duplicate_count, country_code)
			update_pagination_event_time('pagination_time')
			next_active_link = find_if_exists_by_xpath(next_active_link_xpath)
	logger.warning('Page Import Completed')

def get_record_numbers():

	recordno_xpath = "//div[contains(@id, 'TABLE_INFO_LABEL')]"
	recordno_text = find_if_exists_by_xpath(recordno_xpath).text
	recordno_text = recordno_text.split(" ")
	return recordno_text[1]

def close_all():

	# if(logged_in):
	#     hoever_element = find_if_exists_by_xpath('//div[contains(@id, "menubar")]')
	#     ActionChains(driver).move_to_element(hoever_element).perform()
	#     find_if_exists_by_xpath('//div[contains(@class, "v-menubar-popup")]/div/div/span[3]').click()

	if(driver):
		driver.close()
		driver.quit()


def main(data, zone):

	try:
		global driver, account_id, acc_prvdr_code, network_prvdr_code, import_id, agent_id, logger, time_zone
		status, screenshot_path, exc = "uninitiated", "", ""
		username = data.get('username')
		password = data.get('password')
		account_id = data.get('account_id')
		import_id = data.get('import_id')
		agent_id = data.get('acc_number')
		acc_prvdr_code = data.get('acc_prvdr_code')
		network_prvdr_code = data.get('network_prvdr_code')
		country_code = data.get('country_code')
		base_path = data.get('storage_path')
		app_base_path = data.get('base_path')

		if country_code == 'UGA':
			sub_path = 'UGA/stmts/UMTN'
		elif country_code == 'RWA':
			sub_path = 'RWA/stmts/RMTN'

		time_zone = timezone(zone)
		update_event_time('session_start_time')
		now = datetime.combine(datetime.now(time_zone), time.min)

		logging.basicConfig(filename="{}/storage/logs/{}_import_log.log".format(app_base_path, account_id),
							filemode="a",
							format='%(asctime)s %(message)s',
							level=logging.WARNING)
		logger = logging.getLogger()
		logger.warning('=====================MAIN FUNCTION STARTED===={}================='.format(import_id))
		driver = initialize()

		last_txn_time = get_last_txn_date_time(account_id)
		from_date = (last_txn_time - timedelta(minutes=10)).strftime("%Y-%m-%d %H:%M") #now.strftime("%Y-%m-%d %H:%M")

		update_event_time('driver_initialize_time')

		login(username, password, country_code, time_zone, driver, agent_id)
		get_txn_table(from_date, country_code)
		status = "success"

	except Exception as e:
		exc = repr(e) + '\n' + traceback.format_exc()
		exc = re.sub('"','\\"',exc)
		if('Duplicate entry' in exc):
			return status, screenshot_path, exc
		status = 'failure'
		screenshot_path = save_screenshot(base_path, sub_path , status + '_exception', str(import_id))
		now = datetime.now(time_zone)
		now = now.strftime("%Y-%m-%d %H:%M:%S")
		query = "update float_acc_stmt_imports set status = 'failed', end_time = '{}', exception = \"{}\" where id = {}".format(now, exc, import_id)
		stmt_ngin.execute(query)

	finally:
		close_all()
		update_event_time('session_end_time')
	
	return status, screenshot_path, exc


def get_url(country):
	if country == 'UGA':
		return 'https://mobilemoneyreadonly.mtn.co.ug/partner/'
	elif country == 'RWA':
		return 'https://partner.mtn.co.rw/partner/'

