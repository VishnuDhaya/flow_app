<head>
   <style>
      thead{
      font-weight: bold;
      }
      td{
      border: 1px solid black;
      padding: 4px;
      }
      tr{
      border: solid;
      border-width: 1px
      }
      tbody tr td{
      text-align: right;
      }
   </style>
</head>
<body>
   <br/>
   <b>Good day,</b>
   <br/><br/>
   <b>Please be noted that the limits for the below accounts were maintained despite the change in limit based on commission data.</b> 
   <br/><br/>
   <b><u>Accounts with limit adjustment</u></b> 
   <br/><br/>
   <div>
      <table style="border-collapse: collapse;">
         <thead>
            <tr>
               <td>Customer ID</td>
               <td>A/C Provider</td>
               <td>A/C Number</td>
               <td>Alternate A/C Number</td>
               <td>Holder Name</td>
               <td>Last {{$data['months_considered']}} months commission</td>
               <td>Previous Commission Based Limit</td>
               <td>Max FA Taken (Last 5 FAs)</td>
               <td>Minimum Of Both</td>
               <td>New Limit</td>
               <td>Adjusted Limit</td>
            </tr>
         </thead>
         <tbody>
            @foreach ($data['accounts'] as $account)
               <tr>
                  <td>{{$account['cust_id']}}</td>
                  <td>{{$account['acc_prvdr_code']}}</td>
                  <td>{{$account['acc_number']}}</td>
                  <td>{{$account['alt_acc_num']}}</td>
                  <td>{{$account['holder_name']}}</td>
                  <td>{{$account['commission']}}</td>
                  <td>{{$account['previous_csf_limit']}}</td>
                  <td>{{$account['previous_loan_limit']}}</td>
                  <td>{{$account['min_of_both']}}</td>
                  <td>{{$account['new_limit']}}</td>
                  <td>{{$account['limit_used']}}</td>
               </tr>
            @endforeach
         </tbody>
      </table>
   </div>
   <br/><br/>
   <b>Thank you.</b>
   <br/><br/>
</body>