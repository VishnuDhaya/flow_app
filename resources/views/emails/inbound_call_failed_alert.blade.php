@php
    $url = env('APP_URL');
@endphp
<div>

        The Inbound call to this sip number {{$data['destination_num']}} gets failed. Due to below reason
        <p>
            <b>{{$data['issue']}} : {{$data['error_msg']}}</b>
        </p>
        
        <h2>Steps to be followed in Troubleshooting the sip numbers</h2>

        <p>1.Check whether {{$data['cs_name']}}'s internet connection is stable.</p>
        <p>2.Check the above sip number is logged into the sip client (Zoiper). </p>
        <p>3.If then logout from the device and login once again</p>
        
        <p>If the above steps has not resolved the connectivity of the device, report to Appsupport.</p>

        <a href="{{$url}}/cs_management/cs_rosters">Click Here</a> to unassign the sip number to avoid getting call to above sip number 
</div>