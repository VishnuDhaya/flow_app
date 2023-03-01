@php
	$url = env('APP_URL')
@endphp
<div>
   <p>Duplicate profiles exist with <b>{{$data['mobile_num']}}</b> as {{$data['mobile_field']}} mobile number.</p>
   @if($data['mobile_field'] == 'primary')
		<p>Repeat FA SMSs from this number will not be processed until this issue has been resolved</p>
   @endif
		<br><br><h4>Profiles</h4>
		@foreach($data['cust_ids'] as $cust_id)
			<p><a href="{{$url}}borrower/indiv/view/{{$cust_id}}">{{$cust_id}}</a></p>
		@endforeach
		<br><br>

		Thanks<br>
		FLOW Admin

</div>
