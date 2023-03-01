env = {}

import re
import os
import glob
import re
#import pathlib


def load_env():
	with open(".env") as f:
		for line in f:
			if line.startswith("#") or line.isspace():
				continue
			key, value = line.strip().split('=', 1)
			env[key] = value


def db_str():
	username = env['DB_USERNAME']
	password = env['DB_PASSWORD'].strip("'").strip('"')
	password = re.sub('@', '%40', password)
	host = env['DB_HOST']
	db_name = env['DB_DATABASE']

	return "{0}:{1}@{2}/{3}".format(username, password, host, db_name)


def db_read_only_str():
	username = env['DB_USERNAME']
	password = env['DB_PASSWORD'].strip("'").strip('"')
	password = re.sub('@', '%40', password)
	host = env['DB_HOST_XXX_REPLICA']
	db_name = env['DB_DATABASE']

	return "{0}:{1}@{2}/{3}".format(username, password, host, db_name)

def load_df(txn_df, db_con):    
    from sqlalchemy import exc
    num_rows = len(txn_df)	

    for i in range(num_rows):
        try:
            txn_df.iloc[i:i+1].to_sql('account_stmts', con = db_con, if_exists='append', chunksize = 500, index = False)
        except exc.IntegrityError as e:
            err = e.orig.args
            if('Duplicate entry' in err[1]):
                pass
            else:
                raise(err)	