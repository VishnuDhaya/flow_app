from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import NoSuchElementException, TimeoutException 
from time import sleep
from bs4 import BeautifulSoup
# from sqlalchemy import create_engine
# import sys
# import requests
# import csv
# import pandas as pd
import os
# import pymysql
# import socket
# socket.getaddrinfo('192.168.0.14', 3306)
# pip install mysqlclient

# sudo apt install libmysqlclient-dev



# engine = create_engine('mysql://root@localhost/CCA_TXN' , pool_size=10)
# print(engine)

# engine = pymysql.connect("192.168.0.14:3306","karthick_","karth!k","flow_api" )

driver = None
logged_in = False

def wait_for_visibility(by, token, wait_sec = 7):
	wait = WebDriverWait(driver, wait_sec)
	wait.until(EC.visibility_of_element_located((by, token)))


def find_if_exists_by_xpath(xpath):
    try:
        wait_for_visibility(By.XPATH, xpath)
        return driver.find_element_by_xpath(xpath)
    except (NoSuchElementException, TimeoutException) as err:
        return None

def find_if_exists_by_link_text(link_text):
    try:
        wait_for_visibility(By.LINK_TEXT, link_text)
        return driver.find_element_by_link_text(link_text)
    except (NoSuchElementException, TimeoutException) as err:
        return None

def intialize():
	global driver
	chrome_options = Options()
	chrome_options.add_argument('--headless')
	chrome_options.add_argument('--no-sandbox')
	chrome_options.add_argument('--disable-dev-shm-usage')

	# chrome_driver = os.getcwd() +"\\chromedriver"
	driver = webdriver.Chrome('/usr/lib/python2.7/site-packages/chromedriver_binary/chromedriver', options = chrome_options)
	#driver = webdriver.Chrome('/usr/local/lib/python3.8/dist-packages/chromedriver_binary/chromedriver', chrome_options = chrome_options)

	# driver = webdriver.Chrome(chrome_options=chrome_options, executable_path=chrome_driver)
	# driver = webdriver.Chrome(ChromeDriverManager().install())
	# driver = webdriver.Chrome( executable_path = "//chromedriver")
	# driver.maximize_window() 
	driver.implicitly_wait(12)

def login(username,password):
	global logged_in
	driver.get("https://chapchap.co/app/#!/login")
	sleep(2);
	
	driver.find_element_by_name("username").send_keys(username)

	driver.find_element_by_name("password").send_keys(password)

	button = driver.find_element_by_xpath("//button[@id='login-submit']")
	driver.execute_script("arguments[0].click();", button)

	if(find_if_exists_by_link_text('Transactions')):
		logged_in =True


def get_txn_table():
	
	find_if_exists_by_link_text("Transactions").click()	
	
	
	btn = find_if_exists_by_xpath("//div[@ng-show = 'callSuccess']/div/div/div/div/button[4]")
	driver.execute_script("arguments[0].click();", btn)

	table_elmnt = driver.find_element_by_xpath("//div[@ng-show = 'callSuccess']/table[contains(@class ,'table')]")
	table_data = table_elmnt.get_attribute('outerHTML')
	
	# soup = BeautifulSoup(driver.page_source,'html')	
	# table_data = soup.find("table", {"class": "table"})
	
	return str(table_data)

	
def close_all():
    
    if(logged_in):
        driver.find_element_by_css_selector("li.dropdown-nav").click()
        driver.find_element_by_link_text("Log Out").click()
    
    if(driver):
        driver.close()
        driver.quit()

def trans_data(username,password):
	try:
		
		intialize()
		
		login(username,password)
		
		table = get_txn_table()
		

		table = table.replace('Stock sent from','<span style="color:green">&lt;- REPAYMENT<span>')
		table = table.replace('Stock sent to','<span style="color:red">DISBURSAL -&gt;<span>')
		# table = table.replace('<img aria-hidden="false" class="" height="25px" ng-show="row.out" src="img/out.png"/>','<span style="color:red">SENT -&gt;<span>')
		# table = table.replace('<img aria-hidden="false" class="" height="25px" ng-show="!row.out" src="img/in.png"/>', '<span style="color:green">&lt;- RECEIVED</span>')
		# table = table.replace('<img aria-hidden="true" class="ng-hide" height="25px" ng-show="!row.out" src="img/in.png"/>' ,'')
		# table = table.replace('<img aria-hidden="true" class="ng-hide" height="25px" ng-show="row.out" src="img/out.png"/>','')
	finally:
		close_all()

	return table
	
username = "703463210"
password = "3f54XDsqXXMjGaA"
# username = sys.argv[1]
# password = sys.argv[2]
     
table_data= trans_data(username,password)
print(table_data)