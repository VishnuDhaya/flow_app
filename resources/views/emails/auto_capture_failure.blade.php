@php
	$url = rtrim(config('app.url'), '/')
@endphp
<div>
    <p><b>Transaction Details : </b></p>
    Transaction ID: <b>{{$data['stmt_txn_id']}}</b>
    <br>
    Transaction Date: <b>{{$data['stmt_txn_date']}}</b>
    <br>
    Amount: <b>{{$data['cr_amt']}} {{$data['currency_code']}}</b>

    <p>The following error occurred while trying to capture the FA <a href="{{$url}}/fa/view/{{$data['loan_doc_id']}}" target="_blank">{{$data['loan_doc_id']}}</a> at {{$data['failed_at']}}.</p>

    <p><pre style="color:maroon">{{$data['exception']}}</pre></p> <br>
</div>