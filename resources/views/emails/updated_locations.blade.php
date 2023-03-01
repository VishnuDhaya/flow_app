

<head>
   <style>
      thead{
      font-weight: bold;
      }
      td,th{
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
   <b>The below Locations are added from {{$data['start_date']}} to {{$data['end_date']}}</b> 
   <br/><br/>
   <div>
      <table style="border-collapse: collapse;">
         <thead>
            <tr>
               <th colspan="4">Locations</th>
            </tr>
            <tr>
               <th>No</th>
               <th>RM Name</th>
               <td>Data Code</td>
               <td>Data Value</td>
            </tr>

         </thead>
         <tbody>
         @foreach ($data['locations'] as $key=>$value)
            <tr>
               <td>{{$key+1}}</td>
               <td>{{$value->rm_name}}</td>
               <td>{{$value->data_code}}</td>
               <td>{{$value->data_value}}</td>

            </tr>
          @endforeach
         </tbody>
      </table>
   </div>
</body>