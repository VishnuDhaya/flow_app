from itertools import count
import pandas as pd
import datetime as dt
from dateutil.relativedelta import relativedelta
from flow_common import WRITTEN_OFF_STATUSES

def map_ap_to_country(db): 
    country_code_query = "Select country_code from markets where status = 'enabled' "
    country_code_data = pd.read_sql(country_code_query,  con = db)
    
    countries = {}
    for index, record in country_code_data.iterrows():
        country_code = record['country_code']
        acc_prvdr_query = f"select acc_prvdr_code from acc_providers where country_code = '{country_code}' and biz_account = 1"
        acc_prvdr_data = pd.read_sql(acc_prvdr_query, con = db)

        acc_prvdrs = []
        for index, acc_prvdr in acc_prvdr_data.iterrows():
            acc_prvdrs.append(acc_prvdr['acc_prvdr_code'])

        countries.update({country_code: acc_prvdrs})

    return countries

def get_table_fields(table_name, db):
    return pd.read_sql(f"desc {table_name}", db)['Field'].tolist()

def paid_after(date):
    return f"(date(paid_date) > '{date}' or paid_date is null)"

def value_of(variable, scope):
    try:
        return eval(variable, scope)
    except NameError:
        return None
    except Exception as e:
        raise Exception(e)


def get_currency_code(country_code, db):
    return extract_from_query(f"""select currency_code from markets where country_code = '{country_code}'""", db).get('currency_code')


def get_float_vend_products(db):
    query = "select id from loan_products where product_type = 'float_vending'"
    fv_df = pd.read_sql(sql = query, con = db)
    
    products = []
    for i,row in fv_df.iterrows():
        products.append(str(row['id']))
    float_vend_prods = "(" + ",".join(products) + ")"
    
    return float_vend_prods

def extract_from_query(query, db_connection, key_column = None):
    data_frame = pd.read_sql(query, con = db_connection)
    data_values = {}
    if(len(data_frame) == 0):
        return data_values
    if(key_column == None):
        row = data_frame.iloc[0]
        for column in data_frame.columns:
            data_values.update({column: row[column]})
    else:
        for index, row in data_frame.iterrows():
            key = row[key_column]
            values = {}
            for column in data_frame.columns:
                values.update({column: row[column]})
            data_values.update({key : values})
    return data_values


def get_month_forex(base, quote, month, db):

    print('get_month_forex')
    print(base)
    print(quote)
    print(month)
    if(base == quote):
        return 1
    crnt_date = dt.datetime.now()
    crnt_month = crnt_date.strftime("%Y%m")
    if str(month) == str(crnt_month):
        report_date = crnt_date
    else:
        report_date = dt.datetime.strptime(str(month), '%Y%m') + relativedelta(months=1, day=1)
    date_str = report_date.strftime('%Y-%m-%d')
    sql = f"select forex_rate from forex_rates where base='{base}' and quote='{quote}' and date(forex_date) = '{date_str}' order by id desc limit 1"
    print(sql)
    df = pd.read_sql(sql=sql, con=db)
    return df['forex_rate'][0]

def get_current_forex(base, quote, db):

    if(base == quote):
        return 1
    sql = f"select forex_rate from forex_rates where base='{base}' and quote='{quote}' order by id desc limit 1"
    df = pd.read_sql(sql=sql, con=db)
    print(df)
    return df['forex_rate'][0]

def get_forex_for_date(base, quote, date, db):

    if(base == quote):
        return 1
    forex_date = dt.datetime.strptime(str(date), "%Y-%m-%d") + dt.timedelta(1)  
    forex_date = forex_date.strftime("%Y-%m-%d")  
    sql = f"select forex_rate from forex_rates where base='{base}' and quote='{quote}' and date(forex_date) = '{forex_date}' order by id desc limit 1"
    df = pd.read_sql(sql=sql, con=db)
    print(df)
    return df['forex_rate'][0]



def map_fund_to_country(db):
    country_code_query = "Select country_code from markets where status = 'enabled' "
    country_code_data = pd.read_sql(country_code_query,  con = db)

    countries = {}
    for index, record in country_code_data.iterrows():
        country_code = record['country_code']
        fund_code_query = f"select fund_code from capital_funds where country_code = '{country_code}' and fund_type not in ('internal')"
        fund_code_data = pd.read_sql(fund_code_query, con = db)

        fund_codes = []
        for index, fund_code in fund_code_data.iterrows():
            fund_codes.append(fund_code['fund_code'])

        countries.update({country_code: fund_codes})

    return countries

def for_entity(country_code, acc_prvdr_code = None, report = False, alias = '', prefix = ""):
    sql = f""
    if country_code == "*":
        return sql
    sql += f" {prefix} "
    alias = alias if alias == '' else alias + '.'
    sql += f" {alias}country_code = '{country_code}' "
    if report:
        acc_prvdr_code = " is null" if acc_prvdr_code is None else f" = '{acc_prvdr_code}'"
        sql += f" and {alias}acc_prvdr_code {acc_prvdr_code} "
    elif acc_prvdr_code is not None:
        sql += f" and {alias}acc_prvdr_code = '{acc_prvdr_code}' "
    return sql



def get_written_off_loans(country_code, date, db):
    written_off_loans_query = f"""select loan_doc_id from loan_write_off 
                              where country_code = '{country_code}' and year < year(date_add('{date}', INTERVAL 1 MONTH))
                              and write_off_status in {WRITTEN_OFF_STATUSES}"""
    df = pd.read_sql(written_off_loans_query, db)

    writ_off_fa_ids = ""

    for index, record in df.iterrows():
        writ_off_fa_ids += f"'{record['loan_doc_id']}',"
    writ_off_fa_ids = writ_off_fa_ids.strip(',')
    return writ_off_fa_ids

def get_ignore_written_off_condn(country_code, date, db, alias = ""):
    written_off_fa_ids = get_written_off_loans(country_code, date, db)
    if written_off_fa_ids == "":
        return ""
    else:
        return f" and {alias}loan_doc_id not in ({written_off_fa_ids}) "

def insert_records(table, db, scope):
    fields = get_table_fields(table, db)
    fields.remove('id')
    records = {field: [value_of(field, scope)] for field in fields}   
    records = pd.DataFrame(records)
    records.to_sql(con=db, name=table, if_exists='append', index=False)