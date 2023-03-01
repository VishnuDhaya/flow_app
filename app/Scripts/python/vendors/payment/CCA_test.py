#from seleniumrequests import Chrome
from selenium import webdriver
#from getpass import getpass
from selenium.webdriver.common.keys import Keys
from time import sleep
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait 
from selenium_helpers import wait_for_visibility,find_if_exists_by_xpath,find_if_exists_by_link_text,initialize,save_screenshot

import sys, time
import os

driver = None
logged_in = False
tb = ""
sub_path = 'UGA/payments/CCA'
screenshot_path = None

def login(username, password):
    global logged_in, screenshot_path
  
    driver.get("https://chapchap.co/app/#!/login")
    sleep(2)
    driver.find_element_by_name("username").send_keys(username)
    driver.find_element_by_name("password").send_keys(password)
    button = driver.find_element_by_xpath("//button[@id='login-submit']")
    driver.execute_script("arguments[0].click();", button)
    
    if(find_if_exists_by_link_text('Send Float')):
        logged_in = True
    else:
        screenshot_path = save_screenshot(base_path, sub_path , "login_failure", str(recipient))
        raise Exception('Unable to login')



def send_money(recipient, amount, username , password):
    result, txn_id, cust_name,message,txn_cust_name = "uninitiated", "","", "Error during initialize Please retry",""
    try:
        global driver, screenshot_path
        driver = initialize()
        result, message = "uninitiated", "Error during login. Please retry"
        
        login(username, password )
        result, message = "uninitiated", " Error during enter_payment_details. Please retry"
    except Exception as e:
        #print(e)        
        import traceback
        global tb
        tb = traceback.format_exc()
        #print(tb)
        result = 'failure' if result == 'uninitiated' else 'unknown'
        
        screenshot_path = save_screenshot(base_path, sub_path , result + '_exception', str(recipient))
        message += " : " + str(e)
        #message += " | " + tb

    return result,txn_id,message

recipient = sys.argv[1]
amount = sys.argv[2]
username = sys.argv[3]
password = sys.argv[4]

base_path = sys.argv[5]

result,txn_id,message = send_money(recipient, amount, username, password)
response = {'status' : result, 'txn_id' : txn_id,'message' : message, 'screenshot_path' : screenshot_path, 'traceback' : tb}

import json
print(json.dumps(response))



