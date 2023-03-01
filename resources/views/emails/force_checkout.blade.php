<div>
   <b>The user has done a force checkout.</b>
		<br><br> User : <b>{{$data['user_name']}}</b>
		<br>Checkin time : <b>{{$data['checkin_time']}}</b>
		<br>Checkout time : <b>{{$data['checkout_time']}}</b>
		<br>Duration : <b>{{$data['duration']}}</b>
		<br>Customer location : <b><a href="maps.google.com/?q={{$data['checkin_location']}}">maps.google.com/?q={{$data['cust_gps']}}</a></b>
		<br>CheckIn location : <b><a href="maps.google.com/?q={{$data['checkin_location']}}">maps.google.com/?q={{$data['checkin_location']}}</a></b>
		<br>Checkout location :<b><a href="maps.google.com/?q={{$data['checkout_location']}}">maps.google.com/?q={{$data['checkout_location']}}</a></b>
		<br>Distance between Check In & Check Out : <b>{{$data['checkout_distance']}} Meters</b>
		<br>Force checkout reason : <b>{{$data['force_checkout_reason']}}</b>

		<br><br>

		Thanks<br>
		FLOW Admin

</div>

