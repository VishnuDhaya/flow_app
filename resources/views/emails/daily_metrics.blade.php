<head>
   <style>

     tr td{
      text-align: right;
     }
     tr th {
      text-align: left;

     }
     
      th, td { padding: 2px 8px; border: 1px solid black; height: 40px; align-items: center; }
      
/* border-collapse */
       tr>*:not(:first-child) { border-top: 0; }
      tr:not(:first-child)>* { border-left:0;padding: 4px; } 


   </style>
</head>
<body>
   <b><p style="font-size: 16px; text-align:center">{{$data['yesterday_date']}} - {{$data['day']}} - {{$data['country_code']}} Metrics</p></b> 
   <br/>
   <div>
      <table style="margin:0 auto;width:100%;max-width : 800px;border-collapse: collapse; border: 1px solid black; border-bottom:0;">
            <tr>
               <th>No. of Customers Registered</th>
               <td >{{$data['total_reg_cust_count']}}</td>
            </tr>
            <tr>
               <th>No. of Customers Registered on {{$data['yesterday_date']}}</th>
               <td >{{$data['tdy_reg_cust_count']}}</td>
            </tr>
          <tr>
              <th>No. of Active Customers as on {{$data['yesterday_date']}}</th>
              <td >{{$data['tot_active_cust']}}</td>
          </tr>
            <tr>
               <th>No. of FAs Disbursed on {{$data['yesterday_date']}}</th>
               <td >{{$data['tot_disbursal_count']}}</td>
            </tr>
          <tr>
              <th>No. of FAs Outstandings</th>
              <td >{{$data['outstanding_fas_count']}}</td>
          </tr>
            <tr>
               <th>Value of FAs Outstanding (Book Value)</th>
               <td >{{$data['outstanding_fas_value']}} {{$data['currency_code']}}</td>
            </tr>
            <tr>
               <th>Value of FAs Overdue</th>
               <td >{{$data['overdue_fas']}} {{$data['currency_code']}} </td>
            </tr>
            <tr>
               <th>Total Volume of Disbursements </th>
               <td >{{$data['total_volume_of_disbursal']}} {{$data['currency_code']}}</td>
            </tr>
            <tr>
               <th>Repeat Customers</th>
               <td >{{$data['repeat_customers']}} %</td>
            </tr>
            <tr>
               <th>Repayment Rate </th>
               <td >{{$data['repayment_rate']}} %</td>
            </tr>
            <tr>
               <th>Ontime Repayment Rate </th>
               <td >{{$data['ontime_repayment_rate']}} %</td>
            </tr>
            <tr>
               <th>Total Value Disbursed on {{$data['yesterday_date']}}</th>
               <td >{{$data['total_disbursal_today']}} {{$data['currency_code']}}</td>
            </tr>
            <tr>
               <th>Total Value Repaid on {{$data['yesterday_date']}} </th>
               <td >{{$data['total_repaid_today']}} {{$data['currency_code']}}</td>
            </tr>
            <tr>
               <th>Total Fee Received on {{$data['yesterday_date']}}</th>
               <td >{{$data['total_fee']}} {{$data['currency_code']}}</td>
            </tr>
            <tr>
               <th>Total Fee Received for {{$data['month']}} month till {{$data['yesterday_date']}}</th>
               <td >{{$data['total_fee_mtd']}} {{$data['currency_code']}}</td>
            </tr>
      </table>
   </div>
</body>