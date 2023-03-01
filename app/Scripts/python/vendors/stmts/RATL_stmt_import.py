import json
import sys
import os
import pandas as pd
from flow_common import db_str, load_env, env, load_df
from selenium_helpers import initialize_driver_n_logger, find_if_exists_by_xpath, find_if_exists_by_id, save_screenshot
from time import sleep
from datetime import datetime, time, timedelta
from pytz import timezone
from sqlalchemy import create_engine
from sqlalchemy.engine import create_engine
import traceback
import re

driver = None
load_env()
logger = None
stmt_ngin = create_engine('mysql+mysqlconnector://' + db_str(), pool_size=10)
SUCCESS_RESPONSE = 'Your request is being processed and report will be sent to you via email entered.'

def close_all():
    driver.close()
    driver.quit()

def fill_login_n_password(username, password):
    find_if_exists_by_id('login_doaccesscodes_loginId', 5).send_keys(username)
    find_if_exists_by_id('passid', 5).send_keys(password)
    find_if_exists_by_id('login_doaccesscodes_0', 5).click()

def close_button():
    find_if_exists_by_id("button_close").click()
    assert driver.switch_to.alert.text == "closing"
    driver.switch_to.alert.accept()

def logout_using_url(url):
    driver.get(url)
    close_button()

def logout_using_button():
    logout_button = find_if_exists_by_xpath("//a[contains(text(),'Logout')]")
    if logout_button: 
        logout_button.click()
        close_button()

def submit_form(email):
    find_if_exists_by_id("sForm_email").click()
    find_if_exists_by_id("sForm_email").send_keys(email)
    find_if_exists_by_id("sForm_button_download").click()

def fill_report_details(agent_id):
    # Payment Instrument
    instrument_xpath= "//form[@id='sForm']/table/tbody/tr[4]/td[2]/span/input[3]"
    find_if_exists_by_xpath(instrument_xpath).send_keys('WALLET')
    # Transaction Status 
    txn_status_xpath = "//form[@id='sForm']/table/tbody/tr[10]/td[2]/span/input[3]"
    find_if_exists_by_xpath(txn_status_xpath).send_keys('Transaction Success')
    # MSISDN
    msisdn_field = find_if_exists_by_id('sForm_msisdn')
    msisdn_field.clear()
    msisdn_field.send_keys(agent_id)

def set_from_n_to_date(from_date, to_date):
    # From Date Field
    from_date_field = find_if_exists_by_xpath("//input[@name='dojo.fromDateStr']")
    from_date_field.clear()
    from_date_field.send_keys(from_date)
    # To Date Field
    to_date_field = find_if_exists_by_xpath("//input[@name='dojo.toDateStr']")
    to_date_field.clear()
    to_date_field.send_keys(to_date)

def check_submit_status():
    submit_status_xpath = '//*[@id="header"]/table[1]/tbody/tr/td/table/tbody/tr[3]/td[2]/ul/li/span'
    submit_status_element = find_if_exists_by_xpath(submit_status_xpath)
    assert submit_status_element.text == SUCCESS_RESPONSE

def get_transaction_reports(from_date, to_date, agent_id, email):
    xpath = "//a[contains(text(),'Transaction Reports')]"
    find_if_exists_by_xpath(xpath).click()
    find_if_exists_by_id('MMRPTTXN_RPT_CHD').click()
    set_from_n_to_date(from_date, to_date)
    fill_report_details(agent_id)
    submit_form(email)
    check_submit_status()

def execute_logout_links():
    urls = [
        'https://197.157.130.8:5555/AirtelMoney/login/login_logout.action?button.close=Close',
        'https://197.157.130.8:5555/AirtelMoney/login/login_logedout.action?button.close=Close',
    ]
    for url in urls:
        logout_using_url(url)

def get_url(country):
    if country == 'RWA':
        return 'https://197.157.130.8:5555/AirtelMoney/'

def login(username, password, country_code, driver):
    execute_logout_links() # Try all logout links incase logout failed previously
    url = get_url(country_code)
    driver.get(url)
    driver.switch_to.frame(0) # Airtel HTML was inside first iframe
    fill_login_n_password(username, password)

def get_stmt_path(country_code):
    if country_code == 'RWA':
        return 'RWA/stmts/RATL'

def rename_n_clean_columns(df):
    rename_columns = {
        'TRANSFER_ID': 'stmt_txn_id',
        'TRANSFER_DATE': 'stmt_txn_date',
        'SENDER_NAME': 'sender_name',
        'PAYEE_NAME': 'receiver_name',
        'USER_ACCOUNT_NUMBER': 'sender_msisdn',
        'RECEIVER_ACCOUNT_NUMBER': 'receiver_msisdn',
        'POST_BALANCE': 'sender_post_balance',
        'PAYEE_POST_BALANCE': 'receiver_post_balance',
        'TRANSACTION_AMOUNT': 'amount',
    }
    df = df.loc[:,df.columns.isin(rename_columns.keys())]
    df.rename(columns=rename_columns, inplace=True)
    return df

def format_date(txn_date_column):
    format = '%d-%b-%Y %H:%M:%S'
    txn_date_column = pd.to_datetime(txn_date_column, format=format)
    return txn_date_column.dt.strftime('%Y-%-m-%d %H:%M:%S')

