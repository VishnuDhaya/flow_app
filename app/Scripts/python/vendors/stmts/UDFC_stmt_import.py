import json
import sys
import traceback
import re
import pandas as pd
import logging
import urllib.parse
from time import sleep
from numpy import double
from datetime import datetime, time, date
from pytz import timezone
from sqlalchemy import create_engine
from sqlalchemy.engine import create_engine
from selenium_helpers import find_if_exists_by_xpath, initialize, save_screenshot, wait_for_invisibility
from flow_common import load_env, env, db_str
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.support.ui import WebDriverWait 
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
from MTN_stmt_import import isnum,to_num


driver = None
logged_in = False
load_env()
logger = None
time_zone = timezone('Africa/Kigali')
sub_path = 'UGA/stmts/UDFC'

stmt_ngin = create_engine('mysql+mysqlconnector://' + db_str(), pool_size=10)
stmt_db_con = stmt_ngin.connect()




def chk_txn_type(record):

    if(record['cr_amt'] == 0):
        return 'debit'
    elif(record['dr_amt'] == 0):
        return 'credit'

def rename(txn_df):
    
	txn_df.rename(columns={
                            "DateTime": 'stmt_txn_date',
                            "Description": 'descr',
                            "CreditValue": "cr_amt",
                            "DebitValue": "dr_amt",
                            "Balance": 'balance',
                            "AccountTransactionId" : 'stmt_txn_id',
                            "Value" : 'amount',


						    },inplace=True)

	return txn_df

def parse_date(date, format):

    if ('-' in date):
        # date_time_obj =  datetime.strptime(date, format)	
        # return datetime.combine(date_time_obj, time(datetime.now(time_zone).hour, datetime.now(time_zone).minute))
        return datetime.strptime(date, format)
    else: 
        raise Exception("")

def transform(txn_df,from_date):
    
    txn_df['dr_amt'] = txn_df['dr_amt'].apply(to_num)
    txn_df['cr_amt'] = txn_df['cr_amt'].apply(to_num)
    txn_df['balance'] = txn_df['balance'].apply(to_num)
    txn_df['amount'] = txn_df['amount'].apply(to_num)
    txn_df['stmt_txn_date'] = pd.to_datetime(txn_df['stmt_txn_date'])
    txn_df['stmt_txn_id'] = txn_df['stmt_txn_id'].str.strip()
    txn_df['import_id'] = import_id
    txn_df['account_id'] = account_id
    txn_df['acc_prvdr_code'] = acc_prvdr_code
    txn_df['network_prvdr_code'] = network_prvdr_code
    txn_df['acc_number'] = from_acc_num
    txn_df['stmt_txn_type'] = txn_df.apply(chk_txn_type, axis=1)
    txn_df['country_code'] = 'UGA'
    txn_df['source'] = 'stmt'
    now = datetime.now(time_zone)
    txn_df['created_at']  = now.strftime("%Y-%m-%d %H:%M:%S")
    txn_df = txn_df.drop(columns=['ImageCommand', 'Custom', 'TransactionalAccountNumber','ValueDateTime', 'Currency','AccountTransactionTypeId', 'InputBranchId', 'DetailsLink'])

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
                logger.warning("Duplicate_Entry")
                pass
            else:
                raise(err)	


