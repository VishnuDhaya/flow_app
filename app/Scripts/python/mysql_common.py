env = {}

import mysql.connector

def fetch(sql, database):
	
	db = mysql.connector.connect(
													host="192.168.73.166",
													user="FlowUser",
													password="flowapi",
													database= database
													)
	cur = db.cursor()
	cur.fetch(sql)

	sql_data = pd.DataFrame(cur.fetchall())
	sql_data.columns = cur.column_names
	db.close()
	return sql_data

def fetch_txn(sql):
	return fetch(sql, "flow_api_test")

def fetch_calc(sql):
	return fetch(sql, "flow_credit_calc")

def execute(update_sql):
	db = mysql.connector.connect(
													host="192.168.73.166",
													user="FlowUser",
													password="flowapi",
													database= database
													)

	cur = db.cursor(update_sql)
	db.commit()

	return cur.rowcount

