<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Planning;
use App\Models\PlanningDetail;
use App\Models\Input_item;
use App\Models\Harvest_types;
use App\Models\YieldRecord;
use Illuminate\Support\Facades\Log;

class ExportExcel implements FromArray, WithHeadings, WithTitle, WithEvents
{
    protected $header; // array
    protected $title;  // string
    protected $crop_id; // Crop instance
    protected $data; // Data instance
    protected $col_unique; // Data instance
    protected $row_matches; // Data instance

    public function __construct(array $header, string $title, array $data)
    {
        $this->header = $header;
        $this->title = $title;
        $this->data = $data;
        $this->col_unique = [];
        $this->row_matches = [];
    }

    public function headings(): array
    {
        return $this->header;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $sheet->getStyle("A1:{$highestColumn}1")->getFont()->setBold(true);


                $endColIndex = count($this->header) - 1;
                $startRow = 2;
                $lastRow = $startRow + count($this->data) - 1;

                // put fomula for total per weight
                for ($row = $startRow; $row <= $lastRow; $row++) {
                    $startColLetter = $this->getNameFromNumber(9);
                    $endColLetter = $this->getNameFromNumber($endColIndex);
                    $sumRange = "{$startColLetter}{$row}:{$endColLetter}{$row}";

                    $baseIndex = array_search('Total per Weight', $this->header); // Index เริ่มจาก 0
                    $yildsIndex = array_search('Yield', $this->header); // Index เริ่มจาก 0
                    $letterYield = $this->getNameFromNumber($yildsIndex);

                    $weightColLetter = $this->getNameFromNumber($baseIndex);
                    $formulaCell = "{$weightColLetter}{$row}";

                    // ใส่สูตร SUM ลงใน cell
                    $event->sheet->setCellValue($formulaCell, "=SUM($sumRange)*{$letterYield}{$row}");
                }

                // put fomula for total per weight
                for ($row = $startRow; $row <= $lastRow; $row++) {
                    $startColLetter = $this->getNameFromNumber(9);
                    $endColLetter = $this->getNameFromNumber($endColIndex);
                    $sumRange = "{$startColLetter}{$row}:{$endColLetter}{$row}";

                    $baseIndex = array_search('Total per Area', $this->header); // Index เริ่มจาก 0
                    $weightColLetter = $this->getNameFromNumber($baseIndex);
                    $formulaCell = "{$weightColLetter}{$row}";

                    // ใส่สูตร SUM ลงใน cell
                    $event->sheet->setCellValue($formulaCell, "=SUM($sumRange)");
                }

                $baseIndexW = array_search('Total per Weight', $this->header);
                $event->sheet->setCellValue("B".($lastRow + 1), "Total");
                for ($col = $baseIndexW; $col <= $endColIndex; $col++) {
                    $colLetter = $this->getNameFromNumber($col);
                    $formulaCellW = $colLetter.($lastRow + 1);
                    $sumRangeW = $colLetter.str(2).":".$colLetter.($lastRow);
                    $event->sheet->setCellValue($formulaCellW, "=SUM($sumRangeW)");
                }

                $summary_index = $lastRow + 4;
                foreach ($this->col_unique as $unique) {
                    $this->row_matches = [];
                    for ($row = 0; $row <= $lastRow; $row++) {
                        $type = isset($this->data[$row]['Type']) ? $this->data[$row]['Type'] : "";
                        if ($type == $unique) {
                            $this->row_matches[] = $row + $startRow; 
                        }
                    }       
                    if(isset($this->row_matches)){
                        $areaIndex = array_search('Total per Area', $this->header); // Index เริ่มจาก 0
                   
                        $event->sheet->setCellValue("B".$summary_index, "Total");
                        $event->sheet->setCellValue("C".$summary_index, "$unique");
                        $event->sheet->setCellValue("D".$summary_index, "ไร่");

                        $letter = $this->getNameFromNumber($areaIndex);
                        $endletter = $this->getNameFromNumber($endColIndex);
                        $startletter = $this->getNameFromNumber($areaIndex + 1);
              

                        for ($col = $areaIndex + 1; $col <= $endColIndex; $col++) {                            
                            $sumformula = "";
                            $colLetter = $this->getNameFromNumber($col);
                            $compressed = $this->compressRanges($this->row_matches,$colLetter);
                            foreach ($compressed as $comp) {
                                if(!empty($sumformula)){
                                    $sumformula = $sumformula." + SUM($comp)";
                                }
                                else{
                                    $sumformula = "=SUM($comp)";
                                }
                            }
                            $formulaCellW = $colLetter.($summary_index);
                            $event->sheet->setCellValue($formulaCellW, "$sumformula");
                            $event->sheet->setCellValue($letter.$summary_index, "=SUM($startletter$summary_index:$endletter$summary_index)");
                    
                        }
                    }
                    $summary_index++;
                }
           }
        ];
    }

    public function array(): array
    {
        $rows = [];
        foreach ($this->data as $item) {
            $row = [];
            foreach ($this->header as $col) {
                $row[] = $item[$col] ?? '';
                if($col == "Type"){
                    if (!in_array($item['Type'], $this->col_unique)) {
                        $this->col_unique[] = $item[$col] ?? '';
                    }
                }
            }
            $rows[] = $row;
        }
        return $rows;
    }

    public function getNameFromNumber($num): string
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intdiv($num, 26);
        if ($num2 > 0) {
            return $this->getNameFromNumber($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }

    function compressRanges(array $numbers, string $letter = ""): array
    {
        sort($numbers); // เรียงลำดับก่อน
        $ranges = [];

        $start = $numbers[0];
        $end = $start;

        for ($i = 1; $i < count($numbers); $i++) {
            if ($numbers[$i] == $end + 1) {
                $end = $numbers[$i];
            } else {
                $ranges[] = ($start == $end) ? "$letter$start" : "$letter$start:$letter$end";
                $start = $end = $numbers[$i];
            }
        }

        // เพิ่มตัวสุดท้าย
        $ranges[] = ($start == $end) ? "$letter$start" : "$letter$start:$letter$end";

        return $ranges;
    }
}
