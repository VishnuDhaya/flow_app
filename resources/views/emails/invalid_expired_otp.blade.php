@php
$url = env('APP_URL')
@endphp
<div>
    @if($validity_status == 'invalid')
        <p> The confirm code/OTP SMS sent by the customer(<b>{{$cust->biz_name}} | {{$data['mobile_num']}}</b>) is <b>invalid</b>.<br>Please support the customer for sending the SMS in correct format.</p>
    @elseif($validity_status == 'expired')
        <p>The confirm code/OTP SMS sent to the customer(<b>{{$cust->biz_name}} | {{$data['mobile_num']}}</b>) is <b>expired</b>.<br> Please resend the confirm code/OTP, if needed.</p>
    @elseif($validity_status == 'alt_num')
        <p>An SMS has been received from the customer <b>{{$cust->biz_name}}</b> from their alternate number <b>{{$data['mobile_num']}}</b>.</p>
        <p>Please instruct the customer to send SMSs to FLOW from their registered mobile number.</p>
    @elseif($validity_status == 'unknown')
        <p>An unknown SMS has been received from the customer(<b>{{$cust->biz_name}} | {{$data['mobile_num']}}</b>).</p>
    @endif

    <br>FLOW Cust ID : <b>{{$cust->cust_id}}</b>
    <br>Account : <b>{{$cust->acc_number}}</b>
    @if($cust->ongoing_loan_doc_id)
       <br>Ongoing Loan : <b><a href="{{$url}}fa/view/{{$cust->ongoing_loan_doc_id}}" target="_blank">{{$cust->ongoing_loan_doc_id}}</a></b>
    @endif
    @if($validity_status == 'unknown')
    <br>Message Received : <b>{{$data['message']}}</b>
    <br>
    @endif

    <br>thanks
    <br>Flow App Support
</div>
