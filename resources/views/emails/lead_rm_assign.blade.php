@php
    $url = rtrim(config('app.url'), '/')
@endphp
<div>
    @if(array_key_exists('biz_name', $data))
        <p>A lead has been created for <b>{{$data['biz_name']}}<b></p>
        <br>Business Name : <b>{{$data['biz_name']}}</b>
        <br>Account Number : <b>{{$data['account_num']}}</b>
        <br>Account Provider : <b>{{$data['acc_prvdr_code']}}</b>
    @elseif(!array_key_exists('acc_prvdr_code', $data))
        <p>A lead has been created by <b>{{$data['cust_name']}}<b></p>
        <br>Customer Name : <b>{{$data['cust_name']}}</b>
    @elseif(array_key_exists('acc_prvdr_code', $data))
        <p>A lead has been created by <b>{{$data['cust_name']}}<b></p>
        <br>Customer Name : <b>{{$data['cust_name']}}</b>
        <br>Account Number : <b>{{$data['account_num']}}</b>
        <br>Account Provider : <b>{{$data['acc_prvdr_code']}}</b>
    @endif
   
    <br>
    <br><a href="{{$url}}/lead/edit/{{$data['id']}}" target="_blank">Click here</a> to assign the lead to an RM.
</div>
