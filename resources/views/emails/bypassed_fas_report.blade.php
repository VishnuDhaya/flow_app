

<head>
   <style>
      thead{
      font-weight: bold;
      }
      td{
      border: 1px solid black;
      padding: 6px;
      }
      tr{
      border: solid;
      border-width: 1px
      }
     
   </style>
</head>
<body>
   <b>Over the last 30 days, below FAs were disbursed for a customer three or more times without getting SMS based OTP confirmation.</b> 
   <br/><br/>
   <div>
      <table style="border-collapse: collapse;">
         <thead>
            <tr>
               <td>Cust ID</td>
               <td>SMS Not Sent</td>
               <td>SMS Not Received</td>
               <td>Total Bypassed FAs</td>
               <td>Total Disbursals</td>
               <td>Mobile Number</td>
               <td>Bypassed FA IDs</td>
            </tr>
         </thead>
         <tbody>
         @foreach ($data['bypassed_cases'] as $key => $value)
            <tr>
               <td >{{$value->cust_id}}</td>
               <td style="text-align: center">{{$value->sms_not_sent}}</td>
               <td style="text-align: center">{{$value->sms_not_rcvd}}</td>
               <td style="text-align: center">{{$value->bypassed_fas_count}}</td>
               <td style="text-align: center">{{$value->total_disbursals}}</td>
               <td style="text-align: center">{{$value->mobile_num}}</td>
               <td>{{$value->bypassed_fas}}</td>
            </tr>
          @endforeach
         </tbody>
      </table>
   </div>
</body>