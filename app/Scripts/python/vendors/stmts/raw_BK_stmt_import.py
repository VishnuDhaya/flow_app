import pandas as pd
import re
import sys

from datetime import datetime
from sqlalchemy import create_engine
from flow_common import db_str, load_env, env
load_env()
def isnum(value):
    
    if value == '':
        return False
    try:
        import math
        return not math.isnan(float(value))
		
    except:
        return True

def to_num(amt):

    if(type(amt) is str):
        amt = re.sub(",", '',(amt.strip('RWF')))  
    if isnum(amt):
        return abs(float(amt))
    else:
        return 0.0	

def parse_date(date, format):

    format = '%d/%m/%Y'
    date = (date['stmt_txn_date'])
    print(date)
    return date
    if ('/' in date):
        # date_time_obj =  datetime.strptime(date, format)	
        # return datetime.combine(date_time_obj, time(datetime.now(time_zone).hour, datetime.now(time_zone).minute))
        return datetime.strptime(date, format)
    else: 
        raise Exception("")


def chk_txn_type(record):

    if(record['cr_amt'] == 0):
        return 'debit'
    elif(record['dr_amt'] == 0):
        return 'credit'

stmt_ngin = create_engine('mysql+mysqlconnector://' + db_str(), pool_size=10)


months = ['April','May','June','July','August','September', 'October']

for month in months:

    df = pd.read_excel("storage/data/BK Statements.xlsx", month)  


    old_df = pd.DataFrame(df)

    df = old_df.drop(columns=['Unnamed: 1', 'Unnamed: 2', 'Unnamed: 3', 'Unnamed: 5', 'Unnamed: 6', 'Unnamed: 8', 'Unnamed: 11', 'Unnamed: 12', 'Unnamed: 13'])
    print(df)

    df['dr_amt'] = df['dr_amt'].apply(to_num)
    df['cr_amt'] = df['cr_amt'].apply(to_num)
    df['balance'] = df['balance'].apply(to_num)
    df['stmt_txn_date'] = df.apply(parse_date, format='%d/%m/%Y', axis=1)
    df['value_date'] = df.apply(parse_date, format='%d/%m/%Y', axis=1)
    df['import_id'] = 0
    df['account_id'] = 4182
    df['acc_prvdr_code'] = "RBOK"
    df['network_prvdr_code'] = "RBOK"
    df['acc_number'] = "100077653265"
    df['stmt_txn_type'] = df.apply(chk_txn_type, axis=1)
    df['country_code'] = 'RWA'
    df['source'] = 'stmt'

    print(df)



    from sqlalchemy import exc
    num_rows = len(df)	

    for i in range(num_rows):
        try:
            df.iloc[i:i+1].to_sql('account_stmts', con = stmt_ngin, if_exists='append', chunksize = 500, index = False)
        except exc.IntegrityError as e:
            err = e.orig.args
            if('Duplicate entry' in err[1]):
                pass
            else:
                raise(err)	
    print(df)