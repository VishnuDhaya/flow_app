@php
	$url = rtrim(env('APP_URL'),'/');
@endphp
<div>
   A duplicate disbursal transaction has been captured for the FA <a href="{{$url}}/fa/view/{{$data['loan_doc_id']}}" target="_blank">{{$data['loan_doc_id']}}</a> with transaction ID: {{$data['txn_id']}}.<br>
    Please ensure that the customer returns the amount that was disbursed in this transaction.<br>

    <br>
    FLOW Admin


</div>
