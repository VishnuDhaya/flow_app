<?php

namespace App\Services\Vendors\File;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelWriter
{

    /**
     * The SpreadSheet instance.
     *
     * @var \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    private $spreadsheet;

    /**
     * Freeze the header.
     *
     * @var bool
     */
    private $freeze_header = True;

    /**
     * Bold the header.
     *
     * @var bool
     */
    private $bold_header = True;

    /**
     * Set autosize for columns.
     *
     * @var bool
     */
    private $autosize_columns = True;

    /**
     * Create a new ExcelWriter instance.
     * @param array $configs Configs to stylize the sheet
     * 
     */
    public function __construct(array $configs = [])
    {
        $this->initialize_spreadsheet();
        if (!empty($configs)) $this->set_configs($configs);
    }

    /**
     * Set the configs.
     *
     * @param array $configs
     * 
     */
    public function set_configs(array $configs)
    {
        foreach($configs as $config_name => $config_value) {
            if (isset($configs[$config_name])) {
                $this->{$config_name} = $config_value;
            }
        }
    }

    /**
     * Remove the active sheet from the Spreadsheet instance.
     *
     */
    private function remove_active_sheet()
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $this->spreadsheet->removeSheetByIndex($this->spreadsheet->getIndex($sheet));
    }

    /**
     * Create a new Spreadsheet instance.
     *
     */
    private function initialize_spreadsheet()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->remove_active_sheet();
    }

    /**
     * Create a new WorkSheet instance.
     *
     */
    private function create_worksheet($sheet_name = null)
    {
        if ($sheet_name) {
            return new Worksheet($this->spreadsheet, "$sheet_name");
        } else {
            return new Worksheet($this->spreadsheet);
        }
    }

    /**
     * Set autosize for columns.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $sheet
     * @param array $rows
     * 
     */
    private function set_column_auto_size(Worksheet $sheet, array $rows)
    {
        $row = array_values(reset($rows));
        foreach ($row as $column_index => $cell_value) {
            $column_name = Coordinate::stringFromColumnIndex($column_index + 1);
            $sheet->getColumnDimension($column_name)->setAutoSize(true);
        }
    }

    /**
     * Set necessary sheet styles.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $sheet
     * @param array $rows
     * 
     */
    private function stylize_sheet(Worksheet $sheet, array $rows)
    {
        $highestColumnName = $sheet->getHighestColumn();

        if($this->freeze_header) $sheet->freezePane("A2");
        if($this->bold_header) $sheet->getStyle("A1:{$highestColumnName}1")->getFont()->setBold(true);
        if($this->autosize_columns) $this->set_column_auto_size($sheet, $rows);
    }

    /**
     * Insert data to the worksheet.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $sheet
     * @param array $rows
     * @param array|null $header
     * 
     */
    private function insert(Worksheet $sheet, array $rows, $header)
    {
        $rows = array_values($rows);
        if (!isset($header)) {
            $header = array_keys(reset($rows));
        }

        // Set Header
        $header_row = 1;
        foreach ($header as $column_index => $column_name) {
            $sheet->setCellValueByColumnAndRow($column_index + 1, $header_row, $column_name);
        }
        //Set Rows
        foreach ($rows as $row_index => $row) {
            $column_index = 1;
            foreach ($row as $cell_value) {
                $sheet->setCellValueByColumnAndRow($column_index, $row_index + 2, $cell_value);
                $column_index++;
            }
        }
        $this->stylize_sheet($sheet, $rows);
    }

    /**
     * Perform the write operations.
     *
     * @param string $sheet_name The name to set for the sheet
     * @param array $rows Rows to set in the sheet.
     * @param array|null $header Header to set or the keys from the rows will be set.
     * 
     */
    public function write(string $sheet_name, array $rows, array $header = null)
    {
        $worksheet = $this->create_worksheet($sheet_name);
        $this->spreadsheet->addSheet($worksheet);
        $sheet = $this->spreadsheet->setActiveSheetIndexByName($sheet_name);
        $this->insert($sheet, $rows, $header);
    }

    /**
     * Save the Spreadsheet.
     *
     * @param string $file_path
     * 
     */
    public function save(string $file_path)
    {
        $writer = new Xlsx($this->spreadsheet);
        $writer->save($file_path);
    }
}