def get_txn_table(from_date, monthly_stmt = False, driver = None, from_acc_num = None):

    acc_button = find_if_exists_by_xpath('//div[contains(@id,"MainContent_TransactionMainContent_mainAccountsControl_rptCurrentAccounts_divAccountType_2")]', 10)
    acc_button.click()

    find_if_exists_by_xpath('//a[contains(@id,"MainContent_TransactionMainContent_accControl_TabSelector_tabMovements")]').click()

    find_if_exists_by_xpath('//img[contains(@id,"MainContent_TransactionMainContent_txpTransactions_ctl01_dataListUser_flwSearch_Img1")]', 10).click()

    fromdate_input = find_if_exists_by_xpath('//div[contains(@class, "input-control text field background")]/input[contains(@id, "MainContent_TransactionMainContent_txpTransactions_ctl01_dataListUser_flwSearch_dpFromTo_txField")]')

    fromdate_input.clear()
    fromdate_input.send_keys(from_date)

    #todate_input = find_if_exists_by_xpath('//input[contains(@id, "MainContent_TransactionMainContent_txpTransactions_ctl01_dataListUser_flwSearch_dpFromTo_txField2")]')
    # todate_input.clear()


    find_if_exists_by_xpath('//input[contains(@id,"MainContent_TransactionMainContent_txpTransactions_ctl01_dataListUser_flwSearch_btnSearch")]').click()

    image = find_if_exists_by_xpath('//div[contains(@class, "loading-image")]')

    wait_for_invisibility(By.XPATH, '//div[contains(@class, "loading-image")]', 30)
    
    next_active_link_xpath = "//div[contains(@class, 'metro')]/div[3]//li[contains(@class, 'next li-active')]/a"
   
    next_active_link = find_if_exists_by_xpath(next_active_link_xpath)
    lists = []


    if(not next_active_link):
        txns_df = load_inner_table(from_date, monthly_stmt, driver, from_acc_num )
    else: 

        table_list = load_inner_table(from_date, monthly_stmt, driver, from_acc_num )
        lists.extend(table_list)

        while(next_active_link):

            driver.execute_script("arguments[0].click();", next_active_link)

            wait_for_invisibility(By.XPATH, '//div[contains(@class, "loading-image")]', 30)

            table_list = load_inner_table(from_date, monthly_stmt, driver, from_acc_num )
            next_active_link = find_if_exists_by_xpath(next_active_link_xpath)

            lists.extend(table_list)

    return lists

def load_inner_table(from_date, monthly_stmt,driver = None, from_acc_num = None ):

    table = find_if_exists_by_xpath('//div[contains(@class,"table-grid table-responsive table hovered inside-table table-resp-to1199 table-movements no_detail")]/div/table[contains(@id,"MainContent_TransactionMainContent_txpTransactions_ctl01_dataListUser_gridData")]/tbody/tr[contains(@class, "table-header")]')

    if(table):

        body_tables = driver.find_elements_by_xpath('//td[contains(@class, "visible-md visible-lg visible-sm")]/input[contains(@type,"hidden")]')

        list = []

        for body_table in body_tables:
            value = body_table.get_attribute("value")
            parsed_url = urlparse(value)
            obj = parse_qs(parsed_url.query)
            # obj = urllib.parse.parse_qs(value)
            inner_obj = obj['Statement'][0]
            inner_obj = json.loads(inner_obj)
            del inner_obj['ExtendedProperties']
            list.append(inner_obj)

        txn_df = pd.DataFrame(list)

        if(monthly_stmt == False):
            rename(txn_df)
            txn_df = transform(txn_df,from_date)
            load_df(txn_df)

        return list

        

    else:
        pass




def login(username, password, driver):

    global logged_in, new_id, new_otp, status, message, screenshot_path
    status, message = "uninitiated", "Error during login. Please retry"

    driver.get("https://internet.dfcugroup.com/")
 
    find_if_exists_by_xpath('//div[contains(@class, "gdprIntro_intro")]/div[3]/button[2]').click()

    find_if_exists_by_xpath('//input[contains(@id,"MainContentFull_ebLoginControl_txtUserName_txField")]').send_keys(username)
    find_if_exists_by_xpath('//input[contains(@id,"MainContentFull_ebLoginControl_btnGoToSecondStep")]').click()

    find_if_exists_by_xpath('//input[contains(@id,"MainContentFull_ebLoginControl_txtPassword_txField")]').send_keys(password)
    find_if_exists_by_xpath('//input[contains(@id,"MainContentFull_ebLoginControl_btnLogin")]').click()
    
    auth_failed_msg = find_if_exists_by_xpath('//div[contains(@id, "MainContentFull_ebLoginControl_ValidationMessage")]', 5)
    
    if(auth_failed_msg):
        raise Exception(auth_failed_msg)


    return status, message


def close_all():

    if(driver):
        driver.close()
        driver.quit()

def main(username, password, from_acc_num):

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
        curr_date = now.strftime("%d-%m-%Y")

        global driver
        driver = initialize()

        login(username, password, driver)

        get_txn_table(curr_date, False, driver, from_acc_num  )
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


if __name__ == '__main__':
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


    status, screenshot_path, exception = main(username, password, from_acc_num)
    response = {'status' : status, 'screenshot_path' : screenshot_path, 'traceback' : exception}

    print(json.dumps(response))