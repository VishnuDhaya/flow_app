
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
Here is the latest funds utilization report.
<div>

    <table style="border-collapse: collapse;">
        <thead>
            <tr>
                  <td>Fund Name</td>
                  <td>Fund Code</td>
                  <td>Allocated Amount</td>
                  <td>Fund Utilization Percent</td>
                </tr>
        </thead>
        <tbody>
            @foreach($funds as $fund)
                <tr>
                  <td>{{$fund->fund_name}}</td>
                  <td>{{$fund->fund_code}}</td>
                  <td>{{number_format($fund->alloc_amount_fc)}}</td>
                  <td>{{number_format($fund->utilization_perc * 100)}}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

</body>
