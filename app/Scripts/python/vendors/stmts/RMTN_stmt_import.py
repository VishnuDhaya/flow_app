import sys
import json
from MTN_stmt_import import *


data = json.loads(sys.argv[1])
data['country_code'] = 'RWA'

status, screenshot_path, exception = main(data, 'Africa/Kigali')
response = {'status' : status, 'screenshot_path' : screenshot_path, 'traceback' : exception}

print(json.dumps(response))