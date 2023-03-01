<div>
    <h3> Customer App Login Filed - Too many Attempt</h3>
    <table>
        <tr>
            <th style="text-align: left">Customer ID</th>
            <td style="text-align: center">:</td>
            <td>{{$data['cust_id']}}</td>
        </tr>
        <tr>
            <th style="text-align: left">Customer Name</th>
            <td style="text-align: center">:</td>
            <td>{{$data['cust_name']}}</td>
        </tr>
        <tr>
            <th style="text-align: left">Mobile Number</th>
            <td style="text-align: center">:</td>
            <td>{{$data['mobile_num']}}</td>
        </tr>
        <tr>
            <th style="text-align: left">Total Attempts</th>
            <td style="text-align: center">:</td>
            <td>{{$data['attempts']}}</td>
        </tr>
        <tr>
            <th style="text-align: left">Last Attempt</th>
            <td style="text-align: center">:</td>
            <td>{{$data['last_attempt']}}</td>
        </tr>
    </table>
</div>