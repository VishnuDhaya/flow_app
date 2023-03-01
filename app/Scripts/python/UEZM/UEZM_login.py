from selenium_helpers import initialize, find_if_exists_by_id

global driver
driver = initialize()

def login(username,password,link):

    driver.get(link)
    find_if_exists_by_id('txtUserID').send_keys(username)
    find_if_exists_by_id('txtPassword').send_keys(password)	
    find_if_exists_by_id('btnLogin').click()
