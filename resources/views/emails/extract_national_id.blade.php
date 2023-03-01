<div>
    <b>Error Msg : </b> <p>{{$data['error_msg']}}</p>
    <br>
    <b>Raw data</b>
    @foreach($data['raw_data'] as $key => $val)
        <p>{{$key}} : {{$val}}</p>
    @endforeach
    <br>

    <b>Processed data</b>
    @foreach($data['textract'] as $key => $val)
        <p>{{$key}} : {{$val}}</p>
    @endforeach

    <b>Trace : </b> <p>{{$data['trace']}}</p>
    <br>
</div>