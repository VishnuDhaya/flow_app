import sys
import json

from time import sleep
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait 
from selenium_helpers import find_if_exists_by_id, find_if_exists_by_name, wait_for_visibility, find_if_exists_by_xpath, find_if_exists_by_link_text, initialize, save_screenshot

#"772656752"
# sudo pip install selenium
# sudo pip install chromedriver-binary
#curl https://intoli.com/install-google-chrome.sh | bash


#chrome --headless --disable-gpu --remote-debugging-port=9222 https://www.chromestatus.com   # URL to open. Defaults to about:blank.
#sudo chmod +rwx chromedriver
#sudo mv /usr/bin/google-chrome-stable /usr/bin/google-chrome
#cp /usr/local/lib/python3.6/site-packages/chromedriver_binary/chromedriver /usr/bin/chromedriver
'''
https://medium.com/@praneeth.jm/running-chromedriver-and-selenium-in-python-on-an-aws-ec2-instance-2fb4ad633bb5
https://devopsqa.wordpress.com/2018/03/08/install-google-chrome-and-chromedriver-in-amazon-linux-machine/
https://krbnite.github.io/Driving-Headless-Chrome-with-Selenium-on-AWS-EC2/
https://intoli.com/blog/installing-google-chrome-on-centos/
https://medium.com/@davidkadlec/installing-google-chrome-on-amazon-linux-ec2-d1cb6aa37f28
https://medium.com/mockingbot/run-puppeteer-chrome-headless-on-ec2-amazon-linux-ami-6c9c6a17bee6

'''
driver = None
logged_in = False
tb = ""
sub_path = 'UGA/payments/CCA'
screenshot_path = None

def login(username, password):

    global logged_in, screenshot_path
  
    driver.get("https://chapchap.co/app/#!/login")

    sleep(1)

    find_if_exists_by_xpath("//input[contains(@name, 'username')]").send_keys(username)
    find_if_exists_by_xpath("//input[contains(@name, 'password')]").send_keys(password)
    # driver.find_element_by_name("username").send_keys(username)
    # driver.find_element_by_name("password").send_keys(password)
    find_if_exists_by_xpath("//button[@id = 'login-submit']").click()
    # button = driver.find_element_by_xpath("//button[@id='login-submit']")
    # driver.execute_script("arguments[0].click();", button)
    
    if(find_if_exists_by_link_text('Send Float')):
        logged_in = True

    else:
        screenshot_path = save_screenshot(base_path, sub_path , "login_failure", str(recipient))
        raise Exception('Unable to login')


def enter_payment_details(recipient, amount): 

    find_if_exists_by_link_text('Send Float').click()
    
    if(find_if_exists_by_xpath("//input[contains(@name, 'phone')]")):

        find_if_exists_by_name("phone").send_keys(recipient)
        find_if_exists_by_name("amount").send_keys(amount)
        # driver.find_element_by_name("phone").send_keys(recipient)
        # driver.find_element_by_name("amount").send_keys(amount)
        #error = find_if_exists_by_xpath("//form[@name = 'voucherForm']/div[contains(@ng-class, 'phone')]/div/span[@aria-hidden = 'false']")

        submit_btn = find_if_exists_by_id("submit-button")

        if(submit_btn.is_enabled()):
            submit_btn.click()
            sleep(1)
        else:
            error = find_if_exists_by_xpath("//form[@name = 'voucherForm']/div[contains(@ng-class, 'phone')]/div/span[@aria-hidden = 'false']")
            screenshot_path = save_screenshot(base_path, sub_path , "login_failure", str(recipient))
            if(error):
                raise Exception(error.text)
            else:
                raise Exception('Unknown Error')

        return amount
    
    else:
        screenshot_path = save_screenshot(base_path, sub_path , "login_failure", str(recipient))
        raise Exception('Unable to login')


