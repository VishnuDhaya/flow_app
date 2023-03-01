@php
        $url = env('APP_URL');   
        
@endphp
<div>
    The following error occurred while importing statement for {{$data['acc_prvdr_code']}} at {{$data['failed_at']}} - ID: {{$data['import_id']}}<br/>
    <p><pre style="color:maroon">{{$data['exception']}}</pre></p> <br>
    <h4>Screenshot :</h4>
    <!-- <p>{{rtrim($url, '/').$data['screenshot_path']}}</p>   -->
    <img src="{{$message->embed(asset($data['screenshot_path']))}}" width="100%" height="100%"/>

</div>