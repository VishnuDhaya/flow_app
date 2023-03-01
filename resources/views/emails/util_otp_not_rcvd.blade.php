@php
        $url = env('APP_URL')
@endphp
<div>
    @if($data['purpose'] == 'disbursal')
        Disbursal for the FA [<a href="{{$url}}fa/view/{{$data['loan_doc_id']}}" target="_blank">{{$data['loan_doc_id']}}</a>]
    @elseif($data['purpose'] == 'stmt_import')
        Statement import for the account [{{$data['account']['acc_prvdr_code']}} - {{$data['account']['acc_number']}}]
    @endif
        has failed since OTP was not received from the SMS Utility app. <br/>
        Ensure that the device is connected to the internet.
</div>
