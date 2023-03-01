from time import sleep
import sys
import pandas as pd

from selenium_helpers import find_if_exists_by_xpath, find_if_exists_by_link_text, initialize, save_screenshot
from pytz import timezone
from datetime import datetime, date
from dateutil.relativedelta import relativedelta
import traceback
import re
import json
import os, time
import pandas as pd
import logging


curr_file_path = os.path.realpath(__file__)
parent_dir = curr_file_path.rsplit('/',2)[0]
sibling_dir = parent_dir+'/' + 'payment'

sys.path.append(sibling_dir)
from RBOK import login as rbok_login
from MTN_stmt_import import login as mtn_login
from UEZM_stmt_import import login as uezm_login
from UDFC_stmt_import import login as udfc_login
from UDFC_stmt_import import get_txn_table


def get_time_zone(country_code):
    if country_code == 'UGA':
        return 'Africa/Kampala'
    elif country_code == 'RWA':
        return 'Africa/Kigali'

def first_day_of_prev_month(day):

    d = day - relativedelta(months=1)
    return date(d.year, d.month, 1)

def first_day_of_current_month(day):
    
    return date(day.year, day.month, 1)

def check_entire_file_downloaded(file_path):
    # Check file is empty or none
    if(file_path != None):
        file_path_list = file_path.split(".")
        if(file_path_list[-1] != "crdownload"):
            df = pd.read_csv(file_path) if(file_path_list[-1] == "csv") else pd.read_excel(file_path)
            if(len(df.index) > 0):
                return file_path
            else:
                return "file_empty"
        else:
            return None
    return None

def get_file(storage_path):

    file_path = ""
    initial_path = storage_path
    dir_empty = os.listdir(initial_path)
    if len(dir_empty) == 0 :
        return "", "";
    else:
        file_path = max([initial_path + "/" + f for f in os.listdir(initial_path)],key=os.path.getctime)
        if(file_path):
            file_creation_time = time.ctime(os.path.getctime(file_path))
            return file_creation_time, file_path

def get_downloaded_file(storage_path, old_file_time):

    file_path = ""
    initial_path = storage_path
    dir_empty = os.listdir(initial_path)
    attempts = 150

    for i in range(attempts):
        sleep(1)
        new_file_time, file_path = get_file(storage_path)
        if(len(old_file_time) > 0 or len(new_file_time) > 0):
            if(len(old_file_time) > 0 and len(new_file_time) > 0 and old_file_time < new_file_time):
                file_data = is_downloaded_file_empty(file_path)
                if(file_data != None or file_data == "failed"):
                    return file_data
            elif(i == 60 and old_file_time == new_file_time and len(dir_empty) == 1):
                file_data = is_downloaded_file_empty(file_path)
                return file_data   
    return file_path

def is_downloaded_file_empty(file_path):

    file_data = check_entire_file_downloaded(file_path)
    if(file_data == "file_empty"):
        return "failed"
    else:
        return file_data

def start_end_day_date_format(today, format = '%d/%m/%Y'):

    start_day = first_day_of_prev_month(today)
    start_day = start_day.strftime(format) 

    end_day = first_day_of_current_month(today) 
    end_day = end_day.strftime(format)

    return start_day, end_day

def get_dates_of_month(today):

    # first_day of the previous month
    firstday_of_previous_month = first_day_of_prev_month(today)

    # first_day of the current month
    firstday_of_current_month = first_day_of_current_month(today)

    firstday_of_previous_month = str(firstday_of_previous_month) + " 00:00"
    firstday_of_current_month = str(firstday_of_current_month) + " 00:00"

    return firstday_of_previous_month, firstday_of_current_month
    

def close_all(driver):
    
    # if(logged_in):
    #     find_if_exists_by_xpath('//div[contains(@class,"nav-bar-right-lg")]/div[contains(@class,"caret-ctn")]/div/button').click()
    #     find_if_exists_by_xpath('//div[contains(@class,"nav-bar-right-lg")]/div[contains(@class,"caret-ctn")]/div/div/a[2]').click()

    if(driver):
        driver.close()
        driver.quit()

