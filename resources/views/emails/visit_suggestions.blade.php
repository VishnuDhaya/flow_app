
<head>
   <style>
      thead{
      font-weight: bold;
      }
      td{
      border: 1px solid black;
      padding: 5px;
      }
      tr{
      border: solid;
      border-width: 1px
      }

   </style>
</head>
<body>
   <p><b>The Below Customers Needs Visit</b></p>
   <br/><br/>
   <div>
      <table style="border-collapse: collapse;">
         <thead>
            <tr>
               <td>Customer Name</td>
               <td>Location</td>
               <td>Last Visit Date</td>
            </tr>
         </thead>
         <tbody>
         @foreach ($data['borrowers'] as $borrower)
            <tr>
               <td>{{$borrower->first_name}} {{$borrower->last_name}}</td>
               <td>{{dd_value($borrower->location)}}</td>
               <td>{{format_date($borrower->last_visit_date)}}</td>
            </tr>
          @endforeach
         </tbody>
      </table>
   </div>
</body>