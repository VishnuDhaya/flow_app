<div>
    @if($data['status'] == "success")
        @if(isset($data['file_id']))
            @if(isset($data['umtn_start_end_dates']))
                <h3>The Account statement for the account provider {{$data['acc_prvdr_code']}} for the month {{$data['month_year']}} {{$data['umtn_start_end_dates']}} uploaded to google drive successfully.</h3>
            @else
                <h3>The Account statement for the account provider {{$data['acc_prvdr_code']}} for the month {{$data['month_year']}} uploaded to google drive successfully.</h3>
            @endif
            <b>Click the following link to view the statement {{$data['gdrive_folder_path']}}</b>
        @else
            @if(isset($data['umtn_start_end_dates']))
                <h3>The Account statement for the account provider - {{$data['acc_prvdr_code']}} for the month {{$data['month_year']}} {{$data['umtn_start_end_dates']}} uploading failed.</h3>
            @else
                <h3>The Account statement for the account provider - {{$data['acc_prvdr_code']}} for the month {{$data['month_year']}} uploading failed.</h3>
            @endif
            <p style="color: #800000"}><b>{{$data['exception']}}</b></p>
            <p>{{$data['trace']}}</p>
        @endif
    @else
        @if(isset($data['umtn_start_end_dates']))
            <h3>The Account statement for the account provider - {{$data['acc_prvdr_code']}} for the month {{$data['month_year']}} {{$data['umtn_start_end_dates']}} import failed.</h3>
        @else
            <h3>The Account statement for the account provider - {{$data['acc_prvdr_code']}} for the month {{$data['month_year']}} import failed.</h3>
        @endif
        <p style="color: #800000"}><b>{{$data['message']}}</b></p>
    @endif
</div>
