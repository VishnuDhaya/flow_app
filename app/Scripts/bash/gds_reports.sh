#!/bin/bash

#0 5 * * * cd /usr/share/nginx/html/flow-api/app && bash Scripts/bash/gds_reports.sh>/usr/share/nginx/html/reports.log

python3 Scripts/python/reports/client_performance_funds.py &&
python3 Scripts/python/reports/client_performance.py &&
python3 Scripts/python/reports/port.py&&
python3 Scripts/python/reports/portfolio.py&&
python3 Scripts/python/reports/revenuepercustomer.py &&
python3 Scripts/python/reports/live_report.py &&
python3 Scripts/python/reports/monthly_report.py
