<table>
    <thead>
        <tr>
            <th>Cust ID</th>
            <th>Customer Name</th>
            <th>DP Cust ID</th>
            <th>Probation FAs</th>
            <th>Score for Existing Customers</th>
            <th>Result for Existing Customers</th>
            <th>Score for Loyalty Products</th>
            <th>Result for Loyalty Products</th>
            <th>Score for Top Ups</th>
            <th>Result for Top Ups</th>
            <th>Score for Welcome Products</th>
            <th>Result for Welcome Products</th>
            <th>Score for Float Vending</th>
            <th>Result for Float Vending</th>
            <th>Score for Probation</th>
            <th>Result for Probation</th>
            <th>First FA Date</th>
            <th>Last FA Date</th>
            <th>Total FAs</th>
            <th>Late FAs</th>
            <th>Late 1 Day FA</th>
            <th>Late 2 Day FA</th>
            <th>Late 3 Day FA</th>
            <th>Late 3 Day Plus FA</th>
            <th>Gross Value - Ontime FAs</th>
            <th>Gross Value - Repaid After 3 Days</th>
            <th>Gross Value - Number of Advances Till Now</th>
            <th>Gross Value - Gross Number Of Advances Per Quarter</th>
            <th>Gross Value - Repaid After 10 Days</th>
            <th>Gross Value - Repaid After 30 Days</th>
            <th>Ongoing FA</th>
            <th>Due date </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
            <td>{{$user['cust_id']}}</td>
            <td>{{$user['cust_name']}}</td>
            <td>{{$user['dp_cust_id']}}</td>
            <td>{{$user['prob_fas']}}</td>
            <td>{{array_key_exists('score_for_existing_customers',$user) ? $user['score_for_existing_customers'] : NULL}}</td>
            <td>{{array_key_exists('result_for_existing_customers',$user) ? $user['result_for_existing_customers'] : NULL}}</td>
            <td>{{array_key_exists('score_for_loyalty_products',$user) ? $user['score_for_loyalty_products'] : NULL}}</td>
            <td>{{array_key_exists('result_for_loyalty_products',$user) ? $user['result_for_loyalty_products'] : NULL}}</td>
            <td>{{array_key_exists('score_for_top_ups',$user) ? $user['score_for_top_ups'] : NULL}}</td>
            <td>{{array_key_exists('result_for_top_ups',$user) ? $user['result_for_top_ups'] : NULL}}</td>
            <td>{{array_key_exists('score_for_welcome_products',$user) ? $user['score_for_welcome_products'] : NULL}}</td>
            <td>{{array_key_exists('result_for_welcome_products',$user) ? $user['result_for_welcome_products'] : NULL}}</td>
            <td>{{array_key_exists('score_for_float_vending',$user) ? $user['score_for_float_vending'] : NULL}}</td>
            <td>{{array_key_exists('result_for_float_vending',$user) ? $user['result_for_float_vending'] : NULL}}</td>
            <td>{{array_key_exists('score_for_probation',$user) ? $user['score_for_probation'] : NULL}}</td>
            <td>{{array_key_exists('result_for_probation',$user) ? $user['result_for_probation'] : NULL}}</td>
            <td>{{$user['first_loan_date']}}</td>
            <td>{{$user['last_loan_date']}}</td>
            <td>{{$user['tot_loans']}}</td>
            <td>{{$user['late_loans']}}</td>
            <td>{{$user['late_1_day_loans']}}</td>
            <td>{{$user['late_2_day_loans']}}</td>
            <td>{{$user['late_3_day_loans']}}</td>
            <td>{{$user['late_3_day_plus_loans']}}</td>
            <td>{{array_key_exists('gross_ontime_loans_pc',$user) ? $user['gross_ontime_loans_pc'] : NULL}}</td>
            <td>{{array_key_exists('gross_repaid_after_3_days_pc',$user) ? $user['gross_repaid_after_3_days_pc'] : NULL}}</td>
            <td>{{array_key_exists('gross_number_of_advances_till_now',$user) ? $user['gross_number_of_advances_till_now'] : NULL}}</td>
            <td>{{array_key_exists('gross_number_of_advances_per_quarter',$user) ? $user['gross_number_of_advances_per_quarter'] : NULL}}</td>
            <td>{{array_key_exists('gross_repaid_after_10_days_pc',$user) ? $user['gross_repaid_after_10_days_pc'] : NULL}}</td>
            <td>{{array_key_exists('gross_repaid_after_30_days_pc',$user) ? $user['gross_repaid_after_30_days_pc'] : NULL}}</td>
            <td>{{array_key_exists('ongoing_fa',$user) ? $user['ongoing_fa'] : NULL}}</td>
            <td>{{array_key_exists('due_date',$user) ? $user['due_date'] : NULL}}</td>
            </tr>
        @endforeach
    </tbody>
</table>