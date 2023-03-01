import sys
import json
from MTN import *



data = json.loads(sys.argv[1])

data['sub_path'] = 'UGA/payments/UMTN'
data['country_code'] = 'UGA'

send_money(data, 'alias', 'Africa/Kampala')
