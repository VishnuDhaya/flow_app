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
   <b><p style="font-size: 15px">Leads with pending mobile number verification below</p></b> 
   
   <br/>
        <div>
            <table id="customers">
                <tr>
                    <th>Cust Name</th>
                    <th>Biz Name</th>
                    <th>A/C Prvdr Code</th>
                    <th>A/C Number</th>
                    <th>Mobile Number</th>
                    <th>RM Name</th>
                    <th>Assigned</th>
                    <th>Action</th>
                </tr>
                <tr>
                    <td> {{$data['first_name']}}</td>
                    <td> {{$data['biz_name']}}</td>
                    <td>{{$data['acc_prvdr_code']}}</td>
                    <td>{{$data['acc_num']}}</td>
                    <td> {{$data['mobile_num']}}</td>
                    <td> {{$data['rm_name']}}</td>
                    <td>{{$data['assigned_date']}} </td>
                    <a href="{{$url}}lead/ver_mob_num/{{$data['lead_id']}}">Verify</a>
                </tr>
                </table>
        </div>
</body>

