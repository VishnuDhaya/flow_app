
@php
    $url = env('APP_URL');  
    logger($data['full_path'])
                
@endphp

<head>
<link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" >

   <style>
   
   

     tr td{
      text-align: center;
     }
     tr th {
      text-align: center;

     }


      th, td {border: 1px solid black; padding:10px 8px;}

      .image{
        margin: auto;
        height: 300px;
      }
      
/* border-collapse */
       tr>*:not(:first-child) { border-top: 0; }
      tr:not(:first-child)>* { border-left:0;padding: 4px; } 


   </style>
</head>
<body>
   <b><p style="font-size: 15px; text-align:center">Below {{$data['txn_type']}} Captured using skip transaction id check </p></b> 
   <br/>
   <div>
      <table style="margin:0 auto;width:100%;max-width : 800px;border-collapse: collapse; border: 1px solid black; border-bottom:0;">
            <tr>
               <th>Cust Name</th>
               <td >{{$data['cust_name']}}</td>
            </tr>
            <tr>
               <th>Biz Name</th>
               <td >{{$data['biz_name']}}</td>
            </tr>
            <tr>
               <th>A/C Number</th>
               <td >{{$data['acc_number']}}</td>
            </tr>
            <tr>
               <th>Loan Doc ID</th>
               <td >{{$data['loan_doc_id']}}</td>
            </tr>
            <tr>
               <th>Txn ID</th>
               <td >{{$data['txn_id']}}</td>
            </tr>
            <tr>
               <th>Txn Date</th>
               <td >{{format_date($data['txn_date'])}}</td>
            </tr>
            <tr>
               <th>Reason For Skip</th>
               <td >{{dd_value($data['reason_for_skip'])}}</td>
            </tr>
            <tr>
               <th>Photo Transaction Proof</th>
               <td >
                  <div class="row image " style="justify-content: center; text-align:center; mb-3" > 
                     <div class="col-sm-2 col-md-3">                                       
                        <img class= "image"  src="{{$message->embed($data['full_path'])}}" />	
                     </div>
                  </div>
               </td>
            </tr>   
      </table>
              
   </div>
</body>