def initiate_transfer():

    global screenshot_path
    modal_body = "//div[@class='modal-body']/div[@ng-show='{}']"
    result, message = "failure", ""
 
    wait = WebDriverWait(driver, 10)
    wait.until(EC.invisibility_of_element_located((By.XPATH, modal_body.format('loading'))))
    # find_if_exists_by_xpath(modal_body.format('loading'), 10)

    summary_element = driver.find_element_by_xpath(modal_body.format('initSuccess'))
    fail_element = driver.find_element_by_xpath(modal_body.format('callFail')) 

    cust_name = None

    if(summary_element.get_attribute('aria-hidden') == 'false'):

        data = summary_element.find_element_by_xpath('dl/dd')   
        cust_name = data.text
        cust_name = cust_name.lower()
        
        find_if_exists_by_xpath("//div[@class='modal-footer']/button[contains(@class,'btn-success')]").click()       
        #sleep(1)
        wait = WebDriverWait(driver, 10)
        wait.until(EC.invisibility_of_element_located((By.XPATH, modal_body.format('loading'))))
        # find_if_exists_by_xpath(modal_body.format('loading'), 10)

        success_element = driver.find_element_by_xpath(modal_body.format('callSuccess'))
        fail_element = driver.find_element_by_xpath(modal_body.format('callFail'))


        if(success_element.get_attribute('aria-hidden') == 'false' and fail_element.get_attribute('aria-hidden') == 'true'):
            success_element.find_element_by_xpath("div[contains(@class,'alert-success')]")
            result, message = "success" , success_element.text

        elif(fail_element.get_attribute('aria-hidden') == 'false'):
            result, message = "failure", fail_element.text
            screenshot_path = save_screenshot(base_path, sub_path , "failure1", str(recipient))

        else:
            result, message = "unknown", "unknown"
            screenshot_path = save_screenshot(base_path, sub_path , "unknown1", str(recipient))
            
    elif(fail_element.get_attribute('aria-hidden') == 'false'):
        result, message = "failure",  fail_element.text
        screenshot_path = save_screenshot(base_path, sub_path , "failure2", str(recipient))
        
    else:
        result, message = "unknown", "unknown"
        screenshot_path = save_screenshot(base_path, sub_path , "unknown2", str(recipient))
        
    #success_element = driver.find_element_by_xpath("//div[@class='modal-body']/div[@ng-show='callSuccess']")
    return result, cust_name, message


def get_last_txn_id():

    txn_id, txn_cust_name, txn_amt = "", "", ""
    global screenshot_path
    find_if_exists_by_link_text("Transactions").click()

    screenshot_path = save_screenshot(base_path, sub_path , 'txn_id', str(recipient))       
    all_tr = find_if_exists_by_xpath("//table/tbody/tr[td/img[@src='img/out.png' and @aria-hidden='false']]")

    if(all_tr):
        txn_id = all_tr.find_element_by_xpath("td[5][@data-title-text='Transaction ID']").text
        descr = all_tr.find_element_by_xpath("td[4][@data-title-text='Description']").text
        txn_amt = all_tr.find_element_by_xpath("td[2][@data-title-text='Amount']").text
        txn_amt = txn_amt.replace(",","")
        txn_amt = int(txn_amt)
        txn_cust_name = descr.replace("Stock sent to ", "")  
        txn_cust_name = txn_cust_name.lower()

    return txn_id, txn_cust_name, txn_amt
    
def close_modal():

    close_xpath = "//div[@class='modal-header']/button[contains(@class,'close')]"
    modal_close = find_if_exists_by_xpath(close_xpath)
    if(modal_close and modal_close.is_displayed()):
        modal_close.click() 
            
def close_all():
    
    if(logged_in):
        close_modal()
        driver.find_element_by_css_selector("li.dropdown-nav").click()
        driver.find_element_by_link_text("Log Out").click()
    
    if(driver):
        driver.close()
        driver.quit()

def send_money(recipient, amount, username , password):

    result, txn_id, cust_name, message, txn_cust_name, txn_amt = "uninitiated", "", "", "Error during initialize Please retry","",""

    try:
        global driver, screenshot_path
        driver = initialize()
        result, message = "uninitiated", "Error during login. Please retry"
        
        login(username, password)
        result, message = "uninitiated", "Error during enter payment details. Please retry"
        
        amount = enter_payment_details(recipient, amount)
        result, message = "unknown", "Error during initiate transfer"

        result, cust_name, message = initiate_transfer()
        
        if(result == 'success'):
            screenshot_path = save_screenshot(base_path, sub_path , 'success', str(recipient))
            close_modal()
            txn_id, txn_cust_name, txn_amt = get_last_txn_id()
            message = ""
            if(txn_cust_name != cust_name):
                result = 'unknown'
                message += "Name :{} does not match with name :{} for txn_id : {}".format(txn_cust_name, cust_name, txn_id)
                txn_id = None

            if(txn_amt != amount):
                result = 'unknown'
                message += "amount :{} does not match with amount :{} for txn_id : {}".format(txn_amt, amount, txn_id)
                txn_id = None

    except Exception as e:
        import traceback
        global tb
        tb = traceback.format_exc()
        result = 'failure' if result == 'uninitiated' else 'unknown'        
        screenshot_path = save_screenshot(base_path, sub_path , result + '_exception', str(recipient))
        message += " : " + str(e)

    finally:
        close_all()

    return result, txn_id, message, txn_amt


data = json.loads(sys.argv[1])
recipient = data.get('to_acc_num')
amount = data.get('amount')
username = data.get('username')
password = data.get('password')
base_path = data.get('storage_path')

result,txn_id,message,txn_amt = send_money(recipient, amount, username, password)
response = {'status' : result, 'txn_id' : txn_id, 'amount' : txn_amt, 'message' : message, 'screenshot_path' : screenshot_path, 'traceback' : tb}

print(json.dumps(response))
