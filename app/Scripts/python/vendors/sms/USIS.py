import sys
import json
import os
from time import sleep

curr_file_path = os.path.realpath(__file__)
parent_dir = curr_file_path.rsplit('/',2)[0]
stmt_dir = parent_dir+'/'+'stmts'

sys.path.append(stmt_dir)

from selenium_helpers import initialize, find_if_exists_by_xpath
from pytz import timezone
from datetime import datetime


logged_in = False
driver = None

def login_n_get_balance(username, password):

    global logged_in
    status, message, balance = "uninitiated", "Error during login. Please retry", ""

    driver.get("https://simplysms.com/")

    find_if_exists_by_xpath('//a[contains(@id,"login")]').click()

    find_if_exists_by_xpath('//input[contains(@id,"loginusername")]').send_keys(username)
    find_if_exists_by_xpath('//input[contains(@id,"loginpass")]').send_keys(password)
    sleep(1)
    find_if_exists_by_xpath('//button[contains(@id,"submitlogin")]').click()

    if(find_if_exists_by_xpath("//span[contains(@id,'ccredit')]")):

        logged_in = True
        balance = find_if_exists_by_xpath("//span[contains(@id,'ccredit')]").text
        status = "success" 

    else:

        status, message = "failure", "Unable to Login"

    return status, message, balance


def close_all():
    
    if(logged_in):
        find_if_exists_by_xpath('//div[contains(@id,"personal-info-space")]/a[3]').click()

    if(driver):
        driver.close()
        driver.quit()

def get_balance(username, password):

    status, message, balance, exc = "uninitiated", "Error during initialize", "", ""

    try:
        global driver
        driver = initialize()

        status, message, balance = login_n_get_balance(username, password)

        status, message = "success", ""

    except Exception as e:
        import traceback, re
        exc = repr(e) + '\n' + traceback.format_exc()
        exc = re.sub('"','\\"',exc)
        status = 'failure'

    finally:
        close_all()

    return status, message, balance, exc 


if __name__ == '__main__':

    data = json.loads(sys.argv[1])

    username = data.get('username')
    password = data.get('password')

    status, message, balance, tb = get_balance(username, password)

    response = {'status' : status, 'message' : message, 'balance': balance, 'traceback' : tb}

    print(json.dumps(response))
