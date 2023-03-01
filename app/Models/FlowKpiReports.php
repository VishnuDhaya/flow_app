<?php

namespace App\Models;
use App\Models\Model;
use Log;
use Illuminate\Support\Facades\DB;


class FlowKpiReports extends Model
{
	
	const INSERTABLE = ["country_code","report_date","kpi_metric","kpi_unit","kpi_value"];
	const TABLE = "flow_kpi_reports";
  const TOUCH = false;
	public function __construct($country_code)
    {
      parent::__construct($country_code);

    }

    public function model(){
        return FlowKpiReports::class;
    }


   


  }
