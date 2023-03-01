env = {}

import os
import glob
import re
#import pathlib

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