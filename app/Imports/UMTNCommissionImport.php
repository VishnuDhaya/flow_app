<?php

namespace App\Imports;

use App\Models\Account;
use App\Models\CustCommission;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UMTNCommissionImport
{
    public $keys;
    public $years;
    public $commission_keys;
    public $accounts_records;
    public $comms_alt_acc_nums;
    public $acc_prvdr_code = "UMTN";
    public $dt_format = "M-Y";

    public function get_commission_keys(array $header)
    {

        $commission_keys = [];
        foreach ($header as $index => $value) {
            $format = $this->dt_format;
            $dt = DateTime::createFromFormat($format, $value);
            if ($dt && ($dt->format($format) === $value)) {
                $commission_key = [
                    'index' => $index,
                    'year' => $dt->format('Y'),
                    'month' => $dt->format('m')
                ];
                $commission_keys[] = $commission_key;
            }
        }
        return $commission_keys;
    }

    public function get_comms_alt_acc_nums(string $acc_prvdr_code, array $alt_acc_nums_in_sheet)
    {
        $comms_alt_acc_nums = [];
        $cust_commission_repo = new CustCommission;
        foreach ($this->years as $year) {
            $alt_acc_nums = $cust_commission_repo->get_records_by_in('alt_acc_num', $alt_acc_nums_in_sheet, ['alt_acc_num'], null, " AND acc_prvdr_code = '{$acc_prvdr_code}' AND year = '{$year}'");
            $comms_alt_acc_nums[$year] = array_column($alt_acc_nums, 'id', 'alt_acc_num');
            unset($alt_acc_nums);
        }
        return $comms_alt_acc_nums;
    }

    public function get_acc_nums_n_alt_acc_nums_in_accounts(string $acc_prvdr_code, array $alt_acc_nums_in_sheet, array $acc_nums_in_sheet)
    {
        $acc_repo = new Account;
        $accounts_alt_acc_nums = $acc_repo->get_records_by_in('alt_acc_num', $alt_acc_nums_in_sheet, ['alt_acc_num'],  null, " AND acc_prvdr_code = '{$acc_prvdr_code}'");
        $accounts_alt_acc_nums = array_unique(array_column($accounts_alt_acc_nums, 'alt_acc_num'));

        $accounts_acc_numbers = [];
        if(!empty(array_filter($acc_nums_in_sheet))) {
            $accounts_acc_numbers = $acc_repo->get_records_by_in('acc_number', $acc_nums_in_sheet, ['acc_number'], null, " AND acc_prvdr_code = '{$acc_prvdr_code}'");
            $accounts_acc_numbers = array_unique(array_column($accounts_acc_numbers, 'acc_number'));
        }

        return ["alt_acc_nums" => $accounts_alt_acc_nums, "acc_numbers" => $accounts_acc_numbers];
    }

    public function set_existing_accs_n_comms_records($rows) {

        $alt_acc_nums_in_sheet = array_unique(array_column($rows, $this->keys['alt_acc_num']));
        $acc_nums_in_sheet = array_unique(array_column($rows, $this->keys['agent_id']));

        $acc_prvdr_code = $this->acc_prvdr_code;
        $this->accounts_records = $this->get_acc_nums_n_alt_acc_nums_in_accounts($acc_prvdr_code, $alt_acc_nums_in_sheet, $acc_nums_in_sheet);
        $this->comms_alt_acc_nums = $this->get_comms_alt_acc_nums($acc_prvdr_code, $alt_acc_nums_in_sheet);
    }

    public function initialize_instance_variables(array $header)
    {
        $keys = [];
        $keys['alt_acc_num'] = 0;
        $keys['agent_id'] = 1;
        $keys['avg'] = array_key_last($header);

        $commission_keys = $this->get_commission_keys($header);
        if (empty($commission_keys)) {
            thrw("Commission keys not set");
        }

        $commission_keys = collect($commission_keys);
        $years = $commission_keys->pluck('year')->unique()->toArray();

        $this->keys = $keys;
        $this->commission_keys = $commission_keys;
        $this->years = $years;
    }

    public function should_import(array $fields, array $years)
    {
        extract($fields);
        // Check if average commission is above 40K
        if ($avg_comms >= 40000) return True;

        // Check if alt_acc_num or acc_number exists in accounts table
        $check_with_fields = ['alt_acc_nums' => $alt_acc_num];
        if ($acc_number) {
            $check_with_fields['acc_numbers'] = $acc_number;
        }
        foreach ($check_with_fields as $field => $value) {
            if (in_array($value, $this->accounts_records[$field])) return True;
        }

        // Check if alt_acc_num exists in cust_commissions table
        foreach ($years as $year) {
            if (array_key_exists($alt_acc_num, $this->comms_alt_acc_nums[$year])) return True;
        }

        return False;
    }

    public function get_commission_data(array $row, string $year)
    {
        $commission_data = [];
        $commission_keys = $this->commission_keys->where('year', $year)->toArray();
        foreach ($commission_keys as $commission_key) {
            $commission = $row[$commission_key['index']];
            $commission_data[$commission_key['month']] = $commission;
        }
        return $commission_data;
    }

    public function import_row(array &$row)
    {
        if (empty(array_filter($row))) return;

        $avg_comms = $row[$this->keys['avg']];
        $acc_number =  $row[$this->keys['agent_id']]; 
        $alt_acc_num = $row[$this->keys['alt_acc_num']];

        $years = $this->years;
        $fields = compact('acc_number', 'alt_acc_num', 'avg_comms');

        $should_import = $this->should_import($fields, $years);
        if (!$should_import) return;

        foreach ($years as $year) {
            $commission_data = $this->get_commission_data($row, $year);
            $data = [
                'country_code' => session('country_code'),
                'acc_prvdr_code' => $this->acc_prvdr_code,
                'acc_number' => $acc_number,
                'alt_acc_num' => $alt_acc_num,
                'year' => $year,
                'commissions' => $commission_data
            ];
            Log::warning($data);
            $this->upsert_commission($data);
        }
    }

    public function upsert_commission(array $data)
    {
        $alt_acc_num = $data['alt_acc_num'];
        $acc_number = $data['acc_number'];
        $year = $data['year'];
        if ($acc_number === null) {
            unset($data['acc_number']);
        }

        $comms_repo = new CustCommission;
        if (array_key_exists($alt_acc_num, $this->comms_alt_acc_nums[$year])) {
            $data['id'] = $this->comms_alt_acc_nums[$year][$alt_acc_num];
            $comms_repo->update_model($data, 'id');
        } else {
            $comms_repo->insert_model($data);
        }
    }

    public function clean_rows(array $rows) {
        // Get the index of alt_acc_num and acc_num from instance variables
        $alt_acc_num_index = $this->keys['alt_acc_num'];
        $acc_num_index = $this->keys['agent_id'];
        $na_values = [null, '', '=#N/A', '-'];
        
        $new_rows = [];
        $row_size = count($rows);

        for( $iters=0; $iters < $row_size; $iters++ ) {
        
            $new_row = [];
            $row = array_shift($rows);
            foreach($row as $index => $value) {
                
                if (in_array($value, $na_values)) $value = null;
                else {
                    switch ($index) {
                        case $alt_acc_num_index:
                            $value = (string)(int)substr(trim($value), 3);
                            break;
                        case $acc_num_index:
                            $value = (string)(int)$value;
                            $value = str_pad($value, 6, '0', STR_PAD_LEFT);
                            break;
                        default:
                            $value = (int)str_replace(',', '', $value);
                    }
                }
                $new_row[$index] = $value;
            }
            $new_rows[] = $new_row;
        }
        return $new_rows;
    }

    public function read_file(string $file_path)
    {
        $lines = file($file_path);
        if(!$lines) {
            thrw("File does not exist: $file_path");
        }
        return $lines;
    }

    public function import_csv_data(string $file_path)
    {
        $lines = $this->read_file($file_path);
        // Set the header keys
        $header_line = array_shift($lines);
        $header_row = str_getcsv($header_line);
        $this->initialize_instance_variables($header_row);
        // Group rows in chunks of 10000
        $chunks = chunkit($lines, 10000);
        unset($lines);
        
        $count = 0;
        foreach ($chunks as $chunk) {
            $chunk = array_map('str_getcsv', $chunk);
            $chunk = $this->clean_rows($chunk);
            $this->set_existing_accs_n_comms_records($chunk);
            foreach($chunk as $row) {
                $this->import_row($row);
                $count++;
            }
        }
        return $count;
    }

    public function main($file_name)
    {
        set_app_session('UGA');
        $file_path = storage_path("data/comms/$file_name");
        return $this->import_csv_data($file_path);
    }

    public function update_acc_numbers() {
        
        $acc_prvdr_code = $this->acc_prvdr_code; 
        $accounts = (new Account)->get_records_by('acc_prvdr_code', $acc_prvdr_code, ['acc_number', 'alt_acc_num'], null, 'AND alt_acc_num IS NOT NULL');
        foreach($accounts as $account) {
            $record = [
                'alt_acc_num' => $account->alt_acc_num,
                'acc_number' => $account->acc_number,
            ];
            (new CustCommission)->update_model($record, 'alt_acc_num');
        }
    }
}
