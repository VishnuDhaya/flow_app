<div>
   <b>The user has done a force checkin.</b>
		<br><br> User : <b>{{$data['user_name']}}</b>
		<br>Checkin time : <b>{{$data['checkin_time']}}</b>
		<br>Customer location : <b><a href="maps.google.com/?q={{$data['cust_gps']}}">maps.google.com/?q={{$data['cust_gps']}}</a></b>
		<br>CheckIn location : <b><a href="maps.google.com/?q={{$data['gps']}}">maps.google.com/?q={{$data['gps']}}</a></b>
		<br>Distance between CheckIn location & Customer location : <b>{{$data['checkin_distance']}} Meters</b>
		<br>Force checkin reason : <b>{{$data['force_checkin_reason']}}</b>

		<br><br>

		Thanks<br>
		FLOW Admin

</div>