def get_uezm_stmts(username, password, storage_path):

    today = date.today()
    start_day, end_day = start_end_day_date_format(today)
    driver = initialize(storage_path)

    try:
        status, message, file_path = "", "", ""

        uezm_login(username, password, driver)
        find_if_exists_by_link_text('Transaction Details').click()
        find_if_exists_by_link_text('Account Transaction').click()
        sleep(1)
        start_date = driver.find_element_by_id('MainContent_txtFromDate')
        start_date.clear()
        start_date.send_keys(start_day)
        end_date = driver.find_element_by_id('MainContent_txtToDate')
        end_date.clear()
        end_date.send_keys(end_day) 
        driver.find_element_by_id('MainContent_btnSearch').click()
        sleep(4)
        old_file_time, file_path = get_file(storage_path)
        find_if_exists_by_xpath("//div/table/tbody/tr[3]/td/input[contains(@id, 'MainContent_btnExport')]").click()
        file_path = get_downloaded_file(storage_path, old_file_time)

        if(file_path == 'failed'):
            status, message = "failure", "file download failed"
        else:
            status, message = "success", "file download successfully"
 
    except Exception as e:
        exc = repr(e) + '\n' + traceback.format_exc()
        exc = re.sub('"','\\"',exc)
        status = "failure"
        message += exc;
    finally:
        close_all(driver)

    return status, message, file_path

def get_mtn_stmts(username, password, country_code, storage_path, acc_prvdr_code, umtn_start_day = None, umtn_end_day = None):

    if(umtn_start_day != None and umtn_end_day != None):
        if acc_prvdr_code == "UMTN":
            start_day, end_day  = umtn_start_day, umtn_end_day 
    else:
        today = date.today()
        start_day, end_day  = get_dates_of_month(today)
    driver = initialize(storage_path)

    try:
        status, message, file_path = "", "", ""

        mtn_login(username, password, country_code, time_zone, driver, acc_number, True)
        find_if_exists_by_xpath("//div/table/tbody/tr[contains(@class, 'v-selected')]/td/div/div[contains(@id, 'VIEW_ACCOUNT_LIST_TABLE_0_account.id')]").click()
        sleep(4)
        start_date_input = find_if_exists_by_xpath('//div[contains(@id, "VIEW_ACCOUNT_TRANSACTION_HISTORY_FIELD_FROM_DATE")]/input[contains(@class, "v-datefield-textfield")]')
        start_date_input.clear()   
        start_date_input.send_keys(start_day)
        end_date_input = find_if_exists_by_xpath("//div[contains(@id, 'VIEW_ACCOUNT_TRANSACTION_HISTORY_FIELD_TO_DATE')]/input[contains(@class, 'v-textfield')]")
        end_date_input.clear()
        end_date_input.send_keys(end_day)
        find_if_exists_by_xpath("//div[contains(@id,'VIEW_ACCOUNT_TRANSACTION_HISTORY_BUTTON_SEARCH')]").click()
        sleep(4)
        old_file_time, file_path = get_file(storage_path)
        find_if_exists_by_xpath("//div[contains(@id, 'VIEW_ACCOUNT_TRANSACTION_HISTORY_BUTTON_EXPORT')]").click()
        file_path = get_downloaded_file(storage_path, old_file_time)

        if(file_path == 'failed'):
            status, message = "failure", "file download failed"
        else:
            status, message = "success", "file download successfully"

    except Exception as e:

        exc = repr(e) + '\n' + traceback.format_exc()
        exc = re.sub('"','\\"',exc)
        status = "failure"
        message += exc;
    finally:
        close_all(driver)

    return status, message, file_path

