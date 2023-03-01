@php
	$url = rtrim(env('APP_URL'),'/');
    $multiple = sizeof($data['existing_records']) > 1;
    $records = "";
    foreach($data['existing_records'] as $record){
        $records .=  "<a href='{$url}/fa/view/{$record}' target='_blank'>{$record}</a>, ";
    }
    $records = rtrim($records, ', ');
@endphp
<div>
   The FA <a href="{{$url}}/fa/view/{{$data['loan_doc_id']}}" target="_blank">{{$data['loan_doc_id']}}</a> is found to be a duplicate for the customer 
    <a href="{{$url}}/borrower/indiv/view/{{$data['cust_id']}}" target="_blank">{{$data['cust_id']}}</a>.<br>
    This FA will not be disbursed.<br><br>

    The following {{$multiple ? "FAs" : "FA"}} {{$multiple ? "were" : "was"}} already processed today for this customer:<br>
    {!! $records !!}
    <br>
    <br>
    FLOW Admin


</div>
