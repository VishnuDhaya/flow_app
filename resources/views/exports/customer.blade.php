<table>
    <thead>
        <tr>
            <th>Cust ID</th>
            <th>DP Cust ID</th>
            <th>Business name</th>
            <th>Flow - Firstname</th>
            <th>Flow - Lastname</th>
            <th>CCA - Firstname</th>
            <th>CCA - Lastname</th>
            <th>Agent Phonenumber</th>
            <th>Merchant Number</th>
            
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
            <td>{{$user['cust_id']}}</td>
            <td>{{$user['dp_cust_id']}}</td>
            <td>{{$user['biz_name']}}</td>
            <td>{{$user['first_name']}}</td>
            <td>{{$user['last_name']}}</td>
            <td>{{$user['firstName']}}</td>
            <td>{{$user['lastName']}}</td>
            <td>{{$user['agentPhoneNumber']}}</td>
            <td>{{$user['merchantNumber']}}</td>
            
            </tr>
        @endforeach
    </tbody>
</table>