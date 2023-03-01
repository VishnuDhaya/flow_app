@php
    $url = env('APP_URL');
@endphp
<div>
    <h3> The Alternate Account Number has not been configured for below Customer</h3>
   
    <table>
        <tr>
            <th style="text-align: left">Customer ID</th>
            <td style="text-align: center">:</td>
            <td>{{$data['cust_id']}}</td>
        </tr>
        <tr>
            <th style="text-align: left">Customer Name</th>
            <td style="text-align: center">:</td>
            <td>{{$data['cust_name']}}</td>
        </tr>
        <tr>
            <th style="text-align: left">Mobile Number</th>
            <td style="text-align: center">:</td>
            <td>{{$data['mobile_num']}}</td>
        </tr>  
        <tr>
            <th style="text-align: left">Account Number</th>
            <td style="text-align: center">:</td>
            <td>{{$data['acc_num']}}</td>
        </tr>    
        <tr>
            <th style="text-align: left">Account Provider</th>
            <td style="text-align: center">:</td>
            <td>{{$data['acc_prvdr_code']}}</td>
        </tr> 
</div>
<div>
    <h3>Next Steps :</h3>
    <p>1. Inform the respective Operations Admin</p>
    <p>2. RM to get the alternate account number(MSISDN) from the customer.</p>
    <p>3. RM to share it to R&C team and Operarions Admin</p>
    <p>4. R&C or Operations admin should update the alternate account number under Customer Profile -> Accounts</p>
</div>
<div>
    <a href="{{$url}}borrower/indiv/view/{{$data['cust_id']}}"> Click here </a>to UPDATE Alternate Account Number.
</div>
