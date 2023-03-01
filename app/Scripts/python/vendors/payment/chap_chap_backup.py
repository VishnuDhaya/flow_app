from selenium import webdriver
#from getpass import getpass
from selenium.webdriver.common.keys import Keys
from time import sleep
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait 
import sys
#"772656752"
# sudo pip install selenium
# sudo pip install chromedriver-binary
# curl https://intoli.com/install-google-chrome.sh | bash
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

def login(username, password):
    driver.get("https://chapchap.co/app/#!/login")
    
    sleep(2)
    
    
    username_textbox = driver.find_element_by_name("username")
    username_textbox.send_keys(username)
    
    password_textbox = driver.find_element_by_name("password")
    password_textbox.send_keys(password)
    
    button = driver.find_element_by_xpath("//button[@id='login-submit']")
    driver.execute_script("arguments[0].click();", button)
    

def initialize():
    global driver
    chrome_options = Options()
    chrome_options.add_argument('--headless')
    chrome_options.add_argument('--no-sandbox')
    chrome_options.add_argument('--disable-dev-shm-usage')

    driver = webdriver.Chrome(chrome_options = chrome_options)
    driver.maximize_window() 
    driver.implicitly_wait(10) # seconds

def enter_payment_details(recipient, amount):
    driver.find_element_by_link_text("Send Float").click()
    sleep(1)
    
    driver.find_element_by_name("phone").send_keys(recipient)
    
    driver.find_element_by_name("amount").send_keys(amount)
    
    proceed = driver.find_element_by_id("submit-button")
    proceed.click()
    sleep(1)

def initiate_transfer():
    modal_body = "//div[@class='modal-body']/div[@ng-show='{}']"
    result, message = "failure", None
    wait = WebDriverWait(driver, 5)
    wait.until(EC.invisibility_of_element_located((By.XPATH, modal_body.format('loading'))))
    
    driver.save_screenshot("screenshot5.png")
    
    summary_element = driver.find_element_by_xpath(modal_body.format('initSuccess'))
    fail_element = driver.find_element_by_xpath(modal_body.format('callFail')) 
        
    spinner_element = driver.find_element_by_xpath(modal_body.format('loading'))

    if(summary_element.get_attribute('aria-hidden') == 'false'):
       
        data = summary_element.find_elements_by_xpath('dl/dd')
        driver.save_screenshot("summary.png")
        #for item in data:
            #print(item.text)
        confirm = driver.find_element_by_xpath("//div[@class='modal-footer']/button[contains(@class,'btn-success')]")
       
        confirm.click()
        sleep(1)
        wait = WebDriverWait(driver, 5)
        wait.until(EC.invisibility_of_element_located((By.XPATH, modal_body.format('loading'))))

        success_element = driver.find_element_by_xpath(modal_body.format('callSuccess'))
        fail_element = driver.find_element_by_xpath(modal_body.format('callFail'))
        if(success_element.get_attribute('aria-hidden') == 'false'):
            
            success_element.find_element_by_xpath("div[contains(@class,'alert-success')]")
            
            
            result, message = "success" , success_element.text
        elif(fail_element.get_attribute('aria-hidden') == 'false'):
           
            result, message = "failure", fail_element.text
        else:
            result, message = "unknown", "unknown"
            driver.save_screenshot("unknown.png")
    elif(fail_element.get_attribute('aria-hidden') == 'false'):
        
        result, message = "failure",  fail_element.text
    else:
        result, message = "unknown", "unknown"
        driver.save_screenshot("unknown.png")
    #success_element = driver.find_element_by_xpath("//div[@class='modal-body']/div[@ng-show='callSuccess']")
    return result, message

def close_all():
    if(driver):
        driver.find_element_by_xpath("//div[@class='modal-header']/button[contains(@class,'close')]").click()
        driver.find_element_by_css_selector("li.dropdown-nav").click()
        driver.find_element_by_link_text("Log Out").click()
        
        
        driver.close()

def send_money(recipient, amount, username = "759943918", password = "flowchap"):
    result, message = "uninitiated", "Error during initialize"
    try:
        initialize()
        result, message = "uninitiated", "Error during login"
        
        login(username, password)
        result, message = "uninitiated", " Error during enter_payment_details"

        enter_payment_details(recipient, amount)
        result, message = "unknown", "Error during initiate_transfer"

        result, message = initiate_transfer()

    except Exception as e:
        print(e)
        result = 'failure' if result == 'uninitiated' else 'unknown'
        
        result, message + " | " + str(e)
    finally:
        close_all()
    return result, message

recipient = sys.argv[1]
amount = sys.argv[2]
username = sys.argv[3]
password = sys.argv[4]

result, message = send_money(recipient, amount, username, password)

print(result + "|" +message)
