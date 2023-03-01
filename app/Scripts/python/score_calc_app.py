from __future__ import print_function
import mysql.connector
import numpy as np
import pandas as pd
import xlrd
import datetime
from sqlalchemy import create_engine
from flow_common import db_str
from flow_common import db_str, load_env, env, get_unique_files 
import sqlalchemy
from mysql_common import execute
import sys

now = datetime.datetime.now()
#print(now)
#run_id = now.strftime('%y%m%d%H%M')
load_env()
##print(db_str('APP'))

stmt_ngin = create_engine('mysql+mysqlconnector://' + db_str('STMT'))

app_ngin = create_engine('mysql+mysqlconnector://' + db_str('APP'))

def csf_normal_val(csf_type, value):
	val = df_csf_values[df_csf_values['csf_type'] == csf_type] [value >= df_csf_values['value_from']] [value < df_csf_values['value_to']]
	
	if(len(val.index) > 0):
		return val.iat[0,6]
	else:
		return 0


def scorecard_ft(record):
	
	five_day = csf_normal_val( '5_day_avg_roi', record['__5_day_avg_roi'])
	fifteen_day = csf_normal_val( '15_day_avg_roi', record['__15_day_avg_roi'])
	thirty_day = csf_normal_val('30_day_avg_roi', record['__30_day_avg_roi'])
	no_tx = csf_normal_val('30_day_avg_txns', record['__30_day_avg_txns'])

	record['5_day_avg_roi'] = five_day
	record['15_day_avg_roi'] = fifteen_day
	record['30_day_avg_roi'] = thirty_day
	record['30_day_avg_txns'] = no_tx
	#print(record)

	return record;

def format_date(date):
	f_date = date.strftime('%Y-%m-%d')
	return f_date 

def get_meta_df(dp_cust_id):

	sql = "select data_prvdr_cust_id, date(min(txn_date)) meta_txn_start_date, "\
			"date(max(txn_date)) meta_txn_end_date, count(distinct date(txn_date)) meta_txn_days, "\
			 "1+DATEDIFF(max(txn_date), min(txn_date)) meta_cal_days  from "+ table_name  +"\
			 where txn_date > "\
			  				"(select date_sub(date(max(txn_date)),  interval " + str(no_of_days-1) +" day) from "+ table_name +"\
			  				where data_prvdr_cust_id = "+ dp_cust_id + ") "\
  			"and  data_prvdr_cust_id  = " + dp_cust_id
  			
	
	meta_df = pd.read_sql_query(sql,  con = stmt_ngin)
		#, params=[dp_cust_id, dp_cust_id])
	
	meta_df['meta_txn_start_date'] = meta_df['meta_txn_start_date'].apply(format_date)
	meta_df['meta_txn_end_date'] = meta_df['meta_txn_end_date'].apply(format_date)
	return meta_df


def get_average_roi_old(df_flow, N):
	df_ND_total = df_flow[['txn_date', 'comms']].groupby([pd.Grouper(key='txn_date',freq=N+'D')]).sum()
	df_ND_average = df_flow[['txn_date',  'balance']].groupby([pd.Grouper(key='txn_date',freq=N+'D')]).mean()
	
	# Merge the total and average dataframes
	df_ND_ROI = pd.merge(df_ND_total,df_ND_average,how='left',on=['txn_date'])
	#print(df_ND_ROI)
	# Compute the ROI
	df_ND_ROI['__'+N+'_day_avg_roi'] = df_ND_ROI['comms']/df_ND_ROI['balance']
	
	df_ND_ROI = df_ND_ROI[['__'+N+'_day_avg_roi']]
	
	df_ND_average = pd.DataFrame(df_ND_ROI.mean()).transpose()
	
	return df_ND_average



def get_average_roi(df_flow, N):
	
	df_ND_ROI = df_flow.groupby([pd.Grouper(key='txn_date',freq=N+'D')]).agg({'comms': 'sum', 'balance': 'mean'})
	
	# Compute the ROI
	
	df_ND_ROI['__'+N+'_day_avg_roi'] = df_ND_ROI['comms']/df_ND_ROI['balance']
	
	df_ND_ROI['__'+N+'_day_avg_roi'] = df_ND_ROI['__'+N+'_day_avg_roi'].apply(lambda x: 0 if x == np.inf else x)
	#df_ND_ROI = df_ND_ROI[['__'+N+'_day_avg_roi']]
	df_ND_ROI.drop(['comms', 'balance'],axis=1,inplace=True)

	df_ND_average = pd.DataFrame(df_ND_ROI.mean()).transpose()
	
	return df_ND_average

def get_average_txn(df_flow, N):
	df_retail_tx = df_flow[df_flow['is_float'] == False]
	
	df_ND_ret_tx = df_retail_tx.groupby([pd.Grouper(key='txn_date',freq=N+'D')]).count()
	
	df_ND_ret_tx['__'+N+'_day_avg_txns'] = df_ND_ret_tx['is_float']
	df_ND_ret_tx	 = df_ND_ret_tx[['__'+N+'_day_avg_txns']]
	df_ND_ret_tx = pd.DataFrame(df_ND_ret_tx.mean()).transpose()
	
	return df_ND_ret_tx

