from importlib.resources import path
import sys
import pandas as pd
import json

args = json.loads(sys.argv[1])
file_name = args.get('file_name') + '.csv'
data = args.get('data')
df = pd.DataFrame(data)
path= r"public/"
df.to_csv(path + file_name, index=False)