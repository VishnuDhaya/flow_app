import json
import sys
import os
import traceback
import re
import pandas as pd
import logging

curr_file_path = os.path.realpath(__file__)
parent_dir = curr_file_path.rsplit('/',2)[0]
sibling_dir = parent_dir+'/' + 'payment'

sys.path.append(sibling_dir)

from time import sleep
from datetime import datetime, time, date, timedelta
from pytz import timezone
from sqlalchemy import create_engine
from sqlalchemy.engine import create_engine
from selenium_helpers import find_if_exists_by_xpath, initialize, save_screenshot
from flow_common import load_env, env, db_str
from RBOK import login

driver = None
logged_in = False
load_env()
logger = None
time_zone = timezone('Africa/Kigali')
sub_path = 'RWA/stmts/RBOK'

stmt_ngin = create_engine('mysql+mysqlconnector://' + db_str(), pool_size=10)
stmt_db_con = stmt_ngin.connect()

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
        amt = re.sub(",", '',(amt.strip('RWF')))  
    if isnum(amt):
        return abs(float(amt))
    else:
        return 0.0	

def chk_txn_type(record):

    if(record['cr_amt'] == 0):
        return 'debit'
    elif(record['dr_amt'] == 0):
        return 'credit'

def rename(txn_df):
    
	txn_df.rename(columns={"Ref Number":'stmt_txn_id',
                            "Ledger Date": 'stmt_txn_date',
                            "Description": 'descr',
                            "Amount In": "cr_amt",
                            "Amount Out": "dr_amt",
                            "Running Balance": 'balance',
						    },inplace=True)

	return txn_df

def parse_date(date, format):

    if ('/' in date):
        # date_time_obj =  datetime.strptime(date, format)	
        # return datetime.combine(date_time_obj, time(datetime.now(time_zone).hour, datetime.now(time_zone).minute))
        return datetime.strptime(date, format)
    else: 
        raise Exception("")

def transform(txn_df,from_date):

    txn_df['dr_amt'] = txn_df['dr_amt'].apply(to_num)
    txn_df['cr_amt'] = txn_df['cr_amt'].apply(to_num)
    txn_df['balance'] = txn_df['balance'].apply(to_num)
    # txn_df['stmt_txn_date'] = txn_df.apply(get_parse_date, from_date=from_date, axis=1)
    txn_df[['is_future_txn', 'stmt_txn_date', 'value_date']] = txn_df.apply(get_parse_date, from_date=from_date, axis=1, result_type="expand")
    txn_df['stmt_txn_type'] = txn_df.apply(chk_txn_type, axis=1)
    txn_df = remove_dup_future_txn(txn_df, account_id, from_date)
    txn_df['import_id'] = import_id
    txn_df['account_id'] = account_id
    txn_df['acc_prvdr_code'] = acc_prvdr_code
    txn_df['network_prvdr_code'] = network_prvdr_code
    txn_df['acc_number'] = from_acc_num
    txn_df['country_code'] = 'RWA'

    return txn_df

def load_df(txn_df):
    
    from sqlalchemy import exc
    num_rows = len(txn_df)	

    for i in range(num_rows):
        try:
            txn_df.iloc[i:i+1].to_sql('account_stmts', con = stmt_ngin, if_exists='append', chunksize = 500, index = False)
        except exc.IntegrityError as e:
            err = e.orig.args
            if('Duplicate entry' in err[1]):
                pass
            else:
                raise(err)	

def load_inner_table(from_date):

    table = find_if_exists_by_xpath('//div[contains(@class,"statement-body")]/table[contains(@class,"table-responsive")]/thead')

    if(table):
        table = find_if_exists_by_xpath('//div[contains(@class,"statement-body")]/table[contains(@class,"table-responsive")]')
        sleep(7)
        table_txns = table.get_attribute("outerHTML")
        txn_df = pd.read_html(table_txns)
        txn_df = pd.DataFrame(txn_df[0])
        txn_df = txn_df.drop(columns=['Credit/Debit', 'Value Date'])
        rename(txn_df)
        txn_df = transform(txn_df,from_date)
        load_df(txn_df)
    else:
        pass