def update_run_id(dp_cust_id, run_id):
	
	with app_ngin.connect() as con:
		rs = con.execute("update borrowers set run_id = ? where data_prvdr_cust_id = ?", run_id, dp_cust_id)	
	print(rs.rowcount)

def process_cust(dp_cust_id):
	
	'''dp_cust_id_str = "'" + dp_cust_id + "'"
	print(dp_cust_id_str)
	sql = "select data_prvdr_cust_id, txn_date, is_float, comms, balance  from "+ table_name +\
				"where  txn_date > (select date_sub(date(max(txn_date)),  interval " + str(no_of_days-1) +" day) \
				from "+ table_name +" where data_prvdr_cust_id = " + dp_cust_id_str + ")\
				and data_prvdr_cust_id = " + dp_cust_id_str
	'''
	sql = "select data_prvdr_cust_id, txn_date, is_float, comms, balance  from "+ table_name +\
				"where  txn_date > (select date_sub(date(max(txn_date)),  interval " + str(no_of_days-1) +" day) \
				from "+ table_name +" where run_id = "+run_id + " and  data_prvdr_cust_id = "+dp_cust_id + ")\
				and data_prvdr_cust_id = " + dp_cust_id  
	#print(sql)
	df_flow = pd.read_sql_query(sql,  con = stmt_ngin)
		#, params=[dp_cust_id, dp_cust_id]) 
	
	if df_flow.empty:
		print("$$$$$")
		print("No txn records to process")
		quit()
	#df_flow['balance'] = pd.to_numeric(df_flow['balance'])
	df_flow['txn_date'] = pd.to_datetime(df_flow['txn_date'])
	#df_flow['txn_date'] = df_flow['txn_date'].dt.strftime('%Y-%m-%d')
	
	
	df_30D_ret_tx = get_average_txn(df_flow[['txn_date', 'is_float']], '30')
	
	df_flow = df_flow[['txn_date', 'comms', 'balance']]
	
	df_5D_average = get_average_roi(df_flow, '5')
	df_15D_average = get_average_roi(df_flow, '15')
	df_30D_average = get_average_roi(df_flow, '30')

	# Merge the 5D, 15D and 30D calcs in a dataframe
	output_frame = df_5D_average.join(df_15D_average).join(df_30D_average).join(df_30D_ret_tx)
	#output_frame = df_30D_average.join(df_30D_ret_tx)
	
	#output_frame = pd.concat([output_frame, output_frame.apply(scorecard_ft,axis=1)], axis=1)
	#print(output_frame.columns)
 

	output_frame['data_prvdr_cust_id'] = dp_cust_id
	
	output_frame = output_frame.apply(scorecard_ft, axis=1)

	#output_frame = pd.merge(output_frame,df_name_map,how='left',on=['data_prvdr_cust_id'])
	
	#output_frame.to_sql('cust_csf_gross_result', con = stmt_ngin, if_exists='append', index = False)

	#output_frame.drop(['__5_day_avg_roi', '__15_day_avg_roi', 
									#'__30_day_avg_roi' , '__30_day_avg_txns'],axis=1,inplace=True)
	
	meta_df = get_meta_df(dp_cust_id)
	output_frame = pd.merge(output_frame, meta_df, how='left', on=['data_prvdr_cust_id'])
	
	#output_frame =output_frame.melt(id_vars=['data_prvdr_cust_id'], var_name="csf_type", value_name="csf_normal_value")
		
	output_frame['run_id'] = run_id
	print("#####")
	print(output_frame.to_csv())
	

	#output_frame.to_sql('cust_csf_values', con = app_ngin, if_exists='append', index = False,
	#										dtype={'csf_normal_value': sqlalchemy.types.NVARCHAR(length=20)
	#										})
	#update_run_id(dp_cust_id, run_id)
	
	'''
	sql = 'select data_prvdr_cust_id , biz_name from borrowers'
	df_name_map = fetch_txn(sql)
	#print(df_name_map)
	final_frame = frame
	# left join on ID
	final_frame = pd.merge(frame,df_name_map,how='left',on=['data_prvdr_cust_id'])
	# create a Transaction Time column
	'''
############### START ##################


country = sys.argv[1] 
run_id = sys.argv[2] 
dp_cust_id = sys.argv[3]
dp_code = sys.argv[4]
no_of_days = int(sys.argv[5]) #90
table_name = ' cust_acc_stmts '


sql = "select * from cs_factor_values where country_code = '" + country + "'"
df_csf_values = pd.read_sql_query(sql,  con = app_ngin)

if  df_csf_values.empty:
	print("$$$$$")
	print("No records in cs_factor_values table")
	quit()


process_cust(dp_cust_id)	
#print(datetime.datetime.now())