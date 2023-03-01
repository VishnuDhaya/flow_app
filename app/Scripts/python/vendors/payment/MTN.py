from operator import sub
import sys
import json
import mysql.connector

from selenium_helpers import initialize, find_if_exists_by_xpath, save_screenshot
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.action_chains import ActionChains
from time import sleep
from datetime import datetime
import os
import sys
from pytz import timezone

curr_file_path = os.path.realpath(__file__)
parent_dir = curr_file_path.rsplit('/',2)[0]
sibling_dir = parent_dir+'/' + 'stmts'

sys.path.append(sibling_dir)

from MTN_stmt_import import get_new_otp

driver = None
logged_in = False
screenshot_path = None
tb = ""
no_of_times = 100

env = {}

def load_env():

    with open(".env") as f:
        for line in f:
            if line.startswith("#") or line.isspace():
                continue
            key, value = line.strip().split('=', 1)
            env[key] = value

load_env()

def connect_db():
    
    mydb = mysql.connector.connect(
		host = env['DB_HOST'],
		user = env['DB_USERNAME'],
		password = env['DB_PASSWORD'],
		database = env['DB_DATABASE']
    )
    mycursor = mydb.cursor()

    return mydb, mycursor

def fetch_last_row(agent_id):
    
    db, cursor = connect_db()
    sql = "SELECT id, otp FROM otps where status = 'received' and otp_type = 'disb_portal_verify' and entity_id = '{}' ORDER BY id DESC LIMIT 1".format(agent_id)
    cursor.execute(sql)
    myresult = cursor.fetchall()
    db.commit()
    return myresult

def get_new_row(last_row_id, agent_id):
    
    global no_of_times

    new_id, new_otp = None, None

    for i in range(no_of_times):

        sleep(1)
        new_row = fetch_last_row(agent_id)

        for x in new_row:
            temp_id = x[0]
            temp_otp = x[1]

        if(temp_id > last_row_id):
            return temp_id, temp_otp

    return new_id, new_otp

def update_status_to_taken(new_id):
    
    db, cursor = connect_db()
    sql = "UPDATE otps SET status = 'taken' WHERE id = {}".format(new_id)
    cursor.execute(sql)
    db.commit()

def update_status_to_login(new_id):
        
    db, cursor = connect_db()
    sql = "UPDATE otps SET status = 'verified' WHERE id = {} AND status = 'taken'".format(new_id)
    cursor.execute(sql)
    db.commit()

def get_last_row_id(agent_id):
    
    id = 0

    last_row = fetch_last_row(agent_id)

    for x in last_row:
        id = x[0]

    return id

def get_last_txn_id():

    find_if_exists_by_xpath('//div/table/tbody/tr[contains(@class, "v-selected")]/td/div/div[contains(@id, "VIEW_ACCOUNT_LIST_TABLE_0_account.id")]').click()

    txn_id = find_if_exists_by_xpath("//div[contains(@class,'v-table v-treetable')]/div[2]/div/table[contains(@class,'v-table-table')]/tbody/tr[1]/td[1]/div/div/span/span").text
    # txn_cust_name = find_if_exists_by_xpath("//div[contains(@class,'v-table v-treetable')]/div[2]/div/table[contains(@class,'v-table-table')]/tbody/tr[1]/td[13]/div").text

    return txn_id

def initiate_transfer(recipient):
    global screenshot_path

    status = "failure"

    finish_button = find_if_exists_by_xpath('//div[contains(@id,"VIEW_WIZARD_BUTTON_NEXT")]')

    if(finish_button):

        finish_button.click()
        main_message = find_if_exists_by_xpath('//div[contains(@class, "v-Notification tray v-Notification-tray")]/div[contains(@class, "popupContent")]/div[contains(@class,"gwt-HTML")]/h1')
        sub_message = find_if_exists_by_xpath('//div[contains(@class, "v-Notification tray v-Notification-tray")]/div[contains(@class, "popupContent")]/div[contains(@class,"gwt-HTML")]/p')

        if('Complete' in main_message.text):
            status, message = "success", sub_message.text

        elif('Complete' not in main_message.text):        
            status, message = "failure", main_message.text
            screenshot_path = save_screenshot(base_path, sub_path , 'failure', str(recipient))

        else:
            status, message = "unknown", "unknown"
            screenshot_path = save_screenshot(base_path, sub_path , 'unknown', str(recipient))

    else:
        message = "Unable to find finish button"
        screenshot_path = save_screenshot(base_path, sub_path , "login_failure", str(recipient))

    return status, message

