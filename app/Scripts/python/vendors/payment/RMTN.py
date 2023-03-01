import sys
import json
from MTN import *


data = json.loads(sys.argv[1])

data['sub_path'] = 'RWA/payments/RMTN'
data['country_code'] = 'RWA'

send_money(data, 'msisdn', 'Africa/Kigali')


