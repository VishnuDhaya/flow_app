import sys
import json

from selenium_helpers import initialize, find_if_exists_by_xpath, save_screenshot
from selenium.webdriver.common.action_chains import ActionChains
from time import sleep
from selenium.webdriver.common.keys import Keys
from MTN import load_env, env, connect_db, get_last_row_id, get_new_row, update_status_to_taken

driver = None
logged_in = False
screenshot_path = ""
base_path = ""
tb = ""
sub_path = 'RWA/payments/RBOK'
no_of_times = 100

load_env()

def update_status_to_verify(new_id):
        
    db, cursor = connect_db()
    sql = "UPDATE otps SET status = 'verified' WHERE id = {} AND status = 'taken'".format(new_id)
    cursor.execute(sql)
    db.commit()

def submit_otp(otp, driver):

    digits = [int(digit) for digit in str(otp)]

    for i in range(0,len(digits)):

        otp_inputs = find_if_exists_by_xpath('//div[contains(@class,"otp-fake-field")]/div['+str(i+1)+']')
        ActionChains(driver).click(otp_inputs).send_keys(digits[i]).perform()

    find_if_exists_by_xpath('//button[contains(@class, "otp-button")]').click()

def get_txn_id():

    txn_id = ""

    # find_if_exists_by_xpath('//div[contains(@class,"tabs top-tab-bar")]/ol[contains(@class,"tab-list")]/li[2]').click() # Home Tab
    find_if_exists_by_xpath('//div[contains(@class,"tabs top-tab-bar")]/ol[contains(@class,"tab-list")]/li[5]').click() # Transaction History Tab
    find_if_exists_by_xpath('//div[contains(@class,"menu-tab-bar")]/div[contains(@class,"tab-list-ctn")]/ol[contains(@class,"tab-list")]/li[2]').click() # Transfers Tab
    find_if_exists_by_xpath('//div[contains(@class,"transfers-history")]/ul/li[1]/div[contains(@class,"head-section")]').click()

    txn_id = find_if_exists_by_xpath('//div[contains(@class,"transaction-history-item--head")]/span[contains(@class,"ref-number")]/a').text # Ref Number
    find_if_exists_by_xpath('//div[contains(@class,"modal-content")]/div/div[1]/h2/span[contains(@class,"close-sign")]').click()  # Close the modal

    return txn_id  

def initiate_transfer(recipient,amount):

    global screenshot_path
    status,message,txn_id = "failure","Error during initiate transfer",""

    find_if_exists_by_xpath('//input[contains(@id, "floating-center-title")]').send_keys(amount)
    remarks_xpath = find_if_exists_by_xpath('//input[contains(@id, "transaction-description-text-field")]')

    remarks_xpath.send_keys(Keys.CONTROL + Keys.BACK_SPACE)
    remarks_xpath.send_keys(disbursal_id)

    find_if_exists_by_xpath('//div[contains(@class,"transaction-controls")]/button[contains(@type,"submit")]').click()

    confirm_pay_btn = find_if_exists_by_xpath('//div[contains(@class,"transfers-overview")]/div[2]/div/div[contains(@class,"transaction-controls")]/button[2]')

    if(confirm_pay_btn):

        save_screenshot(base_path, sub_path , "success", str(recipient))
        confirm_pay_btn.click()
        
        last_row_id = get_last_row_id(from_acc_num)

        new_id, new_otp = get_new_row(last_row_id, from_acc_num)

        if(new_id):
            update_status_to_taken(new_id)
            otp = new_otp
            submit_otp(otp, driver)

            success_message = find_if_exists_by_xpath('//div[contains(@class,"transfer-success")]/div/h3[contains(@class,"success-message--title")]')

            if(success_message):
                update_status_to_verify(new_id)
                screenshot_path = save_screenshot(base_path, sub_path , "success", str(recipient))
                # txn_id = get_txn_id()
                status, message = "success", success_message.text
            else:
                screenshot_path = save_screenshot(base_path, sub_path , "failure", str(recipient))
                invalid_otp_msg = find_if_exists_by_xpath('//div[contains(@class,"modal-content")]/div/div[2]/form/p[contains(@class,"otp-error-message")]')
                if(invalid_otp_msg):
                    status, message = "failure", invalid_otp_msg.text
                else:
                    error_message = find_if_exists_by_xpath('//div[contains(@class,"notification-modal--error")]/div/div/div[2]/p')
                    find_if_exists_by_xpath('//div[contains(@class,"notification-modal--error")]/div/div/div[2]/div[2]/button').click()
                    status, message = "failure", error_message.text
        else:
            status, message = "failure", "Transaction OTP Not Received"
    else:
        status, message = "failure", "Error during verifying transaction"

    return status, message, txn_id

