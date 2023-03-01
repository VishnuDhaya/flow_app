<div>
    <p>Please verify whether the Account Holder Name matches with the name on the image attached below.</p>
    <br/>
    <b>Lead information</b>
    <p>Account Holder Name (As per N/W Prvdr Records): {{$data['holder_name']}}</p>
    <p>Account Number: {{$data['acc_num']}}</p>
    <br/>
    <div class="row image "> 
        <div class="col-sm-2 col-md-3">                                       
            <p class="no-margin viewCus_label">HOLDER NAME PROOF UPLOADED BY THE AUDITOR</p>
            <img class= "image" width=50% height=50% src="{{$message->embed($data['full_path'])}}" />	
        </div>
    </div>
    <br/>
    <b>Additional Lead information</b>
    <br/>
    <p>Business Name (As entered by RM): {{$data['biz_name']}}</p>
    <p>Owner Name (As per National ID): {{$data['national_id_name']}}</p>
    <br/>
    <p>If you find the names don't match please inform {{$data['ops_admin_email']}} about this.</p>
    <br/>
    
</div>
