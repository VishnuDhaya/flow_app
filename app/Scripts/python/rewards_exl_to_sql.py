import pandas as pd
import sqlalchemy
#import pymysql
from flow_common import db_str, load_env

load_env()

df = pd.read_excel("Scripts/python/cashback.xlsx", skiprows=range(1, 4), na_filter=False)

df = pd.DataFrame(df)
df.drop(df.columns[[5,6,9,11]], axis =1, inplace = True)

df.columns = ['cust_id','mobile_num', 'dp_cust_id', 'biz_name','recent_ontime_fas','cashback','status','transfer_status']
df['dp_code'] = 'null'
df['txn_id'] = 'null'

df.drop(df.tail(180).index,
        inplace = True)
print(df)

database_username = 'root'
database_password = '123456'
database_ip       = '127.0.0.1:3306'
database_name     = 'flow_api'
# db_conn = sqlalchemy.create_engine('mysql+pymysql://{0}:{1}@{2}/{3}'.
#                                                format(database_username, database_password, 
#                                                       database_ip, database_name))
db_conn = sqlalchemy.create_engine('mysql+mysqlconnector://' + db_str('STMT'))
df.to_sql(con=db_conn, name='cash_backs', if_exists='append', index=None)

for index,row in df.iterrows():
       try:
              cust_id = row['cust_id']
              
              dp_code_query = "select data_prvdr_code from borrowers where cust_id ='{}' ".format(cust_id)
              
              dp_code = pd.read_sql_query(dp_code_query , con = db_conn)
              
              update_query = "update cash_backs set dp_code ='{}' where cust_id = '{}'".format(dp_code['data_prvdr_code'][0],cust_id)
              
              with db_conn.begin() as conn:
                     conn.execute(update_query)
                     
              if(index > 303 ):
	                     break
              
       except Exception as e:
              raise e
