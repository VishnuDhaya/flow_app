import pandas as pd
import re
import sys

from datetime import datetime
from sqlalchemy import create_engine
from flow_common import db_str, load_env, env
from pytz import timezone

load_env()


stmt_ngin = create_engine('mysql+mysqlconnector://' + db_str(), pool_size=10)

def isnum(value):
    
    if value == '':
        return False
    try:
        import math
        return not math.isnan(float(value))
		
    except:
        return True

def get_cr_amt(amt):

    if isnum(amt) and amt > 0:
        return abs(float(amt))
    else:
        return 0.0	

def get_dr_amt(amt):

    if isnum(amt) and amt < 0:
        return abs(float(amt))
    else:
        return 0.0	

def parse_date(record):

    return pd.to_datetime(record['time'], unit='s')


def chk_txn_type(record):

    if(record['cr_amt'] == 0):
        return 'debit'
    elif(record['dr_amt'] == 0):
        return 'credit'

def insert_df(df):

    from sqlalchemy import exc
    num_rows = len(df)	

    txn_df = df.drop(columns=['amount', 'time'])

    for i in range(num_rows):
        try:
            txn_df.iloc[i:i+1].to_sql('account_stmts', con = stmt_ngin, if_exists='append', chunksize = 500, index = False)
        except exc.IntegrityError as e:
            err = e.orig.args
            if('Duplicate entry' in err[1]):
                pass
            else:
                raise(err)	

def main():

    try:

        time_zone = timezone('Africa/Kampala')
        df = pd.read_excel("storage/data/CCA Statements.xlsx")
        # df = pd.read_excel("/home/sakthiganesh/Documents/CCA Statements.xlsx")

        old_df = pd.DataFrame(df)

        df = old_df.drop(columns=['account_type', 'account_number', 'service_provider'])

        df.rename(columns={"transaction_id":'stmt_txn_id',
                            "transaction_type": 'descr',
						    },inplace=True)
       
        df['dr_amt'] = df['amount'].apply(get_dr_amt)
        df['cr_amt'] = df['amount'].apply(get_cr_amt)
        # df['balance'] = df['balance'].apply(to_num)
        df['stmt_txn_date'] = df.apply(parse_date, axis=1)
        df['import_id'] = 0
        df['account_id'] = 1783
        df['acc_prvdr_code'] = "CCA"
        df['network_prvdr_code'] = "CCA"
        df['acc_number'] = "0703463210"
        df['stmt_txn_type'] = df.apply(chk_txn_type, axis=1)
        df['country_code'] = 'UGA'
        # df['created_at'] = datetime.now(time_zone).strftime("%Y-%m-%d %H:%M:%S")

        insert_df(df)
        print(df)

    except Exception as e:

        print(e)


main()