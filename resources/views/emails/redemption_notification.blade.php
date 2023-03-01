


<head>
   <style>
   
     tr { display: block; float: left ;width:50%}

     tr td{
      text-align: center;
     }

      th, td { display: grid; border: 1px solid black; padding:10px 0; }
      
/* border-collapse */
       tr>*:not(:first-child) { border-top: 0; }
      tr:not(:first-child)>* { border-left:0;padding: 4px; } 


   </style>
</head>
<body>
   <b><p style="font-size: 15px">Below details for Redemption on {{format_date($data['stmt_txn_date'])}}</p></b> 
   <br/>
   <div>
      <table style="margin:0 auto;width:90%;border-collapse: collapse; border: 1px solid black; border-bottom:0;">
            <tr >
               <th>Stmt Txn Date</th>
               <th>Txn ID</th>
               <th>Amount</th>
               <th>A/C Number</th>
               <th>A/C Provider</th>
               
            </tr>
            <tr >
               <td>{{$data['stmt_txn_date']}}</td>
               <td>{{$data['txn_id']}}</td>
               <td>{{$data['amount']}} {{$data['currency_code']}}</td>
               <td>{{$data['acc_number']}}</td>
               <td>{{$data['acc_prvdr']}}</td>
            </tr>
      </table>
   </div>
</body>