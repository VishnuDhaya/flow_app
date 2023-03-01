@php
    $url = env('APP_URL');
@endphp

<head>
<link href="{{ asset('css/app.css') }}" rel="stylesheet" type="text/css" >

<style>
#customers {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 90%;
}
#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}
#customers tr:nth-child(even){background-color: #f2f2f2;}
#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  color: black;
  font-weight: 600;
}

.image{
    margin: auto;
    height: 400px;
    width:400px; 
    }
</style>
</head>
<body>
   <b><p style="font-size: 15px"> Below is the manual captured txns from {{$data['start_date']}} to {{$data['end_date']}} </p></b> 
   
   <br/>

   <div>
    
      <table id="customers">
            <tr>
                <th>Biz Name</th>
                <th>A/C Number</th>
                <th>FA ID</th>
                <th>Txn Date</th>
                <th>Txn ID</th>
                <th>Amount</th>
                <th>Reason For Skip</th>
                <th>Photo Transaction Proof</th>
             </tr>
             @foreach ($data['loan_txns'] as $result)
                <tr>
                    <td> {{$result->biz_name}}</td>
                    <td> {{$result->acc_number}}</td>
                    <td> <a href="{{$url}}fa/view/{{$result->loan_doc_id }}"> {{$result->loan_doc_id }} </a></td>
                    <td >{{format_date($result->txn_date)}}</td>
                    <td> {{$result->txn_id}}</td>
                    <td >{{ number_format($result->amount , 0) }} {{$result->currency_code}} </td>
                    <td> {{dd_value($result->reason_for_skip)}}</td>                    

                    <td style="text-align:center" >
                        @if(isset($result->full_path)) 
                           <a href="{{$url}}/{{$result->full_path}}">View</a>                            
                        @endif  
                    </td>

                    

                </tr>
            @endforeach
 
         </table>
   </div>

</body>
