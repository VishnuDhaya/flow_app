<div>
     
    @if($data['reason'] == 'CALL_QUEUE_TO_ONE_DIALER_FAILED')
        The following error occured when we tried to make a reminder call to the RelationshipManager 
        <p><span style="color:maroon"><b>{{$data['error_msg']}}</b></span></p>
        Take action.
                       
    @elseif($data['reason'] == 'CALL_QUEUE_TO_ALL_DIALERS_FAILED')
        The Reminder call for RelationshipManager is not happening. Due to ENTIRE request was rejected by AfricasTalking API by below reason
        <p><span style="color:maroon"><b>{{$data['error_msg']}}</b></span></p>
        Take action immediately.

    @elseif($data['reason'] == 'EXCEPTION_WHILE_QUEUING_CALLS')

        <p><span style = "color:maroon"><b>Exception error in Reminder call </b></span></p>
        <p>The Reminder call has failed due to <span style = "color:maroon"><b>{{$data['error_msg']}}</b></span></p>
        <p><b>Trace : </b></p>
        <p>{{$data['exception']}}</p>

    @elseif($data['reason'] == 'CALL_HANGUP_UNSPECIFIED_REASON')
        The Reminder call for this number {{$data['mob_num']}} gets failed. Due to below reason
        <p>
            <b>{{$data['issue']}} : {{$data['error_msg']}}</b>
        </p>
        Report it to AfricasTalking 
    @endif
                

</div>