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
   <b>Please be noted that the commission for the below accounts are not available in the commission sheet.</b> 
   <br/><br/>
   <b><u>Missing Commission Information</u></b> 
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
            </tr>
         </thead>
         <tbody>
            @foreach ($data['accounts'] as $account)
               <tr>
                  <td>{{$account->cust_id}}</td>
                  <td>{{$account->acc_prvdr_code}}</td>
                  <td>{{$account->acc_number}}</td>
                  <td>{{$account->alt_acc_num}}</td>
                  <td>{{$account->holder_name}}</td>
               </tr>
            @endforeach
         </tbody>
      </table>
   </div>
   <br/><br/>
   <b>Thank you.</b>
   <br/><br/>
</body>