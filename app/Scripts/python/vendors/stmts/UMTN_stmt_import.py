import sys
import json
from MTN_stmt_import import *


data = json.loads(sys.argv[1])
data['country_code'] = 'UGA'

status, screenshot_path, exception = main(data, 'Africa/Kampala')
response = {'status' : status, 'screenshot_path' : screenshot_path, 'traceback' : exception}

print(json.dumps(response))