def remove_dup_future_txn(txn_df, account_id, from_date):

    last_month_date = ((parse_date(from_date, '%m/%d/%Y')) + (timedelta(days=-30))).strftime('%Y-%m-%d')
    
    query = "SELECT DISTINCT stmt_txn_id FROM account_stmts WHERE account_id = {} AND is_future_txn = {} AND DATE(stmt_txn_date) > '{}'".format(account_id, 1, last_month_date)
    future_txn_ids = pd.read_sql(con=stmt_db_con, sql=query)
    stmt_txn_ids = future_txn_ids['stmt_txn_id'].tolist()
    return txn_df[~txn_df['stmt_txn_id'].isin(stmt_txn_ids)]


def get_parse_date(record, from_date):
    
    curr_date = parse_date(from_date, '%m/%d/%Y')
    stmt_txn_date = parse_date(record['stmt_txn_date'], '%d/%m/%Y')

    if(stmt_txn_date > curr_date):
        # return curr_date
        return True, curr_date, stmt_txn_date
    else:
        # return stmt_txn_date
        return False, stmt_txn_date,None

def get_txn_stmts(from_date,to_date):

    find_if_exists_by_xpath('//div[contains(@class,"accounts-list-cont")]/div[contains(@class,"account-list")]/li').click()
    sleep(2)
    # find_if_exists_by_xpath('//div[contains(@class,"menu-tab-bar")]/div[1]/ol[contains(@class,"tab-list")]/li[2]').click()
    sleep(1)
    find_if_exists_by_xpath('//input[contains(@id, "start")]').send_keys(from_date)
    find_if_exists_by_xpath('//div[contains(@class, "generate-statement-headers")]/div[1]/div/div[contains(@class,"mydevices__header--content")]/div[2]/input[contains(@class,"sort-date")]').send_keys(to_date)

    find_if_exists_by_xpath('//div[contains(@class, "generate-statement-headers")]/div[2]/button').click()

    load_inner_table(from_date)
    # next_button = find_if_exists_by_xpath('//div[contains(@class,"footer-pagination-container")]/div[contains(@class,"pagination-items")]/button[2]')

    find_if_exists_by_xpath('//div[contains(@class,"account-statement-dialog")]/div[contains(@class,"modal-content")]/div/span').click()

def close_all():
    
    # if(logged_in):
    #     find_if_exists_by_xpath('//div[contains(@class,"nav-bar-right-lg")]/div[contains(@class,"caret-ctn")]/div/button').click()
    #     find_if_exists_by_xpath('//div[contains(@class,"nav-bar-right-lg")]/div[contains(@class,"caret-ctn")]/div/div/a[2]').click()

    if(driver):
        driver.close()
        driver.quit()

def main(username, password):

    global status, message, logger

    status, screenshot_path, message, exc = "uninitiated", "", "Error during initialize", "" 

    try:
        logging.basicConfig(filename="{}/storage/logs/{}_import_log.log".format(app_base_path, account_id),
                            filemode="a",
                            format='%(asctime)s %(message)s',
                            level=logging.WARNING)
        logger = logging.getLogger()
        logger.warning('=====================MAIN FUNCTION STARTED===={}================='.format(import_id))

        now = datetime.combine(datetime.now(time_zone), time.min)
        curr_date = now.strftime("%m/%d/%Y")
        to_date = ((parse_date(curr_date, '%m/%d/%Y')) + (timedelta(days=4))).strftime('%m/%d/%Y')

        global driver
        driver = initialize()

        status, message = login(username, password, driver, from_acc_num, False)

        get_txn_stmts(curr_date,to_date)
        status = "success"

    except Exception as e:
        exc = repr(e) + '\n' + traceback.format_exc()
        exc = re.sub('"','\\"',exc)
        status = 'failure'
        screenshot_path = save_screenshot(base_path, sub_path , status + '_exception', str(import_id))
        now = datetime.now(time_zone)
        now = now.strftime("%Y-%m-%d %H:%M:%S")
        query = "update float_acc_stmt_imports set status = 'failed', end_time = '{}', exception = \"{}\" where id = {}".format(now, exc, import_id)
        stmt_ngin.execute(query)

    finally:
        close_all()

    return status, screenshot_path, exc 

data = json.loads(sys.argv[1])

username = data.get('username')
password = data.get('password')
account_id = data.get('account_id')
import_id = data.get('import_id')
from_acc_num = data.get('acc_number')
acc_prvdr_code = data.get('acc_prvdr_code')
network_prvdr_code = data.get('network_prvdr_code')
base_path = data.get('storage_path')
app_base_path = data.get('base_path')


status, screenshot_path, exception = main(username,password)
response = {'status' : status, 'screenshot_path' : screenshot_path, 'traceback' : exception}

print(json.dumps(response))