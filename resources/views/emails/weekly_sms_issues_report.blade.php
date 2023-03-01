

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
   <b>Weekly Report for SMS related issues ({{$data['start_date']}} to {{$data['end_date']}})</b> 
   <br/><br/>
   <div>
      <table style="border-collapse: collapse;">
         <thead>
            <tr>
               <td>Date</td>
               <td>SMS Provider</td>
               <td>Total Disbursals</td>
               <td>SMS Not Sent</td>
               <td>SMS Not Received</td>
               <td>Total SMS Failure Cases</td>
               <td>Total SMS Success Cases</td>

            </tr>
         </thead>
         <tbody>
         @foreach ($data['total_sms_cases'] as $key => $value)
            <tr>
               <td style="text-align: center">{{format_date($value->date)}}</td>
               <td style="text-align: center">{{$value->vendor_code}}</td>
               <td style="text-align: center">{{$value->total_disbursal }}</td>
               <td style="text-align: center">{{$value->sms_not_sent}}</td>
               <td style="text-align: center">{{$value->sms_not_rcvd}}</td>
               <td style="text-align: center">{{$value->sms_not_rcvd + $value->sms_not_sent }}</td>
               <td style="text-align: center">{{$value->sms_success_cases}}</td>

            </tr>
          @endforeach
         </tbody>
      </table>
   </div>
</body>