<div>
   An error occurred while {{$data['mail_config']['action']}} for {{$data['acc_number']}}<br/>
      <br>AWS Lambda function: <b>{{$data['function_name']}}</b>
      <br>Cloudwatch Logs: <b>{{$data['log_file']}}</b>
      <br>Event obtained by Lambda: <pre>{{ print_r($data['event']) }}</pre>
      <br><br>
</div>
<div>
   The traceback for the error:<br/>
   <p><pre style="color:maroon">{{$data['exception']}}</pre></p> <br>
</div>