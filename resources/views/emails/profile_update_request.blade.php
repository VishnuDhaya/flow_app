<div>
    <h3> Request to correct customer KYC </h3>
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
            <td style="text-align: center; font-size: 16px; font-weight: 700; padding-top: 10px " colspan="2"> Require KYC correction in</td>
        </tr>
        <tr>
            <th style="text-align: left">Section</th>
            <td style="text-align: center">:</td>
            <td>{{$data['sctn_list']}}</td>
        </tr>
        <tr>
            <th style="text-align: left">Customer review</th>
            <td style="text-align: center">:</td>
            <td>{{$data['cust_cmnts']}}</td>
        </tr>

    </table>
</div>