<div class="col-12">
    @if (session()->has('result'))
        <div class="noresult">
            <div id = "alert" class="alert alert-success w-90">
                {{ session('result') }}
            </div>
        </div>
    @endif
@if($results)
<h1 class="leadResultheader">RESULT</h1>
<div class="leadResultcontainerBox">
    <div class="leadResultscrollable">
        <div class="row no-margin">
            @foreach($results as $result)
                <div class="col-md-4">
                        <div class="leadLabelbox" onclick="openProfile({{$result['id']}})">
                            <div class ="{{$result['profile_status']}}" >Closed</div>
                            <h5 class="no-margin">{{$result['biz_name']}}</h5>
                            <label class="">Acoount num:</label> <span class="text-white">{{$result['account_num']}}</span><br/>
                            <label class=" ">Status:</label> <span class="text-white">{{get_tf_status($result['tf_status'])}}</span>
                        </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
    @endif
</div>
<script>
    function openProfile(id) {
        Livewire.emit('homeProfile',id);
        $('#exampleModal').modal('show')
    }
    window.addEventListener('lead-deleted', event => {
        $('#exampleModal').modal('hide')
    })
</script>