@php
    $url = env('APP_URL');
@endphp

<div>

{{$data['cust_name']}} has registered a complaint about {{$data['complaint_type']}}.

</div>

<div>
    <h3><b>Complaint Details</b></h3>
    <br/>
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
            <th style="text-align: left">Date of Complaint</th>
            <td style="text-align: center">:</td>
            <td>{{$data['date_of_complaint']}}</td>
        </tr> 
        <tr>
            <th style="text-align: left">Remarks </th>
            <td style="text-align: center">:</td>
            <td>{{$data['remarks']}}</td>
        </tr> 
        <tr>
            <th style="text-align: left">Resolution</th>
            <td style="text-align: center">:</td>
            <td>{{$data['resolution'][0]}}</td>
        </tr> 
        
</table>
   

</div>


