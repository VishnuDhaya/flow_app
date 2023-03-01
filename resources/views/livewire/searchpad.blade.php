<div class="col-md-4 no-padding">
    <div class="leadSearchsidecontainerBoxshadow">
        <div class="scrollableLeadlistsPad">
            <div class="leadSearchboxContainer">
                <div class="leadSearchbox">
                    <input type="text" wire:model="searchterm" placeholder="Lead name / Account Number / SC Code" class="leadSearch w-90">
                    <i class="fa fa-search"></i>
                </div>
            </div>
            <div class="scrollableLeadlists">
                @foreach($leads as $lead)
                <div class="leadLabelbox" wire:click="$emit('homeProfile',{{$lead->id}})">
                    <div class ="{{$lead->profile_status}}" >Closed</div>
                    <div class="d-flex justify-content-between"><h5 class="no-margin">{{$lead->biz_name}}</h5><p class="datetime">{{date_format(date_create($lead->created_at),"H:i d/m/Y");}}</p></div>
                    <label class="">Acc. num :</label> <span class="text-white">{{$lead->account_num}}</span><br/>
                    <label class=" ">Status &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :</label> <span class="text-white">{{get_tf_status($lead->tf_status)}}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>