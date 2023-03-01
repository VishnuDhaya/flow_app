<?php 

namespace App\Services\Support;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Excel;
use Log;

class ExcelExportService implements FromCollection,WithHeadings{
	use Exportable;

	public function __construct($data, $headers, $base_dir){
		$this->data = $data;
		$this->headers = $headers;
		$this->base_dir = $base_dir;
    }

	public function collection(){
		return collect($this->data);
	}

	public function headings(): array {
		return $this->headers;
	}

	public function export($file_name_prefix){
		//Log::warning("exporting reports");
        #$excel_file = new ExcelExportService($data, $header);
        $now = datetime_db();
        $file_name = $file_name_prefix."_$now.xlsx";
        $exported = Excel::store($this, separate([$this->base_dir, $file_name_prefix."_$now.xlsx"]));
        if($exported){
        	//Log::warning($file_name);
        	return $file_name; 
        }
    }
}
