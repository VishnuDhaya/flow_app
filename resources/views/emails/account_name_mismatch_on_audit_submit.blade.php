<div>
    <b><p>Audior has approved KYC for a lead with name mismatch</p></b>
    <br/>
    <b>Lead information</b>
    @if(isset($data['third_party_owner_name']))
    <p>Third Party A/C Holder Name (As per N/W Prvdr Records): {{$data['holder_name']}}</p>
    <p>Third Party A/C Holder Name (As per National ID): {{$data['third_party_owner_name']}}</p>
    @else
    <p>Account Holder Name (As per N/W Prvdr Records): {{$data['holder_name']}}</p>
    <p>Owner Name (As per National ID): {{$data['national_id_name']}}</p>
    @endif
    <p>Business Name (As entered by RM): {{$data['biz_name']}}</p>
    <p>Lead ID: {{$data['lead_id']}}</p>
    <br/>
    <b>Please refer to the link below to see the details of the lead.</b>
    <br/>
    <a href="{{$data['app_url']}}/lead/audit_kyc/{{$data['lead_id']}}">Click Here</a>
    <br/>
    <p>For clarifications contact {{$data['auditor_mail']}}</p>
    <br/>
    <p>Thankyou.</p>
    
</div>