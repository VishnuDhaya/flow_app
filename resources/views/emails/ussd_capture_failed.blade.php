@php
    $url = env('APP_URL');
@endphp
<div>
    The following error occurred while capturing USSD disbursal for the FA <a href="{{$url}}fa/view/{{$data['loan_doc_id']}}" target="_blank">{{$data['loan_doc_id']}}</a>.<br/>
        Customer ID : <b>{{$data['cust_id']}}</b>
		<br>Flow Account ID : <b>{{$data['account_id']}}</b>
		<br>Account Provider : <b>{{$data['acc_prvdr_code']}}</b>
		<br>Transaction ID : <b>{{$data['txn_id']}}</b>
		<br>Disbursal Attempt ID : <b>{{$data['disb_id']}}</b>
	
    <p><pre style="color:maroon"><b>{{$data['exp_msg']}}</b></pre></p> <br>
    <p><pre style="color:maroon">{{$data['exp_trace']}}</pre></p> <br>
</div>