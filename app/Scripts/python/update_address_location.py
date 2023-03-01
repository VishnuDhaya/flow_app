import pandas as pd
from sqlalchemy import create_engine
import os

from flow_common import db_str, load_env, env, get_unique_files 

load_env()


def fetch():

	db = create_engine('mysql+mysqlconnector://' + db_str('STMT'))
	print(db)

	file_name = r'../../../storage/data/Uganda Address Locations.xlsx' 
	dirname = os.path.dirname(__file__)
	file_name = os.path.join(dirname, file_name)

	df = pd.read_excel(file_name,'Sheet2',skiprows=[0,2],na_filter=False)
	df = pd.DataFrame(df)

	for index,row in df.iterrows():
		try:
			data_prvdr_cust_id = row['data_prvdr_cust_id']
			location_xl = row['Confirmed Location']
			location_xl = location_xl.lower()

			addr_id = "select owner_address_id from borrowers where data_prvdr_cust_id ='{}' ".format(data_prvdr_cust_id)
			print(addr_id)
			
			addr_ids = pd.read_sql_query(addr_id , con = db)
			

			for index,addr_id in addr_ids.iterrows():
			
				# location_db = "select field_8 from address_info where id = '{}'".format(addr_id['owner_address_id'])
				
				# location_db = pd.read_sql_query(location_db , con = db)

				# location_db = location_db['field_8'][0]	
				# print(location_db is not None)			

				# if(location_xl is not None and  location_db is None):
				if(location_xl is not None):
					update_query = "update address_info set field_8 ='{}' where id = '{}'".format(location_xl,addr_id['owner_address_id'])
					print(update_query)
					with db.begin() as conn:
						conn.execute(update_query)
						print("location updated")

			if(index > 1399 ):
				break

		except Exception as e:
		 	raise e

	return "Updated successfully"

result = fetch()
print(result)
	