def enter_payment_details(recipient, amount, identifier_type, country):

    global screenshot_path
    txn_id = ""

    span_no = 4 if country == 'UGA' else 3
    hoever_financial = find_if_exists_by_xpath("//div[contains(@id,'mainMenuBar')]/span[{}]".format(span_no))
    ActionChains(driver).move_to_element(hoever_financial).perform()

    hoever_transfer = find_if_exists_by_xpath('//div[contains(@class, "v-menubar-popup")]/div[contains(@class,"popupContent")]/div/span[1]')
    ActionChains(driver).move_to_element(hoever_transfer).perform()

    find_if_exists_by_xpath('/html/body/div[last()]/div[contains(@class,"popupContent")]/div/span[1]').click()

    payment_info_form = find_if_exists_by_xpath('//div[contains(@class, "v-window-wrap2")]')

    if(payment_info_form):

        find_if_exists_by_xpath('//input[contains(@id,"VIEW_ACCOUNT_HOLDER_TRANSFER_WIZARD_FIELD_AMOUNT")]').send_keys(amount)
        # continue_key = input()
        # find_if_exists_by_xpath('//input[contains(@id,"VIEW_ACCOUNT_HOLDER_TRANSFER_WIZARD_FIELD_NOTE")]').send_keys("Disbursed")
        if(identifier_type == 'alias'):
            find_if_exists_by_xpath('//div[contains(@id,"VIEW_ACCOUNT_HOLDER_TRANSFER_WIZARD_FIELD_RECEIVER+IDENTITYTYPE")]/div').click()
   
            sleep(1)

            find_if_exists_by_xpath('//div[contains(@id,"VAADIN_COMBOBOX_OPTIONLIST")]/div[contains(@class,"popupContent")]/div[2]/table/tbody/tr[2]/td').click()
            find_if_exists_by_xpath('//input[contains(@id,"VIEW_ACCOUNT_HOLDER_TRANSFER_WIZARD_FIELD_RECEIVER+IPN+ALIAS_NAME")]').send_keys(recipient)
        elif(identifier_type == 'msisdn'):
            isd_code_dp = find_if_exists_by_xpath('//div[contains(@id, "VIEW_ACCOUNT_HOLDER_TRANSFER_WIZARD_FIELD_RECEIVER+IPN+COUNTRYCODE")]/div')
            isd_code_dp.click()
            isd_code_field = find_if_exists_by_xpath('//div[contains(@id, "VIEW_ACCOUNT_HOLDER_TRANSFER_WIZARD_FIELD_RECEIVER+IPN+COUNTRYCODE")]/input')
            isd_code = get_isd_code(country)
            isd_code_field.send_keys(isd_code)
            for i in range(0,10):
                option = find_if_exists_by_xpath('//div[contains(@id,"VAADIN_COMBOBOX_OPTIONLIST")]/div[contains(@class,"popupContent")]/div[2]/table/tbody/tr[1]/td/span')
                if(option.get_attribute('innerHTML') == isd_code):
                    option.click()
                    break
                sleep(1)

            find_if_exists_by_xpath('//input[contains(@id, "VIEW_ACCOUNT_HOLDER_TRANSFER_WIZARD_FIELD_RECEIVER+IPN+NATIONALNUMBER")]').click()
            find_if_exists_by_xpath('//input[contains(@id, "VIEW_ACCOUNT_HOLDER_TRANSFER_WIZARD_FIELD_RECEIVER+IPN+NATIONALNUMBER")]').send_keys(recipient)

        find_if_exists_by_xpath('//div[contains(@id,"VIEW_WIZARD_BUTTON_NEXT")]').click()

        # main_error = find_if_exists_by_xpath('//div[contains(@class, "v-Notification error v-Notification-error")]/div[contains(@class, "popupContent")]/div[contains(@class,"gwt-HTML")]/h1')
        sub_error = find_if_exists_by_xpath('//div[contains(@class, "v-Notification error v-Notification-error")]/div[contains(@class, "popupContent")]/div[contains(@class,"gwt-HTML")]/p')

        if(not sub_error):
            status, message = "unknown", "Error during initiate_transfer"
            screenshot_path = save_screenshot(base_path, sub_path , "success", str(recipient))
            status, message = initiate_transfer(recipient)
            if(status == "success"):
                screenshot_path = save_screenshot(base_path, sub_path , "success", str(recipient))
                txn_id = get_last_txn_id()
            else:
                screenshot_path = save_screenshot(base_path, sub_path , "success", str(recipient))
                find_if_exists_by_xpath('//div[contains(@id,"VIEW_ACCOUNT_HOLDER_TRANSFER_WIZARD_window_close")]').click()
        else:
            screenshot_path = save_screenshot(base_path, sub_path , "failure", str(recipient))
            status, message = "failure", sub_error.text
            find_if_exists_by_xpath('//div[contains(@id,"VIEW_ACCOUNT_HOLDER_TRANSFER_WIZARD_window_close")]').click()
    else:
        screenshot_path = save_screenshot(base_path, sub_path , "unknown", str(recipient))
        status, message = 'failure', 'Form not found'

    return status, message, txn_id

