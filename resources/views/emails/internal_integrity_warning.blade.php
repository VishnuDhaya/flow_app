
<head>
    <style>
        thead{
            font-weight: bold;
        }
        td{
            border: 1px solid black;
        }
        tr{
            border: solid;
            border-width: 1px
        }
    </style>
</head>

<body>
The {{$data['mismatch_type']}} amounts in the Loan and Loan Transaction records were found to be inconsistent for the following FAs.
<div>

    <table style="border-collapse: collapse;">
        <thead>
            <tr>
                  <td>FA ID</td>
                  <td>Loan Amount</td>
                  <td>Loan Transaction Amount</td>
                  <td>Disbursal Date</td>
                  <td>Repayment Date</td>
                </tr>
        </thead>
        <tbody>
            @foreach($data['loans'] as $loan)
                <tr>
                  <td>{{$loan->loan_doc_id}}</td>
                  <td>{{number_format($loan->l_amt)}}</td>
                  <td>{{number_format($loan->t_amt)}}</td>
                  <td>{{$loan->disbursal_date}}</td>
                  <td>{{$loan->paid_date}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</body>
