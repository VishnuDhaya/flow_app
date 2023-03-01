import pandas as pd
import re
import mysql.connector
import json

from datetime import datetime
from sqlalchemy import create_engine
from flow_common import db_str, load_env, env
from pytz import timezone
from numpy import double

load_env()


stmt_ngin = create_engine('mysql+mysqlconnector://' + db_str(), pool_size=10)

def connect_db():

    mydb = mysql.connector.connect(
		host = env['DB_HOST'],
		user = env['DB_USERNAME'],
		password = env['DB_PASSWORD'],
		database = env['DB_DATABASE']
    )
    mycursor = mydb.cursor()

    return mydb, mycursor

def isnum(value):
    
    if value == '':
        return False
    try:
        import math
        return not math.isnan(float(value))
		
    except:
        return True

def get_txn_type_n_amount(record):

    if record['amount'] > 0 :

        record['cr_amt'] = record['amount']
        record['dr_amt'] = 0

        return 'credit', record['cr_amt']

    elif record['amount'] < 0 :

        record['dr_amt'] = record['amount']
        record['cr_amt'] = 0

        return 'debit', record['dr_amt']

def parse_date(record):

    date = record['stmt_txn_date']

    if ('/' in date):
        return datetime.strptime(date, '%Y-%m-%d %I:%m:00')
    else: 
        raise Exception("")

def chk_txn_type(record):

    if(record['cr_amt'] == 0):
        return 'debit'
    elif(record['dr_amt'] == 0):
        return 'credit'

def check_record_exists(txn_id, txn_type):

    db, cursor = connect_db()
    sql = "SELECT id from account_stmts where stmt_txn_id = '{}' and stmt_txn_type = '{}' order by id desc limit 1".format(txn_id, txn_type)
    cursor.execute(sql)
    result = cursor.fetchall()

    return result

def insert_df(df):

    from sqlalchemy import exc
    num_rows = len(df)	

    for i in range(num_rows):
        try:
            result = check_record_exists(df.iloc[i]['stmt_txn_id'], df.iloc[i]['stmt_txn_type'])
            if len(result) == 0 :
                df.iloc[i:i+1].to_sql('account_stmts', con = stmt_ngin, if_exists='append', chunksize = 500, index = False)
        except exc.IntegrityError as e:
            err = e.orig.args
            if('Duplicate entry' in err[1]):
                pass
            else:
                raise Exception(err)

def to_num(amt):

    if isnum(amt):
        return abs(double(amt))
    else:
        return 0.00	

def get_cr_amt(amount):

    if amount > 0:
        return to_num(amount)
    return to_num(0)

def get_dr_amt(amount):

    if amount < 0:
        return to_num(amount)
    return to_num(0)


def main():

    try:

        status, message = "initiated", ""
        time_zone = timezone('Africa/Kampala')
        df = pd.read_excel("storage/data/UMTN Statements.xlsx")
        # df = pd.read_excel("/home/sakthiganesh/Documents/CCA Statements.xlsx")

        old_df = pd.DataFrame(df)
       
        df[['stmt_txn_type','amount']] = old_df.apply(get_txn_type_n_amount, axis=1, result_type="expand")

        df['dr_amt'] = df['amount'].apply(get_dr_amt)
        df['cr_amt'] = df['amount'].apply(get_cr_amt)

        df['balance'] = old_df['balance'].apply(to_num)
        df['stmt_txn_date'] = old_df['txn_date'].apply(lambda t: t.replace(second=0))
        df['import_id'] = 0
        
        df['acc_prvdr_code'] = "UMTN"
        
        df['country_code'] = 'UGA'
        df['source'] = 'stmt'

        df['created_at'] = datetime.now(time_zone).strftime("%Y-%m-%d %H:%M:%S")

        df = df.drop(columns=['txn_date', 'amount'])

        insert_df(df)
        status, message = "success", "Imported Successfully"
        # print(df)

    except Exception as e:

        import traceback
        exc = repr(e) + '\n' + traceback.format_exc()
        exc = re.sub('"','\\"',exc)
        status = 'failure'
        message += exc

    return status, message


status, message = main()

result = {'status' : status, 'message' : message}

print(json.dumps(result))