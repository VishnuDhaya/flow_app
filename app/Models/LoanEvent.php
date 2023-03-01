<?php

namespace App\Models;

use App\Models\Model;


class LoanEvent extends Model
{
   const TABLE = "loan_events";

   const INSERTABLE = ['loan_doc_id','event_type', 'created_at', 'event_data'];

   // const INSERTABLE = ['country_code','currency_code','org_id', 'head_person_id'];	

 
}


  
        