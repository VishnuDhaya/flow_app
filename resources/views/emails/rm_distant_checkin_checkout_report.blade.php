

<head>
   <style>
      thead{
      font-weight: bold;
      }
      td{
      border: 1px solid black;
      padding: 4px;
      }
      tr{
      border: solid;
      border-width: 1px
      }
     
   </style>
</head>
<body>
   <b>Below is the distant check-in & check-out report for {{$data['rm_name']}} for last week ({{$data['start_date']}} to {{$data['end_date']}})</b> 
   <br/><br/>
   <div>
      <table style="border-collapse: collapse;">
         <thead>
            <tr>
               <td>RM Name</td>
               <td>Total Visits</td>
               <td>Distant Checkin Count</td>
               <td>Distant Checkin Reason</td>
               <td>Distant Checkout Count</td>
               <td>Distant Checkout Reason</td>
               <td>Early Checkout Count</td>
               <td>Early Checkout Reason</td>
               <td>After Biz hours Checkin Count</td>
               <td>After Biz hours Checkin Reason</td>

            </tr>
         </thead>
         <tbody>
            <tr>
               <td>{{$data['rm_name']}}</td>
               <td>{{$data['total_visits']}}</td>
               <td>{{$data['force_checkin_count']}}</td>
               
              
                <td>@if(is_array($data['checkin_reason']))
                        @foreach ($data['checkin_reason'] as $checkin_reason)
                            <span>{{$checkin_reason}}</span><br/>
                        @endforeach
                    @else
                        {{$data['checkin_reason']}}
                    @endif
                </td>
                <td>{{$data['force_checkout_count']}}</td>

                <td>@if(is_array($data['checkout_reason']))
                        @foreach ($data['checkout_reason'] as $checkout_reason)
                            <span>{{$checkout_reason}}</span><br/>
                        @endforeach
                    @else
                        {{$data['checkout_reason']}}
                    @endif
                </td>

                <td>{{$data['early_checkout_count']}}</td>

               <td>@if(is_array($data['early_checkout_reason']))
                     @foreach ($data['early_checkout_reason'] as $early_checkout_reason)
                           <span>{{$early_checkout_reason}}</span><br/>
                     @endforeach
                  @else
                     {{$data['early_checkout_reason']}}
                  @endif
               </td>

               <td>{{$data['after_biz_hrs_chkin_count']}}</td>

               <td>@if(is_array($data['after_biz_hrs_chkin_reason']))
                     @foreach ($data['after_biz_hrs_chkin_reason'] as $after_biz_hrs_chkin_reason)
                           <span>{{$after_biz_hrs_chkin_reason}}</span><br/>
                     @endforeach
                  @else
                     {{$data['after_biz_hrs_chkin_reason']}}
                  @endif
               </td>

            </tr>
         </tbody>
      </table>
   </div>
</body>