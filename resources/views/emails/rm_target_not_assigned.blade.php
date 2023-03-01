@php
        $url = env('APP_URL')
@endphp

<div>
<p>The Targets for the RMs has not been assigned yet. Ensure that the RM Targets to be assigned On or Before  {{$data['date']}}.</p>
</div>

<div>
<a href="{{$url}}assign/rm_target"> Click here </a>to update the targets for relationship managers.
</div>