<?php

use Illuminate\Database\Seeder;
use App\Services\LoanService;
use App\Repositories\SQL\BorrowerRepositorySQL;
class BorrowerCategory extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $loan_serv = new LoanService();  
      $borrowers = DB::select("select id,tot_loans,late_loans,prob_fas from borrowers");
      foreach ($borrowers as  $borrower) {
         $borrower_id = $borrower->id;
         $category = $loan_serv->get_cust_category($borrower);
         DB::table('borrowers')->where('id',$borrower_id)->update(['category' => $category]);
      }
    }
}