def fill_columns(record, agent_id):
    # Set txn_type as debit if sender_msisdn matches with ours
    sender_msisdn = record['sender_msisdn']
    txn_type = 'debit' if (sender_msisdn == agent_id) else 'credit'

    # Balance was stored separately in sender and receiver columns instead of a single column
    balance = record['sender_post_balance'] if (txn_type == 'debit') else record['receiver_post_balance']
    # In both cases the msisdn of our customer is being stored for recon purposes
    ref_account_num = record['receiver_msisdn'] if (txn_type == 'debit') else record['sender_msisdn']
    # In both cases the name of our customer is being stored for recon purposes
    descr = record['receiver_name'] if (txn_type == 'debit') else record['sender_name']
    descr = "/{}".format(descr)
    # Fill credit and debit columns
    amount = record['amount']
    dr_amt = amount if txn_type == 'debit' else 0
    cr_amt = amount if txn_type == 'credit' else 0

    return txn_type, balance, ref_account_num, dr_amt, cr_amt, descr

def convert_to_string(df, columns):
    for column in columns:
        df[column] = df[column].astype('string')
    return df

def set_account_info(df, account_info):
    infos_to_set = [
        'account_id',
        'acc_prvdr_code',
        'network_prvdr_code',
        'acc_number',
        'country_code',
        'import_id'
    ]
    for info_to_set in infos_to_set:
        df[info_to_set] = account_info[info_to_set]
    return df

def get_df(file_path):
    try:
        df = pd.read_csv(file_path, skiprows=4)
    except pd.errors.EmptyDataError:
        df = pd.DataFrame()
    return df

def remove_whitespace(df):
    df.columns = df.columns.str.strip()
    df.applymap(lambda x: x.strip() if isinstance(x, str) else x)
    return df 

def clean_df(df, data):
    agent_id = data['acc_number']
    df = rename_n_clean_columns(df)
    df = df[df.amount != 0] # Remove records where amount is 0
    df = remove_whitespace(df)
    
    columns = ['receiver_msisdn', 'sender_msisdn']
    df = convert_to_string(df, columns)

    df['stmt_txn_date'] = format_date(df['stmt_txn_date'])
    df[['stmt_txn_type', 'balance', 'ref_account_num', 'dr_amt', 'cr_amt', 'descr']] = df.apply(fill_columns, agent_id=agent_id, axis=1, result_type='expand')

    df = set_account_info(df, data)
    now = datetime.now(time_zone).strftime("%Y-%m-%d %H:%M:%S")
    df = df.assign(source='stmt', created_at = now)

    columns = ['sender_post_balance','receiver_post_balance', 'sender_msisdn', 'receiver_msisdn', 'sender_name', 'receiver_name']
    df.drop(columns, axis=1, inplace=True, errors='ignore')
    return df

def process_stmt(data):
    df = get_df(data['stmt_path'])
    if not df.empty:
        df = clean_df(df, data)
        load_df(df, stmt_ngin)
    return df

def remove_stmt(file_path):
    if os.path.isfile(file_path):
        os.remove(file_path)

def request_txn_data(data, now):

    global driver
    username = data['username']
    password = data['password']
    account_id = data['account_id']
    agent_id = data['acc_number']
    app_base_path = data['base_path']
    import_id = data['import_id']
    country_code = data['country_code']

    # from_date = '10/11/2022'
    # to_date = '30/11/2022'

    # from_date = (now - timedelta(days=1)).strftime("%d/%m/%Y")
    from_date = now.strftime("%d/%m/%Y")
    to_date = now.strftime("%d/%m/%Y")
    email = data['imap_email']

    driver, logger = initialize_driver_n_logger(app_base_path, account_id, import_id)
    login(username, password, country_code, driver)
    get_transaction_reports(from_date, to_date, agent_id, email)
    return driver, logger

def update_stmt_import_status(import_id, status, now, exc):
    query = ("update float_acc_stmt_imports set status = %(status)s, end_time = %(end_time)s, exception = %(exception)s where id = %(id)s")
    stmt_ngin.execute(query, {'status': status, 'end_time': now, 'exception': exc, 'id': import_id})

def main(data, zone):

    try:
        global driver, time_zone
        status, screenshot_path, exc, stmt_path = "uninitiated", "", None, None
        
        import_id = data.get('import_id')
        country_code = data.get('country_code')
        storage_path = data.get('storage_path')

        time_zone = timezone(zone)
        now = datetime.combine(datetime.now(time_zone), time.min)

        if ('stmt_path' in data) and data['stmt_path']:
            stmt_path = data['stmt_path']
            process_stmt(data)
        else:
            driver, logger = request_txn_data(data, now)
            update_stmt_import_status(import_id, 'stmt_requested', now, None)
        status = "success"

    except Exception as e:
        exc = repr(e) + '\n' + traceback.format_exc()
        exc = re.sub('"','\\"',exc)
        status = 'failure'
        stmt_path = get_stmt_path(country_code)
        if(driver):
            screenshot_path = save_screenshot(storage_path, stmt_path , status + '_exception', str(import_id))
        update_stmt_import_status(import_id, 'failed', now, exc)

    finally:
        if stmt_path:
            remove_stmt(stmt_path)
        if(driver):
            logout_using_button()
            close_all()
    return status, screenshot_path, exc

if __name__ == '__main__':
    data = json.loads(sys.argv[1])
    data['country_code'] = 'RWA'
    status, screenshot_path, exception = main(data, 'Africa/Kigali')
    response = {'status' : status, 'screenshot_path' : screenshot_path, 'traceback' : exception}
    print(json.dumps(response))

    