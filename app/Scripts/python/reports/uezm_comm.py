import pandas as pd
from sqlalchemy import create_engine
import re
import datetime as dt
from flow_common import db_str, load_env
from datetime import datetime
from dateutil.relativedelta import relativedelta

load_env()

db = create_engine('mysql+mysqlconnector://'+db_str('STMT'))
db = db.connect()


def count_txn_ids(list):
    str = ','.join(list)
    count = len(re.split(',|\||/',str))
    return count


def get_range():
    rs = pd.read_sql(sql = "select IFNULL(max(month),0) as month from commissions where acc_prvdr_code = 'UEZM' ", con = db)
    if rs['month'][0] == 0:
        date = dt.date(2019,1,1)
    else:
        date = dt.datetime.strptime(str(rs['month'][0]),"%Y%m") + relativedelta(months=1)

    now = dt.datetime.now()
    month_range = []
    while ((date.year*100)+date.month < (now.year*100)+now.month):
       month_range.append((date.year*100)+date.month)
       date = date + relativedelta(months=1)
    return month_range

def get_commission():
    month_range = get_range()
    for month in month_range:
        comm_charge = 1765 if month > 201910 else 500
        db.execute(f"set @month = '{month}'")

        query = "select txn_id from loans l, loan_txns t where l.loan_doc_id = t.loan_doc_id and acc_prvdr_code = 'UEZM' and EXTRACT(YEAR_MONTH FROM txn_date) = @month and txn_type = 'payment' and l.loan_purpose = 'float_advance' "
        df = pd.read_sql(sql=query, con=db)
        count = count_txn_ids(df['txn_id'].fillna('0'))
        total_paid = count * comm_charge
        if month == 201910:
            second_part_query = "select txn_id from loans l, loan_txns t where l.loan_doc_id = t.loan_doc_id and acc_prvdr_code = 'UEZM' and EXTRACT(YEAR_MONTH FROM txn_date) = @month and txn_date >= '2019-10-15' and txn_type = 'payment'"
            df = pd.read_sql(sql = second_part_query, con=db)
            count = count_txn_ids(df['txn_id'])
            total_paid += count * 1265

        record = {'month': [month], 'country_code': ['UGA'], 'acc_prvdr_code': ['UEZM'], 'total_paid': [total_paid]}
        record_df = pd.DataFrame(record)
        record_df.to_sql(con=db, name='commissions',index=id, if_exists='append')

get_commission()