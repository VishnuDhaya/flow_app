<?php

use Illuminate\Database\Seeder;
use App\Services\LoanService;
use App\Repositories\SQL\BorrowerRepositorySQL;
class Sprint_12_borrower_category extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    $loan_serv = new LoanService();  
    $borrowers = DB::select("select id,tot_loans,late_loans from borrowers");
    foreach ($borrowers as  $borrower) {
       $borrower_id = $borrower->id;
       $category = $loan_serv->get_cust_category($borrower);
       DB::table('borrowers')->where('id',$borrower_id)->update(['category' => $category]);
      }
    }
}
