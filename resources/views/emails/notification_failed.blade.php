@php
    if($data['notify_type'] == 'new_loan_appl'){
        $type = 'Approval Required';
    }
    elseif($data['notify_type'] == 'pre_approved') {
        $type = 'Pre Approval';
    }
    elseif($data['notify_type'] == 'fp_success'){
        $type = 'File Process Status';
    }
    elseif($data['notify_type'] == 'rm_visit_request'){
        $type = 'RM Visit Request';
    }
    else{
        $type = \Illuminate\Support\Str::title($data['notify_type']);
    }
@endphp
<div>
    The following error occurred while attempting to send <b>{{$type}}</b> notification to {{$data['recipient_name']}}.<br />

    <p>
    <pre style="color:maroon"><b>{{$data['exp_msg']}}</b></pre>
    </p> <br>
    <p>
    <pre style="color:maroon">{{$data['exp_trace']}}</pre>
    </p> <br>
</div>