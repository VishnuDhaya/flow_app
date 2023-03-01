<head>
    <style>
        b{
            font-size: 15px;
        }
    </style>
</head>
<body>
    <div>
        <p>Hello Team,</p>
        <p>Note that the current balance at {{$data['current_datetime']}} for {{$data['acc_prvdr_code']}} A/C # {{$data['acc_number']}} is <b>{{number_format($data['balance'])}} {{$data['currency_code']}}</b>.
            Please take IMMEDIATE ACTION, So that the disbursal will not be affected.</p>
        <p>Thanks</p>
        <p>Flow Admin</p>
    </div>
</body>
