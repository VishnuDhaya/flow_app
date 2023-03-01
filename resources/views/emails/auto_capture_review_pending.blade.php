@php
	$url = rtrim(config('app.url'), '/')
@endphp
<div>
    <p><pre style="color:maroon">Can not Capture Payment. Review Pending for the FA {{$data['loan_doc_id']}}.</pre></p>
    Reason: <b>@php echo(dd_value($data['review_reason'])) @endphp</b><br>

    <p><b>Transaction Details : </b></p>
    Transaction ID: <b>{{$data['stmt_txn_id']}}</b><br>
    Transaction Date: <b>{{$data['stmt_txn_date']}}</b><br>
    Amount: <b>{{$data['cr_amt']}} {{$data['currency_code']}}</b><br> 


    <br><a href="{{$url}}/fa/view/{{$data['loan_doc_id']}}" target="_blank">Click here</a> to go to the Loan page.
</div>