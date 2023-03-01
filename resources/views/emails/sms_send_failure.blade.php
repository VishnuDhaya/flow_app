<div>
    Consequently two or more SMS sending has been failed. This could be because of a downtime/outage at <b>{{$data['vendor_code']}}</b> end. Decide to switch to another vendor. <br/>
    @if(isset($data['response']))
        Response<br/>
        <p><pre style="color:maroon">{{$data['response']}}</pre></p><br/><br/>
    @endif
    Thanks,<br>
    FLOW Admin
</div>
