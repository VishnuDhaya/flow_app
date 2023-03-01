env = {}

import re
import os
import glob
import re
#import pathlib

WRITE_OFF_STATUS = "('approved','partially_recovered','recovered')"
STATUSES_BEFORE_DISBURSAL = "('voided', 'hold', 'pending_disbursal', 'pending_mnl_dsbrsl')"
WRITTEN_OFF_STATUSES = "('approved','partially_recovered','recovered')"
TXN_TYPES = "('payment','disbursal')"
BAD_DEBT_CUTOFF_DAYS = 60

DISBURSED = f" status not in {STATUSES_BEFORE_DISBURSAL} "
NOT_WRITTEN_OFF = f"(write_off_status not in {WRITTEN_OFF_STATUSES} or write_off_status is null)"

AVG_RETAIL_TXNS_FUNDED_PER_FA = 340
AVG_RETAIL_CUSTS_PER_AGENT = 500
AVG_CUST_REVENUE_PER_MONTH_IN_USD = 105
CAPITAL_MULT_FACTOR_FOR_AGENT = 1.25


def load_env():
	with open("../.env") as f:
		for line in f:
			if line.startswith("#") or line.isspace():
				continue
			key, value = line.strip().split('=', 1)
			env[key] = value


def db_str(ds):
	username = env['DB_USERNAME']
	password = env['DB_PASSWORD'].strip("'").strip('"')
	password = re.sub('@', '%40', password)
	host = env['DB_HOST']
	db_name = env['DB_DATABASE']
	
	return "{0}:{1}@{2}/{3}".format(username, password, host, db_name)

def db_rep_str(ds):
	username = env['DB_USERNAME']
	password = env['DB_PASSWORD'].strip("'").strip('"')
	password = re.sub('@', '%40', password)
	host = env['DB_HOST']
	db_name = env['DB_DATABASE_XXX_REPORT']
	
	return "{0}:{1}@{2}/{3}".format(username, password, host, db_name)


def db_read_only_str(ds):
	username = env['DB_USERNAME']
	password = env['DB_PASSWORD'].strip("'").strip('"')
	password = re.sub('@', '%40', password)
	host = env['DB_HOST_XXX_REPLICA']
	db_name = env['DB_DATABASE']
	
	return "{0}:{1}@{2}/{3}".format(username, password, host, db_name)

'''def db_str(ds):
	username = env[ds+'_DB_USERNAME']
	password = env[ds+'_DB_PASSWORD']
	host = env[ds+'_DB_HOST']
	db_name = env[ds+'_DB_DATABASE']
	
	return "{0}:{1}@{2}/{3}".format(username, password, host, db_name)
'''
#from filehash import FileHash
import hashlib

def get_hash(file):
	md5_hash = hashlib.md5()
	with open(file, "rb") as f:
		for byth_block in iter(lambda: f.read(4096), b""):
			md5_hash.update(byth_block)
	
	return md5_hash.hexdigest()


def get_unique_files(path):

#path = "/home/sateesh/Documents/PROJECTS/python/2019-06-29/"

	duplicate_files = {}
	allFiles = []
	other_files = []
	files_map = {}
	for r, d, f in os.walk(path):
		for file in f:
			filename = os.path.join(r, file)
			if  file.endswith('.xls') or file.endswith('.xlsx') or file.endswith('.csv'):
			#if pathlib.Path(file_name).suffix  in ['xls', 'xlsx', 'csv']	:
				
				allFiles.append(filename)
				hash_key = get_hash(filename)
				if(files_map.get(hash_key) == None):
					files_map[hash_key] = filename
				else:	
					#print(filename + " is a duplicate with " + files_map[hash_key])
					duplicate_files[filename]	= files_map[hash_key]
			else:
				 	other_files.append(filename)	

	#allFiles = [f for f in glob.glob(path+"**/*.xls", recursive = True)]

	#allFiles.append([f for f in glob.glob(path+"**/*.xlsx", recursive= True)])

	#allFiles.append([f for f in glob.glob(path+"**/*.csv", recursive= True)])
	print("\nTotal Files : ")
	print(len(allFiles))

	print("\nDuplicate Files : ")
	print(len(duplicate_files))

	#print(duplicate_files)
	print("\nOther Files : ")
	print(len(other_files))
	print(other_files)

	print("\nUnique Files : ")
	print(len(files_map))

	#print(files_map.values())
	return files_map.values()

#allFiles = get_unique_files("/home/sateesh/Documents/PROJECTS/python/2019-06-29/")

def mail_data():
	username = env['MAIL_USERNAME']
	password = env['MAIL_PASSWORD']
	
	return [username, password]
