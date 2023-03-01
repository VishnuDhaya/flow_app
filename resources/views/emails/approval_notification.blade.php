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
   <b>The following FA application is pending to your approval</b> 
   <br/><br/>
   <div>
      <table style="border-collapse: collapse;">
         <thead>
            <tr>
               <td>Customer Name</td>
               <td>Cust ID</td>
               <td>Float Advance</td>
               <td>Fee</td>
               <td>Duration</td>
               <td>Application Date </td>
            </tr>
         </thead>
         <tbody>
            <tr>
               <td>{{$data['cust_name']}}</td>
               <td>{{$data['cust_id']}}</td>
               <td>{{$data['loan_principal']}}</td>
               <td>{{$data['flow_fee']}}</td>
               <td>{{$data['duration']}}</td>
               <td>{{$data['loan_appl_date']}}</td>
            </tr>
         </tbody>
      </table>
   </div>
</body>