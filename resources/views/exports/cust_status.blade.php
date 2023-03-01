<table>
    <thead>
        <tr>
            <th>Cust ID</th>
            <th>Middle Name</th>
            <th>KYC Status</th>
            <th>Customer Status</th>
            <th>National ID Path</th>
            
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
            <td>{{$user['cust_id']}}</td>
            <td>{{$user['middle_name']}}</td>
            <td>{{$user['kyc_status']}}</td>
            <td>{{$user['status']}}</td>
            <td><a href={{$user['national_id_path']}}>{{$user['national_id_path']}}</a></td>
            
            </tr>
        @endforeach
    </tbody>
</table>