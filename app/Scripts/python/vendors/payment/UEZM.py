#from seleniumrequests import Chrome
from selenium import webdriver
#from getpass import getpass
from selenium.webdriver.common.keys import Keys
from time import sleep
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait 
import sys, time
import os
from selenium.common.exceptions import NoSuchElementException
from selenium_helpers import wait_for_visibility,find_if_exists_by_xpath,find_if_exists_by_link_text,initialize,save_screenshot,find_if_exists_by_id

driver = None
logged_in = False
tb = ""
screenshot_path = None
sub_path = 'UGA/payments/UEZM'

def login(username, password,staff_id):
	
	driver.get("https://ug.ezeemoney.biz/WebTerminal/Account/LogIn")
	driver.find_element_by_id('SCMerchantCode').send_keys(username)
	driver.find_element_by_id('StaffLoginID').send_keys(staff_id)
	driver.find_element_by_id('Password').send_keys(password)
	# Check if id exist for this button
	driver.find_element_by_xpath('//input[@type="submit"]').click()



def access(access_no):
	# # Check if Confirm Login Access comes
	global logged_in

	confirm_txt = "//form/ul/li[contains(@class, 'ui-first-child')]"
	submit_btn = "//form/ul/li[contains(@class, 'ui-last-child')]/div/input[@type='submit']"
	


	chk_txt = find_if_exists_by_xpath(confirm_txt, 45)
	chk_btn = find_if_exists_by_xpath(submit_btn, 45)
	
	if(chk_txt  and chk_btn):
		driver.find_element_by_id('TAGNo').send_keys(access_no)
	# Check if id exist for this but
		chk_btn.click()

		if(find_if_exists_by_xpath('//*[text()="Main Menu"]')):
			logged_in = True
		else:
			raise Exception('Unable to login')
	else:
		err = find_if_exists_by_xpath("//div[contains(@class, 'validation-summary-errors')]").text
		raise Exception('Please do not try again' , err)


	
def navigate():
	driver.get("https://ug.ezeemoney.biz/WebTerminal/F9/TRFSCtoSC")
	#driver.find_element_by_xpath('/html/body/div[3]/div[2]/ul/li[6]/a').click()
	#driver.find_element_by_xpath('/html/body/div[1]/div[2]/div/ul/li[6]').click()
	   
def enter_payment_details(recipient, amount):
	driver.find_element_by_id('SCCode').send_keys(recipient)
	driver.find_element_by_id('Amount').send_keys(amount)
	driver.find_element_by_id('btnNext').click()

def initiate_transfer():
	global screenshot_path
	status, message = "failure", None
	checkbtn = find_if_exists_by_id("btnConfirm")
	
	if(checkbtn):
		checkbtn.click()
		heading = find_if_exists_by_id('Result',60)
		if(heading):
			third_section = find_if_exists_by_xpath('//section[@id="Result"]/div[@id="ResultMerchant"]',15)
			first_section = find_if_exists_by_xpath('//section[@id="Result"]/div[1]/div',15)		
			if('OK' in first_section.text and 'block' in third_section.get_attribute('style') ):
				third_section = third_section.find_element_by_xpath('div')
				status, message = "success", third_section.text

			elif('OK' not in first_section.text):
						
				status, message = "failure", first_section.text
				screenshot_path = save_screenshot(base_path, sub_path , 'failure1', str(recipient))
			else:
				status, message = "unknown", "unknown"
				screenshot_path = save_screenshot(base_path, sub_path , 'unknown', str(recipient))
		else:
			message = "Unable to find heading"
	else:
		message = "Unable to find confirm button"
	return status, message, third_section

def get_last_txn_id(third_section):
	
	#txn_status = third_section.find_element_by_id('ResultMerchantReceipt').text
	txn_status = third_section.get_attribute('innerHTML')
	txn_status = txn_status.replace('<br>', '')

	txn_status = txn_status.split('\n')
	
	txn_id = txn_status[8]
	message_arr = txn_status[10:16]
	message = " ".join(message_arr)
    
	txn_id = txn_id.strip()
    
	txn_id = txn_id.split(':')
	txn_id = txn_id[1].strip()

	terminal_id = txn_status[6]
	terminal_id = terminal_id.strip()
    
	terminal_id = terminal_id.split(':')
	terminal_id = terminal_id[1].strip()

	return txn_id, terminal_id, message

def close_all():
 	if(driver and logged_in):
 		find_if_exists_by_link_text('Main Menu').click()
 		find_if_exists_by_link_text('Logout').click()
 	
 	if(driver):
 		driver.close()
 		driver.quit()

def send_money(recipient, amount, username, staff_id, password, access_no):
	try:
		status, txn_id,terminal_id, message = "uninitiated", "","", "Error during initialize Please retry"
		global driver, screenshot_path
		driver = initialize()
		status, message = "uninitiated", "Error during initialize. Please retry"
		
		login(username, password, staff_id)
		status, message = "uninitiated", "Error during login step 1."
		access(access_no)

		status, message = "uninitiated", "Error during login step 2."
		navigate()

		status, message = "uninitiated", " Error during enter_payment_details. Please retry"
        
		enter_payment_details(recipient, amount)
		
		status, message = "unknown", " Error during initiate_transfer."

		status, message, third_section = initiate_transfer()

		if(status == 'success'):
			screenshot_path = save_screenshot(base_path, sub_path , 'success', str(recipient))
			txn_id, terminal_id, message = get_last_txn_id(third_section)
            
	except Exception as e:
		global tb
		import traceback
        
		tb = traceback.format_exc()
		
		status = 'failure' if status == 'uninitiated' else 'unknown'
		
		screenshot_path = save_screenshot(base_path, sub_path , status + '_exception', str(recipient))
		message += " : " + str(e)
		
	finally:
		close_all()

	return status, txn_id,terminal_id, message
	


import json

data = json.loads(sys.argv[1])
recipient = data.get('to_acc_num')
amount = data.get('amount')
username = data.get('username')
staff_id = data.get('staff_id')
password = data.get('password_disb')
access_no = data.get('access_no')
base_path = data.get('storage_path')

status, txn_id, terminal_id,message = send_money(recipient, amount, username, staff_id, password, access_no)
response = {'status' : status, 'txn_id' : txn_id,'terminal_id' : terminal_id, 'message' : message, 'screenshot_path' : screenshot_path, 'traceback' : tb}
print(json.dumps(response))
