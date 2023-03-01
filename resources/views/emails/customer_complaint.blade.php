@php
    $url = env('APP_URL');
@endphp

<div>

{{$data['cust_name']}} has registered a complaint about {{$data['complaint_type']}}.

</div>

<div>
    <h3><b>Complaint Details</b></h3>
<table>
        <tr>
            <th style="text-align: left">Customer Name</th>
            <td style="text-align: center">:</td>
            <td>{{$data['cust_name']}}</td>
        </tr>
        <tr>
            <th style="text-align: left">Complaint about</th>
            <td style="text-align: center">:</td>
            <td>{{$data['complaint_type']}}</td>
        </tr>   
        <tr>
            <th style="text-align: left">Complaint details</th>
            <td style="text-align: center">:</td>
            <td>{{$data['remarks']}}</td>
        </tr> 
        
</table>

</div>

<div>
    <p><a href="{{$url}}/complaint_lists"> Click here </a> to take action on the complaint.</p>
</div>