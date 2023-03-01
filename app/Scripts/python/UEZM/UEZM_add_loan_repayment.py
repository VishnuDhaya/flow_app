import sys
import json
import datetime


from selenium_helpers import find_if_exists_by_xpath,find_if_exists_by_link_text,find_if_exists_by_id
from UEZM_login import login
from UEZM_login import driver
from pytz import timezone

tb = ""
logged_in = False

def add_record():

	eastern = timezone('Africa/Kampala')
	today = datetime.datetime.now(eastern)
	today_formatted = today.strftime ('%d/%m/%Y') # format the date to dd/mm/yyyy

	sc_code = data.get('sc_code')
	total_amt = data.get('total_amt')
	deducted_amt = data.get('daily_deducted_amt')

	find_if_exists_by_id('MainContent_txtMerchantCode').send_keys(sc_code)
	find_if_exists_by_id('MainContent_txtTotalAmt').send_keys(total_amt)
	find_if_exists_by_id('MainContent_txtDeductAmt').send_keys(deducted_amt)
	find_if_exists_by_id('MainContent_txtStartDate').send_keys(today_formatted)
	
	find_if_exists_by_id('MainContent_btnSave').click()

def add_loan_repayment():

	global logged_in

	if(find_if_exists_by_link_text('SC Maintenance')):

		logged_in = True
		find_if_exists_by_link_text('SC Maintenance').click()
		find_if_exists_by_link_text('SC Terminal Loan Repayment').click()
		find_if_exists_by_id('MainContent_btnAddMain').click()
		add_record()
		
		record_status = find_if_exists_by_id('MainContent_lblMainMessage')

		if(record_status):
			if("added" in record_status.text):
				status = "success"
				message = record_status.text
				find_if_exists_by_xpath("//table[contains(@id, 'MainContent_gvMain')]//tr[last()]/td[1]/a").click()
				loan_id_xpath = find_if_exists_by_xpath("//table[contains(@id,'MainContent_gvData')]//tr[last()]/td[3]")
				loan_id = loan_id_xpath.text
			else:
				status, message, loan_id = "failure", record_status.text, ""
    			
		else:
			error_message = find_if_exists_by_id('MainContent_lblMessageAdd').text
			status, message, loan_id = "failure", error_message, ""

	else:
		status, message, loan_id = 'failure', 'Login failed', ''

	return status, message, loan_id

def close_all():
    
	if(logged_in):
		find_if_exists_by_id('btnLogout').click()
    
	if(driver):
		driver.close()
		driver.quit()

def loan_repayment_setting(username,password):

	status = "failure"
	message = None
	loan_id = ""

	try:
		login(username,password,link)
		status, message, loan_id = add_loan_repayment()

	except Exception as e:   

		import traceback
		global tb
		tb = traceback.format_exc()
		message += " : " + str(e)

	finally:
		close_all()

	return status, message, loan_id

link = "https://ug.ezeemoney.biz/WebAdmin"
data = json.loads(sys.argv[1])
username = data.get('username')
password = data.get('password')
     
status, message, loan_id = loan_repayment_setting(username,password)

response = {'status':status, 'message':message, 'loan_id':loan_id}
print(json.dumps(response))

#Geofrey
#Password1*