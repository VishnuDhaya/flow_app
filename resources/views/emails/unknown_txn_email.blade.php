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
   <b>Please see the below unknown transactions for the date {{$data['yesterday']}}.</b> 
   <br/><br/>
   <b><u>Unknown Transactions</u></b> 
   <br/><br/>
   <div>
      <table style="border-collapse: collapse;">
         <thead>
            <tr>
               <td>Network</td>
               <td>A/C Provider</td>
               <td>A/C Number</td>
               <td>No. of Credit Transactions</td>
               <td>Total Credited Amount</td>
               <td>No. of Debit Transactions</td>
               <td>Total Debited Amount</td>
            </tr>
         </thead>
         <tbody>
            @foreach ($data['unknown_txns'] as $value)
               <tr>
                  <td>{{$value['network_prvdr']}}</td>
                  <td>{{$value['acc_prvdr']}}</td>
                  <td>{{$value['acc_number']}}</td>
                  <td>{{$value['no_of_credit_txns']}}</td>
                  <td>{{$value['total_amt_credited']}}</td>
                  <td>{{$value['no_of_debit_txns']}}</td>
                  <td>{{$value['total_amt_debited']}}</td>
               </tr>
            @endforeach
         </tbody>
      </table>
   </div>
   <br/><br/>
   <b>Please refer to the link below to see the details of the unknown transactions.</b>
   <br/>
   <a href="{{$data['app_url']}}/unknown_txns/{{$data['yesterday']}}">{{$data['yesterday']}}</a>
   <br/><br/>
   <b>Thank you.</b>
   <br/><br/>
</body>