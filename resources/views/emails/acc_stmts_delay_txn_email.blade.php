<head>
   <style>

     tr td{
         text-align: right;
         font-size: 16px;
     }
     tr th {
         text-align: left;
         font-size: 16px;
     }
     
      th, td { padding: 2px 10px; border: 1px solid black; height: 40px; align-items: center; max-width: 100px;}
      
/* border-collapse */
       tr>*:not(:first-child) { border-top: 0; }
      tr:not(:first-child)>* { border-left:0;padding: 4px; } 
     p{
         font-size: 16px;
     }

   </style>
</head>
<body>
   <b><p>The following transactions are taken more than six minutes to import</p></b>
   <p>Import ID - {{$data['import_id']}}, Import start time - {{$data['start_time']}}, Import end time - {{$data['end_time']}}, Account number - {{$data['acc_number']}}</p>
   <p>Account provider - {{$data['acc_prvdr_code']}}, No of Transactions - {{$data['no_of_txns']}}, Average Transaction delay - {{$data['avg_txn_delay']}} minutes</p>
   <br>
   <br/>
   <div>
      <table style="width: 35%; border-collapse: collapse; border: 1px solid black; border-bottom: 0;">
            <tr>
               <th>Transaction ID</th>
               <th>Transaction time</th>
               <th>Insert time</th>
            </tr>
            @foreach($data['delayed_records'] as $record)
                <tr>
                    <td style="text-align: left;">{{$record->stmt_txn_id}}</td>
                    <td>{{$record->stmt_txn_date}}</td>
                    <td>{{$record->created_at}}</td>
                </tr>
            @endforeach
      </table>
   </div>
</body>