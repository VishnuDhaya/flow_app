@php
	$url = env('APP_URL');
	$data = (object)$data;
@endphp
<div>
	<p style="font-size:150%; color:maroon">Exception Message: <b>{{json_decode($data->flow_response)->exp_msg}}</b></p> <br>
	&nbsp;&nbsp;<p>The instant disbursal for the Loan <b>{{$data->loan_doc_id}}</b> needs to be reviewed.
		<br>Customer Name : <b>{{$loan->cust_name}}</b>
		<br>Loan Status : <b>@php echo(dd_value($loan->status)) @endphp</b>
		<br>Disbursal Status : <b>@php echo(dd_value($data->status)) @endphp</b>
		<br>Attempt ID : {{$disb_attempt_info->id}}
		<br>Total Attempts : {{$disb_attempt_info->count}}
		<br>
		<br><a href="{{$url}}fa/view/{{$data->loan_doc_id}}" target="_blank">Click here</a> to go to the Loan page.
		<br>
		<h4><u>Screenshot(s) :</u></h4>
		@php $partner_response = json_decode($data->partner_response) @endphp
		@if(is_array($partner_response))
		@foreach ($partner_response as $res)
			@if(isset($res->screenshot_path)) 
			<!-- <p>{{rtrim($url,'/').$res->screenshot_path}}</p> -->
			<img src="{{$message->embed(asset($res->screenshot_path))}}" width="100%" height="100%"/> 
			@endif
		@endforeach
		@endif
		<br>
		<h4><u>Disbursal Details</u></h4>
		<table style = 'border-collapse: collapse;'>

			<tbody>
				<tr style = 'border: solid; border-width: 1px 0;'>
					  <td> loan_status </td>
					  <td> <pre>{{$loan->status}}</pre> </td>
				  </tr>
				 @foreach($data as $key=>$value)
					 @php if($key == 'status'){$key = 'disbursal_status';} @endphp
				  <tr style = 'border: solid; border-width: 1px 0;'>
					  <td> {{$key}} </td>
					  <td> <pre>{{$value}}</pre> </td>
				  </tr>
				 @endforeach
		   </tbody>
		</table>
		<br><br>

	thanks<br>
	FLOW Admin
</div>