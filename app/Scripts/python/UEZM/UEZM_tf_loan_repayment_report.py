import sys
import json
import pandas as pd
import os

curr_file_path = os.path.realpath(__file__)
parent_dir = curr_file_path.rsplit('/',2)[0]
sibling_dir = parent_dir+'/'+'vendors/stmts'

sys.path.append(sibling_dir)

from selenium_helpers import find_if_exists_by_xpath,find_if_exists_by_link_text,find_if_exists_by_id
from UEZM_login import login
from UEZM_login import driver
from sqlalchemy import create_engine
from datetime import datetime
from time import sleep
from pytz import timezone
from selenium.common.exceptions import TimeoutException
from flow_common import db_str, load_env

load_env()
logged_in = False

stmt_ngin = create_engine('mysql+mysqlconnector://'+db_str(), pool_size=10)

eastern = timezone('Africa/Kampala')
loc_dt = datetime.now(eastern)
# today_date = loc_dt.strftime('%d/%m/%Y')

def rename(txn_df):

    txn_df.rename(columns = {
                        'Merchant Code':'from_acc_num',
                        'Merchant Name':'from_acc_name',
                        'Transaction DateTime': 'stmt_txn_date',
                        'Amount Deducted' : 'cr_amt',
                        'Total Collected' : 'tot_repaid',
                        'Loan Balance'	 : 'tot_bal',
                        },inplace=True)
	
    return txn_df

def parse_date(date):

    #"19/01/2022 11:08:58"
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

def transform(txn_df):
	
    txn_df['to_acc_num'] = "92249908"
    txn_df['stmt_txn_date'] = txn_df['stmt_txn_date'].apply(parse_date)
    txn_df['cr_amt'] = txn_df['cr_amt'].apply(to_num)
    txn_df['tot_repaid'] = txn_df['tot_repaid'].apply(to_num)	
    txn_df['tot_bal'] = txn_df['tot_bal'].apply(to_num)
    txn_df['country_code'] = 'UGA'
    txn_df['acc_prvdr_code'] = 'UEZM'
    txn_df['created_at'] = (loc_dt.strftime('%Y-%m-%d %H:%M:%S'))
    
    return txn_df

def load_df(txn_df):

	from sqlalchemy import exc
	num_rows = len(txn_df)	
	#Iterate one row at a time
	for i in range(num_rows):
		try:
			txn_df.iloc[i:i+1].to_sql('tf_repay_txn_imports', con = stmt_ngin, if_exists='append', 
				chunksize = 500, index = False)
		except exc.IntegrityError as e:
			err = e.orig.args
			if('Duplicate entry' in err[1]):
				pass
			else:
				raise(err)	

def load_inner_table(skip_last_two_rows):

    table = find_if_exists_by_xpath("//table[@id = 'MainContent_gvData']")

    if(table):
        table_txns = table.get_attribute("outerHTML")
        txn_df = pd.read_html(table_txns)
        txn_df = pd.DataFrame(txn_df[0])
        row_count = txn_df.shape[0]

        if(skip_last_two_rows):
            txn_df = txn_df.iloc[0:row_count-2]

        txn_df = txn_df[['Merchant Code','Merchant Name','Transaction DateTime','Amount Deducted','Total Collected','Loan Balance']]
        rename(txn_df)
        txn_df = transform(txn_df)
        load_df(txn_df)
        status, message = 'success', 'Imported'
    else:
        status, message = 'failure', 'No record found'

    return status, message
    
def loan_repayment_report():

    global logged_in

    if(find_if_exists_by_link_text('Reports')):

        logged_in = True

        find_if_exists_by_link_text('Reports').click()
        find_if_exists_by_link_text('Loan Repayment Report').click()
        find_if_exists_by_id('MainContent_txtSearchSCCode').send_keys(sc_code)
        find_if_exists_by_id('MainContent_txtSearchStartDate').send_keys(start_date)
        # find_if_exists_by_id('MainContent_txtSearchEndDate').send_keys(end_date)
        find_if_exists_by_id('MainContent_btnSearch').click()

        current_page_xpath = "//table[contains(@id, 'MainContent_gvData')]//tr[last()]//table//span"
        current_page = find_if_exists_by_xpath(current_page_xpath)

        if(not current_page): # Pagination does not exist
            status, message = load_inner_table(False)
        else: 
            status, message = load_inner_table(True)
            current_page = current_page.text
            next_active_link_xpath = "//table[contains(@id, 'MainContent_gvData')]//tr[last()]//table//span//following::a[1]"
            next_active_link = find_if_exists_by_xpath(next_active_link_xpath)

            while(next_active_link != None):

                try:
                    next_active_link.click()
                    while(current_page == find_if_exists_by_xpath(current_page_xpath).text):
                        sleep(1)
                    status, message = load_inner_table(True)
                    current_page = find_if_exists_by_xpath(current_page_xpath).text
                    next_active_link = find_if_exists_by_xpath(next_active_link_xpath)
                except TimeoutException:
                    break
    else:
        status, message = "failure", "Login failed"

    return status, message

def close_all():
    
    if(logged_in):
        find_if_exists_by_id('btnLogout').click()
    
    if(driver):
        driver.close()
        driver.quit()

def get_loan_repayment_report():

    status = 'failure'
    message = ''

    try:
        login(username,password,link)
        status, message = loan_repayment_report()

    except Exception as e:   

        import traceback
        global tb
        tb = traceback.format_exc()
        message += " : " + str(e)
        status = 'failure'

    finally:
        close_all()

    return status, message

data = json.loads(sys.argv[1])

username = data.get('username')
password = data.get('password')
sc_code = data.get('acc_number')
start_date = (data.get('start_date').replace('-','/'))

link = "https://ug.ezeemoney.biz/SC"


status, message = get_loan_repayment_report()
response = {'status' : status, 'message' : message}
print(json.dumps(response))


#92249908
#ABC@#abc9879
