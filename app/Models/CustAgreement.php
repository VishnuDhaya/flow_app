<?php

namespace App\Models;

use App\Models\Model;


class CustAgreement extends Model
{
   const CODE_NAME = "id";
   const TABLE = "cust_agreements";
   const INSERTABLE = ['country_code','witness_mobile_num','photo_witness_national_id','photo_witness_national_id_back', 'aggr_doc_id','product_id_csv','cust_id','status', 'valid_from', 'valid_upto','witness_name','aggr_type','duration_type', 'acc_number'];
   const UPDATABLE = ['status'];

}
