

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
   <b>The following FAs are Pre-approved today</b> 
   <br/><br/>
   <div>
      <table style="border-collapse: collapse;">
         <thead>
            <tr>
               <td>Customer Name</td>
               <td>Customer Mobile Number</td>
               <td>FA ID</td>
               <td>Cust ID</td>
               <td>Float Advance</td>
               <td>Fee</td>
               <td>Duration</td>
               <td>Application Date </td>
            </tr>
         </thead>
         <tbody>
         @foreach ($data['loans'] as $loan)
            <tr>
               <td>{{$loan->cust_name}}</td>
               <td>{{$loan->cust_mobile_num}}</td>
               <td>{{$loan->loan_doc_id}}</td>
               <td>{{$loan->cust_id}}</td>
               <td>{{$loan->loan_principal}}</td>
               <td>{{$loan->flow_fee}}</td>
               <td>{{$loan->duration}}</td>
               <td>{{format_date($loan->loan_appl_date)}}</td>

            </tr>
          @endforeach
         </tbody>
      </table>
   </div>
</body>