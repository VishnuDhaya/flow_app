@php
    $sms_type = $is_otp ? 'confirm code or OTP' : 'notification';
@endphp
<div>
    The {{$sms_type}} SMS sent to the customer(<b>{{$cust_name}} | {{$mobile_num}}</b>) has not been delivered. It got resulted in <b>{{$status}}</b> status from the vendor.
</div>
