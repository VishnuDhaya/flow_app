@php
	$url = rtrim(config('app.url'), '/')
@endphp
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
      tbody tr td{
      text-align: right;
      }
   </style>
</head>
<body>
   <b>The following FA Waiver Request is pending to your approval</b> 
   <br/><br/>
   <div>
      <table style="border-collapse: collapse;">
         <thead>
            <tr>
               <td>FA ID</td>
               <td>OS Penalty</td>
               <td>Requested Waiver Amount</td>
               <td>Late For</td>
               <td> Requested On</td>
            </tr>
         </thead>
         <tbody>
            <tr>
            <a href="{{$url}}/fa/view/{{$data['loan_doc_id']}}" target="_blank">{{$data['loan_doc_id']}}</a>
               <td>{{$data['os_penalty']}}</td>
               <td>{{$data['requested_amount']}}</td>
               <td>{{$data['penalty_days']}} Days</td>
               <td>{{$data['requested_on']}}</td>
            </tr>
         </tbody>
      </table>
   </div>
</body>