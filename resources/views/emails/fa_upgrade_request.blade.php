<div>
    <h3>Upgrade Float Advance for the below Customer</h3>

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
            <td style="font-weight: 700; padding-top: 10px; font-size:16px " colspan="2"> Request For Float Upgrade</td>
        </tr> 
        <tr>
            <th style="text-align: left">Current Eligible FA Limit</th>
            <td style="text-align: center">:</td>
            <td>{{$data['elig_fa_limit']}} UGX</td>
        </tr>
        <tr>
            <th style="text-align: left">Requested upgrade amount</th>
            <td style="text-align: center">:</td>
            <td>{{$data['upgrade_amt']}} UGX</td>
        </tr>
    </table>
</div>