def enter_payment_details(recipient, amount):

    global screenshot_path
    txn_id = ""

    find_if_exists_by_xpath('//div[contains(@class,"tabs top-tab-bar")]/ol[contains(@class,"tab-list")]/li[2]').click() # Payment & Transfer Tab
    find_if_exists_by_xpath('//div[contains(@class, "payments-transfers-nav--tabs")]/div[2]').click()                   # Bank Transfer Button
    find_if_exists_by_xpath('//div[contains(@class, "transfers-accounts-holder")]/div').click()                         # Select Account Holder Tab
    find_if_exists_by_xpath('//div[contains(@class, "transfer-to-other-selector")]/div/button[contains(@class,"new-button")]').click()

    beneficiary_modal = find_if_exists_by_xpath('//div[contains(@class, "modal-content")]/form/div/div[contains(@class,"verify-account-name-ctn")]')

    if(beneficiary_modal):

        find_if_exists_by_xpath('//div[contains(@class, "modal-content")]/form/div/div[contains(@class,"verify-account-name-ctn")]/div[1]/input').send_keys(recipient)
        find_if_exists_by_xpath('//div[contains(@class, "modal-content")]/form/div/div[contains(@class,"verify-account-name-ctn")]/div[2]/span').click()

        continue_btn = find_if_exists_by_xpath('//div[contains(@class,"modal-content")]/form/div[2]/div/button[contains(@class,"done-button")]')

        if(continue_btn):
            continue_btn.click()
            status, message,txn_id = initiate_transfer(recipient,amount)
        else:
            error_message = find_if_exists_by_xpath('//div[contains(@class,"notification-modal--error")]/div/div/div[2]/p')
            status, message = "failure", error_message.text
            screenshot_path = save_screenshot(base_path, sub_path , "failure", str(recipient))
            find_if_exists_by_xpath('//div[contains(@class,"notification-modal--error")]/div/div/div[2]/div[2]/button').click()
            find_if_exists_by_xpath('//div[contains(@class,"existing-contacts-modal")]/div/form/div/div/img').click()         
    else:
        screenshot_path = save_screenshot(base_path, sub_path , "failure", str(recipient))
        status, message = "failure", "Error during verification of beneficiary"

    return status, message, txn_id

def save_screen():

    global screenshot_path

    if(base_path):
        screenshot_path = save_screenshot(base_path, sub_path , "login_failure", str(recipient))

def login(username, password, driver, from_acc_num, is_disb_login = True):

    global logged_in, new_id, new_otp, status, message, screenshot_path
    status, message = "uninitiated", "Error during login. Please retry"

    driver.get("https://online.bk.rw/")
    find_if_exists_by_xpath('//input[contains(@id,"username-text")]').send_keys(username)
    find_if_exists_by_xpath('//input[contains(@id,"password-text")]').send_keys(password)
    find_if_exists_by_xpath('//button[contains(@type,"submit")]').click()

    if(find_if_exists_by_xpath("//div[contains(@class,'modal-content')]")):

        last_row_id = get_last_row_id(from_acc_num)
        new_id, new_otp = get_new_row(last_row_id, from_acc_num)

        if(new_id):
            update_status_to_taken(new_id)
            otp = new_otp
            submit_otp(otp, driver)
            if(find_if_exists_by_xpath('//div[contains(@class,"tabs top-tab-bar")]/ol/li[2]')):  # Home Page tab
                global logged_in
                logged_in = True
                update_status_to_verify(new_id)
            else:
                if(not is_disb_login):
                    raise Exception("Incorrect OTP")
                else:
                    status, message = "failure", "Invalid OTP"
                    screenshot_path = save_screenshot(base_path, sub_path , "login_failure", str(recipient))
        else:
            if(not is_disb_login):
                raise Exception("OTP Not Received")
            else:
                status, message = "failure", "OTP Not Received"         
                screenshot_path = save_screenshot(base_path, sub_path , "login_failure", str(recipient))
    else:
        if(not is_disb_login):
            raise Exception("Authentication failed")
        else:
            status, message = "failure", "Login failed: Authentication failed"
            screenshot_path = save_screenshot(base_path, sub_path , "login_failure", str(recipient))

    return status, message

def send_money(recipient, amount, username, password):

    global status, message, txn_id
    status, txn_id, message = "uninitiated", "", "Error during initialize Please retry"

    try:
        global driver, screenshot_path
        driver = initialize()
        status, message = login(username, password, driver, from_acc_num) 
        if(not status == "failure"):   
            status, message = "uninitiated", " Error during enter_payment_details. Please retry"
            status, message, txn_id  = enter_payment_details(recipient, amount)

    except Exception as e:    
        import traceback
        global tb
        tb = traceback.format_exc()
        status = 'failure' if status == 'uninitiated' else 'unknown'
        screenshot_path = save_screenshot(base_path, sub_path , status + '_exception', str(recipient))
        message += " : " + str(e)

    finally:
        close_all()

    return status, txn_id, message

def close_all():
    
    # if(logged_in):
    #     find_if_exists_by_xpath('//div[contains(@class,"nav-bar-right-lg")]/div[contains(@class,"caret-ctn")]/div/button').click()
    #     find_if_exists_by_xpath('//div[contains(@class,"nav-bar-right-lg")]/div[contains(@class,"caret-ctn")]/div/div/a[2]').click()

    if(driver):
        driver.close()
        driver.quit()

if __name__ == '__main__':
    data = json.loads(sys.argv[1])
    recipient = data.get('to_acc_num')
    amount = data.get('amount')
    username = data.get('username')
    password = data.get('password')
    from_acc_num = data.get('from_acc_num')
    base_path = data.get('storage_path')
    disbursal_id = data.get('disb_id')

    status, txn_id, message = send_money(recipient, amount, username, password)

    response = {'status' : status, 'txn_id' : txn_id, 'message' : message, 'screenshot_path' : screenshot_path, 'traceback' : tb}

    print(json.dumps(response))