def login(username, password, recipient, zone):

    global logged_in, new_id, new_otp, status, message
    url = get_url(country)
    time_zone = timezone(zone)
    driver.get(url)
    find_if_exists_by_xpath('//input[contains(@id,"USERNAME")]').send_keys(username)
    find_if_exists_by_xpath('//input[contains(@id,"PASSWORD")]').send_keys(password)
    today = datetime.now(time_zone)
    login_time = (today.strftime("%Y-%m-%d %H:%M:%S"))
    find_if_exists_by_xpath('//div[contains(@id,"LOGIN_BUTTON")]').click()

    # last_row_id = get_last_row_id(agent_id)

    if(find_if_exists_by_xpath('//div[contains(@id,"mainMenuBar")]/span[4]/span[2]', 6)):
        logged_in = True

    elif(find_if_exists_by_xpath("//div[contains(@class,'v-window')]/div[contains(@class,'popupContent')]")):

        new_id, new_otp = get_new_otp(login_time, agent_id)

        if(new_id):
            update_status_to_taken(new_id)
            otp = new_otp
            find_if_exists_by_xpath('//input[contains(@id, "VIEW_SECONDARY_LOGIN_MESSAGE")]').send_keys(otp)
            find_if_exists_by_xpath('//div[contains(@id, "VIEW_SECONDARY_LOGIN_BUTTON_OK")]').click()

            if(find_if_exists_by_xpath('//div[contains(@id,"mainMenuBar")]/span[4]/span[2]')):
                logged_in = True
                update_status_to_login(new_id)
            else:
                status, message = "failure", "Incorrect OTP or Timed out"
        else:
            status, message = "failure", "OTP Not Received"

    else:
        status, message = "failure", "Login failed: Authentication failed"
        screenshot_path = save_screenshot(base_path, sub_path , "login_failure", str(recipient))

    return status, message

def send_money(data, identifier_type, time_zone):
    global status, message, txn_id, agent_id, base_path, sub_path, country, logged_in
    recipient = data.get('to_acc_num')
    amount = data.get('amount')
    username = data.get('username')
    password = data.get('password')
    agent_id = data.get('agent_id')
    base_path = data.get('storage_path')
    sub_path = data.get('sub_path')
    country = data.get('country_code')
    status, txn_id, message = "uninitiated", "", "Error during initialize Please retry"

    try:
        global driver, screenshot_path
        driver = initialize()
        status, message = "uninitiated", "Error during login. Please retry"
        status, message = login(username, password, recipient, time_zone) 
        if(not status == "failure"):   
            status, message = "uninitiated", " Error during enter_payment_details. Please retry"
            status, message, txn_id  = enter_payment_details(recipient, amount, identifier_type, country)

    except Exception as e:    
        import traceback
        global tb
        tb = traceback.format_exc()
        status = 'failure' if status == 'uninitiated' else 'unknown'
        screenshot_path = save_screenshot(base_path, sub_path , status + '_exception', str(recipient))
        message += " : " + str(e)

    finally:
        close_all()

    response = {'status' : status, 'txn_id' : txn_id, 'message' : message, 'screenshot_path' : screenshot_path, 'traceback' : tb}
    print(json.dumps(response))
    return status, txn_id, message, screenshot_path, tb

def close_all():
    
    # if(logged_in):
    #     hover_element = find_if_exists_by_xpath('//div[contains(@id, "menubar")]')
    #     ActionChains(driver).move_to_element(hover_element).perform()
    #     find_if_exists_by_xpath('//div[contains(@class, "v-menubar-popup")]/div/div/span[3]').click()

    if(driver):
        driver.close()
        driver.quit()

def get_url(country):
    if country == 'UGA':
        return 'https://mobilemoneyreadonly.mtn.co.ug/partner/'
    elif country == 'RWA':
        return 'https://partner.mtn.co.rw/partner/'


def get_isd_code(country):
    if country == 'UGA':
        return '256'
    elif country == 'RWA':
        return '250'
