<div>
    <p>The following error occurred while trying to reconciling the {{$data['txn_type']}} transaction at {{$data['failed_at']}}.</p>

    <p><b>Transaction Details : </b></p>
    Transaction ID: <b>{{$data['stmt_txn_id']}}</b>
    <br>
    Transaction Date: <b>{{$data['stmt_txn_date']}}</b>
    <br>
    Description: <b>{{$data['descr']}}</b>
    <br>
    Amount: <b>{{$data['amount']}} {{$data['currency_code']}}</b>

    <p><pre style="color:maroon"><b>{{$data['exception']}}</b></pre></p> <br>
    <p><pre style="color:maroon">{{$data['trace']}}</pre></p> <br>

</div>
