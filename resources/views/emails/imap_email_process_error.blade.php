<div>
    The following error occurred while processing email for {{$data['acc_prvdr_code']}} at {{$data['failed_at']}}<br/>
    <p>{{$data['message']}}</p><br>
</div>
<div>
   The traceback for the error:<br/>
   <p><pre style="color:maroon">{{$data['exception']}}</pre></p> <br>
</div>