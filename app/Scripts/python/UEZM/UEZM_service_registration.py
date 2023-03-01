import sys
import json

from selenium_helpers import find_if_exists_by_link_text,find_if_exists_by_id, find_if_exists_by_xpath
from UEZM_login import login
from UEZM_login import driver
from selenium.webdriver.support.select import Select

tb = ""
logged_in = False

data = json.loads(sys.argv[1])
locals().update(data) 

abbr_name = data['UEZM']['UEZM_MainContent_txtAbbreviationName']['value']
cmp_reg_no = data['UEZM']['UEZM_MainContent_txtCompanyRegistrationNo']['value']
business_nature = data['UEZM']['UEZM_MainContent_ddlNatureOfBusiness']['value']
operated_by = data['UEZM']['UEZM_MainContent_ddOperatedBy']['value']
recruiter_id = data['UEZM']['UEZM_MainContent_txtRecruiterID']['value']
zone = data['UEZM']['UEZM_MainContent_ddlZone']['value']

def submit_form():
    	
	driver.execute_script("document.getElementById('MainContent_txtcompanyname').value='"+biz_name+"'")
	driver.execute_script("document.getElementById('MainContent_txtAbbreviationName').value='"+abbr_name+"'")
	driver.execute_script("document.getElementById('MainContent_txtCompanyRegistrationNo').value='"+cmp_reg_no+"'")
	driver.execute_script("document.getElementById('MainContent_txtAddress').value='"+ho_address+"'")
	driver.execute_script("document.getElementById('MainContent_txtbusinessphone').value='"+business_ph+"'")
	Select(find_if_exists_by_id("MainContent_ddlRegion")).select_by_value(region)
	Select(find_if_exists_by_id("MainContent_ddlNatureOfBusiness")).select_by_value(business_nature)
	Select(find_if_exists_by_id("MainContent_ddOperatedBy")).select_by_value(operated_by)
	driver.execute_script("document.getElementById('MainContent_txtBusinessLocation').value='"+business_place+"'")
	driver.execute_script("document.getElementById('MainContent_txtVillageLocation').value='"+village_location+"'")
	driver.execute_script("document.getElementById('MainContent_txtLC1').value='"+lc1+"'")
	driver.execute_script("document.getElementById('MainContent_txtDistrict').value='"+district+"'")
	Select(find_if_exists_by_id("MainContent_ddGender")).select_by_value(gender)
	driver.execute_script("document.getElementById('MainContent_txtname').value='"+name+"'")
	driver.execute_script("document.getElementById('MainContent_txtic').value='"+national_id+"'")
	driver.execute_script("document.getElementById('MainContent_txtNotificationPhone').value='"+notification_ph+"'")
	driver.execute_script("document.getElementById('MainContent_txtemail').value='"+email+"'")
	Select(find_if_exists_by_id('MainContent_ddlBankCode')).select_by_value(bank_code)
	driver.execute_script("document.getElementById('MainContent_txtBankAccNo').value='"+str(bank_acc_num)+"'")
	driver.execute_script("document.getElementById('MainContent_txtBankAccName').value='"+bank_acc_name+"'")
	Select(find_if_exists_by_id('MainContent_ddWallet')).select_by_value(wallet_type)
	driver.execute_script("document.getElementById('MainContent_txtRecruiterID').value='"+str(recruiter_id)+"'")
	Select(find_if_exists_by_id('MainContent_ddlZone')).select_by_value(zone)

	find_if_exists_by_id('MainContent_btnSubmit').click()

	alert_obj = driver.switch_to.alert
	alert_obj.accept()

def register_service_center():

	global logged_in

	if(find_if_exists_by_link_text('Service Center Maintenance')):

		logged_in = True
		find_if_exists_by_link_text('Service Center Maintenance').click()
		find_if_exists_by_link_text('Service Center Registration').click()

		form_header = find_if_exists_by_xpath('//div[@id="MainContent_pnlDisplay"]/table[2]/tbody/tr[1]/td[contains(@class,"labelHeader")]')

		if("Company Registration Information" in form_header.text):
			submit_form()
			merchant_reg_status = find_if_exists_by_id('MainContent_lblMessage').text

			if("Merchant Code" in merchant_reg_status):
				sc_code = merchant_reg_status.split(' ')
				sc_code = sc_code[-1]
				sc_code = sc_code.split('.')
				status = "success"
				message = merchant_reg_status
				sc_code = sc_code[0]
			else:
				status = "failure"
				message = merchant_reg_status
				sc_code = ""
		else:
			status, message, sc_code = "failure", "Form not found", ""
	else:
		status, message, sc_code = "failure", "Login failed", ""

	return status, message, sc_code

def close_all():

	if(logged_in):
		find_if_exists_by_id('btnLogout').click()

	if(driver):
		driver.close()
		driver.quit()

def create_sc_code(username,password):

	status = 'failure'
	message = None
	sc_code = ''

	try:
		login(username,password,link)
		status, message, sc_code = register_service_center()

	except Exception as e:   

		import traceback
		global tb
		tb = traceback.format_exc()
		message += " : " + str(e)
    
	finally:
		close_all()

	return status, message, sc_code


link = "https://ug.ezeemoney.biz/Agent"
status, message, sc_code = create_sc_code(username,password)
response = {'status' : status, 'message' : message, 'sc_code' : sc_code}
print(json.dumps(response))


#0703463210
#ABC@#abc123456