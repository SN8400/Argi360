<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\ExportExcel;
use App\Exports\FarmerExport;
use App\Models\HarvestPlan;
use App\Models\Planning;
use App\Models\PlanningDetail;
use App\Models\PlanningDetailDate;
use App\Models\YieldRecord;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\LazyCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExportExcelController extends Controller
{
    public function testexportExcel($crop_id, $report_type)
    {
        ini_set('memory_limit', '2000M');
        ini_set('post_max_size', '128M');
        ini_set('upload_max_filesize', '128M');
        ini_set('max_input_time', '3000');
        ini_set('max_execution_time', '19000');
        $plannings = Planning::with(['crop', 'inputitem', 'harvesttype', 'planningdetails'])
                ->where('crop_id', $crop_id)
                ->get();

        $startDate = $plannings[0]->crop->startdate ?? now();

        $header = ['ID', 'Type', 'Broker', 'Areas', 'Yield', 'date', 'age', 'Total per Weight', 'Total per Area'];
        $startColumn = 9;

        $title = 'Users Report';
        for ($loopDate = $startColumn; $loopDate < 120 + $startColumn; $loopDate++) {
            $loopCalDate = $loopDate - $startColumn;
            $columnDate = Carbon::parse($startDate)->addDays($loopCalDate)->format('Y-m-d');
            $header[] = $columnDate;
        }
        $pointer = 2;
        $range = [];
        $currentItem = '';
        $currentHarvest = '';
        $rows = [];
        $planningIdList = $plannings->pluck('id')->toArray();
        $planningDetails = PlanningDetail::with('planning')
            ->whereIn('planning_id', $planningIdList)
            ->get()
            ->sortBy([
                fn($a, $b) => $a->planning->input_item_id <=> $b->planning->input_item_id,
                fn($a, $b) => $a->planning->harvest_type_id <=> $b->planning->harvest_type_id,
            ])
            ->values();

        $yeildListRaw = YieldRecord::with(['crop', 'area', 'broker', 'inputitem', 'harvesttype'])
                ->where('crop_id', $crop_id)
                ->get();

        foreach ($planningDetails as $value) {
            $yeildListRaw = YieldRecord::with(['crop', 'area', 'broker', 'inputitem', 'harvesttype'])
                ->where('crop_id', $crop_id)
                ->where('area_id', $value->area_id)
                ->where('broker_id', $value->broker_id)
                ->where('input_item_id', $value->planning->item_input_id)
                ->where('harvest_type_id', $value->planning->harvest_type_id)
                ->get();
        }

        foreach ($plannings as $planning) {
            foreach ($planning->planningdetails as $pd) {
                // หา yield ที่ match
                $yield = YieldRecord::with(['crop', 'area', 'broker', 'inputitem', 'harvesttype'])
                    ->where('crop_id', $planning->crop_id)
                    ->where('area_id', $pd->area_id)
                    ->where('broker_id', $pd->broker_id)
                    ->where('input_item_id', $planning->item_input_id)
                    ->where('harvest_type_id', $planning->harvest_type_id)
                    ->first();

                // ถ้ามี yield
                if ($yield) {
                    // เตรียม row พื้นฐาน
                    $baseRow = [
                        'ID' => $pd->id,
                        'Type' => $yield->inputitem->tradename.' | '.$yield->harvesttype->name,
                        'Broker' => $yield->broker->fname,
                        'Areas' => $yield->area->name,
                        'Yield' => $yield->rate,
                        'date' => $yield->crop->startdate.' - '.$yield->crop->enddate,
                        'age' => $pd->harvest_age,
                        'Total per Weight' => '',
                        'Total per Area' => '',
                    ];

                    // สร้างคอลัมน์ตามวันที่
                    foreach ($pd->planningDetailDates as $pdd) {
                        $col = Carbon::parse($pdd->plan_date)->format('Y-m-d');
                        $baseRow[$col] = $pdd->plan_value;
                    }

                    $pivotData[] = $baseRow;
                }
            }
        }
        // Log::info('header', ['response' => $header]);
        return Excel::download(new ExportExcel($header, $title, $pivotData), 'grow-plan-report.xlsx');
    }

    public function exportHarvestPlanByCrop($crop_id)
    {
        ini_set('memory_limit', '2000M');
        ini_set('post_max_size', '128M');
        ini_set('upload_max_filesize', '128M');
        ini_set('max_input_time', '3000');
        ini_set('max_execution_time', '19000');
        $plannings = Planning::with(['crop', 'inputitem', 'harvesttype', 'planningdetails'])
                ->where('crop_id', $crop_id)
                ->get();

        $startDate = $plannings[0]->crop->startdate ?? now();

        $header = ['ID', 'Type', 'Broker', 'Areas', 'Yield', 'date', 'age', 'Total per Weight', 'Total per Area'];
        $startColumn = 9;
        $title = 'แผนเก็บเกี่ยว';
        for ($loopDate = $startColumn; $loopDate < 120 + $startColumn; $loopDate++) {
            $loopCalDate = $loopDate - $startColumn;
            $columnDate = Carbon::parse($startDate)->addDays($loopCalDate)->format('Y-m-d');
            $header[] = $columnDate;
        }
        $pointer = 2;
        $range = [];
        $currentItem = '';
        $currentHarvest = '';
        $rows = [];
        $planningIdList = $plannings->pluck('id')->toArray();
        $planningDetails = PlanningDetail::with('planning')
            ->whereIn('planning_id', $planningIdList)
            ->get()
            ->sortBy([
                fn($a, $b) => $a->planning->input_item_id <=> $b->planning->input_item_id,
                fn($a, $b) => $a->planning->harvest_type_id <=> $b->planning->harvest_type_id,
            ])
            ->values();

        $yeildListRaw = YieldRecord::with(['crop', 'area', 'broker', 'inputitem', 'harvesttype'])
                ->where('crop_id', $crop_id)
                ->get();

        foreach ($planningDetails as $value) {
            $yeildListRaw = YieldRecord::with(['crop', 'area', 'broker', 'inputitem', 'harvesttype'])
                ->where('crop_id', $crop_id)
                ->where('area_id', $value->area_id)
                ->where('broker_id', $value->broker_id)
                ->where('input_item_id', $value->planning->item_input_id)
                ->where('harvest_type_id', $value->planning->harvest_type_id)
                ->get();
        }

        foreach ($plannings as $planning) {
            foreach ($planning->planningdetails as $pd) {
                // หา yield ที่ match
                $yield = YieldRecord::with(['crop', 'area', 'broker', 'inputitem', 'harvesttype'])
                    ->where('crop_id', $planning->crop_id)
                    ->where('area_id', $pd->area_id)
                    ->where('broker_id', $pd->broker_id)
                    ->where('input_item_id', $planning->item_input_id)
                    ->where('harvest_type_id', $planning->harvest_type_id)
                    ->first();

                // ถ้ามี yield
                if ($yield) {
                    // เตรียม row พื้นฐาน
                    $baseRow = [
                        'ID' => $pd->id,
                        'Type' => $yield->inputitem->tradename.' | '.$yield->harvesttype->name,
                        'Broker' => $yield->broker->fname,
                        'Areas' => $yield->area->name,
                        'Yield' => $yield->rate,
                        'date' => $yield->crop->startdate.' - '.$yield->crop->enddate,
                        'age' => $pd->harvest_age,
                        'Total per Weight' => '',
                        'Total per Area' => '',
                    ];

                    // สร้างคอลัมน์ตามวันที่
                    foreach ($pd->planningDetailDates as $pdd) {
                        $col = Carbon::parse($pdd->plan_harvest)->format('Y-m-d');
                        $baseRow[$col] = $pdd->plan_value * $pdd->yeild/1000;
                    }

                    $pivotData[] = $baseRow;
                }
            }
        }
        return Excel::download(new ExportExcel($header, $title, $pivotData), 'harvest-plan-report.xlsx');
    }
    
    public function exportGrowPlanByCrop($crop_id)
    {
        ini_set('memory_limit', '2000M');
        ini_set('post_max_size', '128M');
        ini_set('upload_max_filesize', '128M');
        ini_set('max_input_time', '3000');
        ini_set('max_execution_time', '19000');
        $plannings = Planning::with(['crop', 'inputitem', 'harvesttype', 'planningdetails'])
                ->where('crop_id', $crop_id)
                ->get();

        $startDate = $plannings[0]->crop->startdate ?? now();

        $header = ['ID', 'Type', 'Broker', 'Areas', 'Yield', 'date', 'age', 'Total per Weight', 'Total per Area'];
        $startColumn = 9;

        $title = 'แผนปลูก';
        for ($loopDate = $startColumn; $loopDate < 120 + $startColumn; $loopDate++) {
            $loopCalDate = $loopDate - $startColumn;
            $columnDate = Carbon::parse($startDate)->addDays($loopCalDate)->format('Y-m-d');
            $header[] = $columnDate;
        }
        $pointer = 2;
        $range = [];
        $currentItem = '';
        $currentHarvest = '';
        $rows = [];
        $planningIdList = $plannings->pluck('id')->toArray();
        $planningDetails = PlanningDetail::with('planning')
            ->whereIn('planning_id', $planningIdList)
            ->get()
            ->sortBy([
                fn($a, $b) => $a->planning->input_item_id <=> $b->planning->input_item_id,
                fn($a, $b) => $a->planning->harvest_type_id <=> $b->planning->harvest_type_id,
            ])
            ->values();

        $yeildListRaw = YieldRecord::with(['crop', 'area', 'broker', 'inputitem', 'harvesttype'])
                ->where('crop_id', $crop_id)
                ->get();

        foreach ($planningDetails as $value) {
            $yeildListRaw = YieldRecord::with(['crop', 'area', 'broker', 'inputitem', 'harvesttype'])
                ->where('crop_id', $crop_id)
                ->where('area_id', $value->area_id)
                ->where('broker_id', $value->broker_id)
                ->where('input_item_id', $value->planning->item_input_id)
                ->where('harvest_type_id', $value->planning->harvest_type_id)
                ->get();
        }

        foreach ($plannings as $planning) {
            foreach ($planning->planningdetails as $pd) {
                // หา yield ที่ match
                $yield = YieldRecord::with(['crop', 'area', 'broker', 'inputitem', 'harvesttype'])
                    ->where('crop_id', $planning->crop_id)
                    ->where('area_id', $pd->area_id)
                    ->where('broker_id', $pd->broker_id)
                    ->where('input_item_id', $planning->item_input_id)
                    ->where('harvest_type_id', $planning->harvest_type_id)
                    ->first();

                // ถ้ามี yield
                if ($yield) {
                    // เตรียม row พื้นฐาน
                    $baseRow = [
                        'ID' => $pd->id,
                        'Type' => $yield->inputitem->tradename.' | '.$yield->harvesttype->name,
                        'Broker' => $yield->broker->fname,
                        'Areas' => $yield->area->name,
                        'Yield' => $yield->rate,
                        'date' => $yield->crop->startdate.' - '.$yield->crop->enddate,
                        'age' => $pd->harvest_age,
                        'Total per Weight' => '',
                        'Total per Area' => '',
                    ];

                    // สร้างคอลัมน์ตามวันที่
                    foreach ($pd->planningDetailDates as $pdd) {
                        $col = Carbon::parse($pdd->plan_date)->format('Y-m-d');
                        $baseRow[$col] = $pdd->plan_value;
                    }

                    $pivotData[] = $baseRow;
                }
            }
        }
        // Log::info('header', ['response' => $header]);
        return Excel::download(new ExportExcel($header, $title, $pivotData), 'grow-plan-report.xlsx');
    }

    public function uploadPlan(Request $request, $crop_id = 2)
    {
        ini_set('memory_limit', '2000M');
        ini_set('post_max_size', '64M');
        ini_set('upload_max_filesize', '64M');
        ini_set('max_input_time', 300);
        ini_set('max_execution_time', 1900);
        $plannings = Planning::with(['crop', 'inputitem', 'harvesttype', 'planningdetails'])
                ->where('crop_id', $crop_id)
                ->get();

        $planningIdList = $plannings->pluck('id')->toArray();
        $planningDetails = PlanningDetail::with('planning')
            ->whereIn('planning_id', $planningIdList)
            ->get()
            ->sortBy([
                fn($a, $b) => $a->planning->input_item_id <=> $b->planning->input_item_id,
                fn($a, $b) => $a->planning->harvest_type_id <=> $b->planning->harvest_type_id,
            ])
            ->values();        

        ////////////////////////////////////////////////////////////////////////////////////////////////////////
        $file = $request->file('upload'); // <input type="file" name="upload">
        $fileName = $file->getClientOriginalName();

        $cropData = Crop::findOrFail($crop_id);
        $datac = PlanningDetail::where('crop_id', $crop_id)->count();

        // if ($request->isMethod('post')) {

            $path = $file->storeAs('excel', $fileName, 'local');

            $spreadsheet = IOFactory::load(storage_path('app/' . $path));
            $sheet = $spreadsheet->getActiveSheet();
            $rows = [];
            $dataValue = [];
            $highestRow = $datac + 2;
            $numberOfColumn = 128;
            $sowingDate = [];
            

            // อ่านวันที่จาก column
            for ($col = 9; $col <= $numberOfColumn; ++$col) {
                $cell = $sheet->getCellByColumnAndRow($col, 1)->getValue();
                if (is_numeric($cell)) {
                    $sowingDate[$col] = Carbon::instance(Date::excelToDateTimeObject($cell))->format('Y-m-d');
                } else {
                    $sowingDate[$col] = Carbon::parse(trim($cell))->format('Y-m-d');
                }
            }

        return response()->json([
            'status' => 'success',
            'message' => $fileName
        ], 200);
    }

    public function importPlanByExcel(Request $request, string $crop_id)
    {
        ini_set('memory_limit', '2000M');
        ini_set('post_max_size', '64M');
        ini_set('upload_max_filesize', '64M');
        ini_set('max_input_time', 300);
        ini_set('max_execution_time', 1900);
        $plannings = Planning::with(['crop', 'inputitem', 'harvesttype', 'planningdetails'])
                ->where('crop_id', $crop_id)
                ->get();

        $planningIdList = $plannings->pluck('id')->toArray();
        $planningDetails = PlanningDetail::with('planning')
            ->whereIn('planning_id', $planningIdList)
            ->get()
            ->sortBy([
                fn($a, $b) => $a->planning->input_item_id <=> $b->planning->input_item_id,
                fn($a, $b) => $a->planning->harvest_type_id <=> $b->planning->harvest_type_id,
            ])
            ->values();   

        $datac = $planningDetails->count();

        $file = $request->file('file'); 
        $fileName = $file->getClientOriginalName();
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $datac + 2;
        $numberOfColumn = 128;
        $sowingDate = [];
        
        // อ่านวันที่จาก column
        for ($col = 9; $col <= $numberOfColumn; ++$col) {
            $cell = $sheet->getCellByColumnAndRow($col, 1)->getValue();
            if (is_numeric($cell)) {
                $sowingDate[$col] = Carbon::instance(Date::excelToDateTimeObject($cell))->format('Y-m-d');
            } else {
                $sowingDate[$col] = Carbon::parse(trim($cell))->format('Y-m-d');
            }
        }

        $dataValue = [];
        for ($row = 3; $row <= $highestRow; ++$row) {
            $currentID = trim($sheet->getCellByColumnAndRow(1, $row)->getCalculatedValue());
            $yeild = trim($sheet->getCellByColumnAndRow(5, $row)->getCalculatedValue());
            $age = trim($sheet->getCellByColumnAndRow(7, $row)->getCalculatedValue());
            for ($col = 9; $col <= $numberOfColumn; ++$col) {
                $cellData = trim($sheet->getCellByColumnAndRow($col, $row)->getCalculatedValue());
                if (!empty($cellData)) {
                    $sowDate = $sowingDate[$col];
                    $harvestDate = Carbon::parse($sowDate)->addDays($age)->format('Y-m-d');
                    $dataValue[$currentID][$sowDate] = [
                        'value' => $cellData,
                        'age' => $age,
                        'harvest' => $harvestDate,
                        'yeild' => $yeild
                    ];
                }
            }
        }

        $version = Carbon::now()->format('ymd_Hi');
        // Disable crop ก่อน (ตาม logic เดิม)
        PlanningDetailDate::where('crop_id', $crop_id)->update(['status' => 0]);
        foreach ($dataValue as $planningDetailId => $datePlans) {
            foreach ($datePlans as $planDate => $values) {
                $plan = new PlanningDetailDate();
                $plan->crop_id = $crop_id;
                $plan->version = $version;
                $plan->planning_detail_id = $planningDetailId;
                $plan->plan_date = $planDate;
                $plan->plan_value = $values['value'] ?? null;
                $plan->age = $values['age'] ?? null;
                $plan->plan_harvest = $values['harvest'] ?? null;
                $plan->yeild = $values['yeild'] ?? null;
                $plan->status = 1;
                $plan->createdBy = auth()->id() ?? 1;
                $plan->modifiedBy = auth()->id() ?? 1;
                $plan->save();
            }
        }
        
        return response()->json([
            'status' => 'success',
            'message' => $fileName
        ], 200);
    }
  
    public function exportFarmerByCrop(Request $request, string $crop_id)
    {
        ini_set('memory_limit', '2000M');
        ini_set('post_max_size', '128M');
        ini_set('upload_max_filesize', '128M');
        ini_set('max_input_time', '3000');
        ini_set('max_execution_time', '19000');
        $filename = 'farmer_' . now()->format('ymd_His') . '.xlsx';
        

        return Excel::download(new FarmerExport($crop_id), $filename);
    }

}