def get_rbok_stmts(username, password, storage_path):

    today = date.today()
    start_day, end_day = start_end_day_date_format(today, '%m/%d/%Y')
    driver = initialize(storage_path)

    try:
        status, message, file_path = "", "", ""
        rbok_login(username, password, driver, acc_number, False) 
            
        find_if_exists_by_xpath('//div[contains(@class,"accounts-list-cont")]/div[contains(@class,"account-list")]/li').click()
        sleep(2)
        find_if_exists_by_xpath('//input[contains(@id, "start")]').send_keys(start_day)
        find_if_exists_by_xpath('//div[contains(@class, "generate-statement-headers")]/div[1]/div/div[contains(@class,"mydevices__header--content")]/div[2]/input[contains(@class,"sort-date")]').send_keys(end_day)
        find_if_exists_by_xpath('//div[contains(@class, "generate-statement-headers")]/div[2]/button').click()
        old_file_time, file_path = get_file(storage_path)
        find_if_exists_by_xpath('//div[contains(@class,"account-statement-dialog")]/div[contains(@class,"modal-content")]/div[contains(@class,"modal-body")]/div[contains(@class,"statement-header")]/div[contains(@class,"statement-header-bottom")]/div[contains(@class, "statement-header-bottom--item")]/img[1]').click()
        sleep(4)
        file_path = get_downloaded_file(storage_path, old_file_time)
        
        if(file_path == 'failed'):
            status, message = "failure", "file download failed"
        else:
            status, message = "success", "file download successfully"

    except Exception as e:
        exc = repr(e) + '\n' + traceback.format_exc()
        exc = re.sub('"','\\"',exc)
        status = "failure"
        message += exc;
    finally:
        close_all(driver)

    return status, message, file_path


def get_udfc_stmts(username, password, storage_path):

    today = date.today()
    start_day, end_day = start_end_day_date_format(today, '%d-%m-%Y')

    driver = initialize(storage_path)

    try:
        status, message, file_path = "", "", ""
        udfc_login(username, password, driver) 
        lists = get_txn_table(start_day, True,driver)
        txn_df = pd.DataFrame(lists)
        txn_df = txn_df.drop(columns=['ImageCommand', 'Custom', 'TransactionalAccountNumber','ValueDateTime', 'Currency','AccountTransactionTypeId', 'InputBranchId', 'DetailsLink'])
        file_path = str(storage_path) +'/UDFC_statement'+'_'+str(start_day)+'_'+str(end_day)+'.csv'
        txn_df.to_csv(file_path, index = False)
        file_exists = os.path.exists(file_path)
                
        if(file_exists):
            status, message = "success", "file download successfully"
        else:
            status, message = "failure", "file download failed"

    except Exception as e:
        exc = repr(e) + '\n' + traceback.format_exc()
        exc = re.sub('"','\\"',exc)
        status = "failure"
        message += exc;
    finally:
        close_all(driver)

    return status, message, file_path

def main(data):

    try:
        global acc_number, time_zone
        status, message, file_path, umtn_start_day, umtn_end_day = "initiated", "", "", "", ""

        username = data.get('username')
        password = data.get('password')
        country_code = data.get('country_code')
        acc_number = data.get('acc_number');
        acc_prvdr_code = data.get('acc_prvdr_code')
        storage_path = data.get('storage_path')
        umtn_start_day =  data.get('start_day')
        umtn_end_day = data.get('end_day')
        zone = get_time_zone(country_code)
        time_zone = timezone(zone)

        if acc_prvdr_code == 'RMTN':
            status, message, file_path = get_mtn_stmts(username, password, country_code, storage_path, acc_prvdr_code)
        elif acc_prvdr_code == 'UMTN':
            status, message, file_path = get_mtn_stmts(username, password, country_code, storage_path, acc_prvdr_code, umtn_start_day, umtn_end_day)
        elif acc_prvdr_code ==  'UEZM':
            status, message, file_path = get_uezm_stmts(username, password, storage_path)
        elif acc_prvdr_code == 'RBOK':
            status, message, file_path = get_rbok_stmts(username, password, storage_path)
        elif acc_prvdr_code == 'UDFC':
            status, message, file_path = get_udfc_stmts(username, password, storage_path)
            
    except Exception as e:
        exc = repr(e) + '\n' + traceback.format_exc()
        exc = re.sub('"','\\"',exc)
        status = "failure"
        message += exc;

    return status, message, file_path

data = json.loads(sys.argv[1])
# logging.basicConfig(filename="/home/oem/Documents/PROJECTS/new_flow/flow-api/storage/logs/STMT_import_log.log",
#                             filemode="a",
#                             format='%(asctime)s %(message)s',
#                             level=logging.WARNING)
# logger = logging.getLogger()
# logger.warning(data)
status, message, file_path = main(data)
response = {"status": status, "message": message, "file_path": file_path}

print(json.dumps(response))



     
