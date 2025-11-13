<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HarvestPlan;
use App\Models\Crops;
use App\Models\Areas;
use App\Models\Brokers;
use App\Models\Input_item;
use App\Models\Sowing;
use App\Models\User_farmer;
use App\Models\Qa_group_details;
use App\Models\Harvest_types;
use App\Models\Import_qa_results;
use App\Models\ImportWeightSAP;
use App\Models\ImportQcSAP;
use App\Models\HarvestMoveQ;
use App\Models\HarvestLocks;
use App\Helpers\RoleHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HarvestPlansController extends Controller
{
    public function index(string $crop_id)
    {
        $results = collect();
        $crop = Crops::findOrFail($crop_id);
        $areaList = Areas::pluck('name', 'id');
        $brokerList = Brokers::all();
        $inputItemList = Input_item::all();

        // หารายการ sowing ที่ต้อง ignore
        $ignoreSowingList = Sowing::where('crop_id', $crop_id)
            ->where('harvest_status', 'เสียหายทั้งหมด')
            ->pluck('id')
            ->toArray();

        // ดึงวันที่ที่มีข้อมูล estimate
        $hdate = HarvestPlan::where('crop_id', $crop_id)
            ->distinct()
            ->orderBy('date_est')
            ->pluck('date_est')
            ->toArray();

        // ดึงข้อมูลรายวันแบบ summary
        $harvestPlanData = HarvestPlan::selectRaw('
                crop_id,
                date_est,
                count(id) as count_value,
                ROUND(sum(value_est)/1000,2) as est_value,
                ROUND(sum(value_bf_harvest)/1000,2) as bf_value,
                ROUND(sum(value_act)/1000,2) as act_value
            ')
            ->where('crop_id', $crop_id)
            ->groupBy('crop_id', 'date_est')
            ->orderBy('date_est')
            ->get();

        $dateList = $harvestPlanData->pluck('date_est')->unique()->toArray();

        // ดึงข้อมูล detail
        $detailRaw = HarvestPlan::selectRaw('
                date_est,
                input_item_id,
                mat_type,
                ROUND(sum(value_est)/1000,2) as est_value
            ')
            ->where('crop_id', $crop_id)
            ->whereIn('date_est', $dateList)
            ->groupBy('date_est', 'input_item_id', 'mat_type')
            ->get();

        // ดึงข้อมูลชั่งจริงจาก raw SQL
        $dateListStr = implode("','", $dateList);
        $realWgSql = "
            SELECT 
                harvest_plans.date_est,
                harvest_plans.input_item_id,
                harvest_plans.mat_type,
                ROUND(SUM(import_weight_saps.net_wg)/1000, 2) AS real_value
            FROM harvest_plans
            LEFT JOIN import_weight_saps ON import_weight_saps.harvest_plan_id = harvest_plans.id
            WHERE harvest_plans.crop_id = ?
            AND harvest_plans.date_est IN ('$dateListStr')
            GROUP BY harvest_plans.date_est, harvest_plans.input_item_id, harvest_plans.mat_type
        ";

        $weightRaw = DB::select($realWgSql, [$crop_id]);

        // แปลงข้อมูลเป็น array สำหรับแสดงผล
        $detailData = [];
        foreach ($detailRaw as $item) {
            $detailData[$item->date_est][$item->input_item_id][trim($item->mat_type)] = $item->est_value;
        }

        $detail2Data = [];
        $sum2Data = [];
        foreach ($weightRaw as $item) {
            $detail2Data[$item->date_est][$item->input_item_id][trim($item->mat_type)] = $item->real_value;
            $sum2Data[$item->date_est] = ($sum2Data[$item->date_est] ?? 0) + $item->real_value;
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'harvestPlanData'=> $harvestPlanData,
                'areaList'=> $areaList,
                'brokerList'=> $brokerList,
                'inputItemList'=> $inputItemList,
                'detailData'=> $detailData,
                'crop'=> $crop,
                'detail2Data'=> $detail2Data,
                'sum2Data'=> $sum2Data
            ]
        ]);
    }

    public function detailDate($crop_id, $date, $type = 'N')
    {
        $crop = Crops::find($crop_id);

        $areaList = Areas::all();
        $brokerList = Brokers::all();
        $inputItemList = Input_item::all();

        $harvestPlanData = HarvestPlan::selectRaw('
                harvest_plans.crop_id,
                harvest_plans.area_id,
                harvest_plans.broker_id,
                brokers.code,
                harvest_plans.input_item_id,
                harvest_plans.date_est,
                harvest_plans.age,
                harvest_plans.mat_type,
                sowings.start_date,
                COUNT(harvest_plans.id) as count_value,
                SUM(harvest_plans.value_est) as est_value,
                SUM(harvest_plans.value_bf_harvest) as bf_value,
                SUM(harvest_plans.value_act) as act_value
            ')
            ->leftJoin('brokers', 'brokers.id', '=', 'harvest_plans.broker_id')
            ->leftJoin('sowings', 'sowings.id', '=', 'harvest_plans.sowing_id')
            ->where('harvest_plans.crop_id', $crop_id)
            ->where('harvest_plans.date_est', $date)
            ->where('sowings.harvest_status', '<>', 'เสียหายทั้งหมด')
            ->groupBy(
                'harvest_plans.crop_id',
                'harvest_plans.area_id',
                'harvest_plans.broker_id',
                'brokers.code',
                'harvest_plans.input_item_id',
                'harvest_plans.date_est',
                'harvest_plans.age',
                'harvest_plans.mat_type',
                'sowings.start_date'
            )
            ->orderBy('brokers.code')
            ->orderByDesc('harvest_plans.age')
            ->orderBy('harvest_plans.broker_id')
            ->orderBy('harvest_plans.date_est')
            ->orderBy('harvest_plans.input_item_id')
            ->orderBy('harvest_plans.area_id')
            ->get();

        $realWeightRaw = DB::table('harvest_plans')
            ->selectRaw('
                harvest_plans.crop_id,
                harvest_plans.area_id,
                harvest_plans.broker_id,
                brokers.code,
                harvest_plans.input_item_id,
                harvest_plans.date_est,
                harvest_plans.age,
                harvest_plans.mat_type,
                sowings.start_date,
                ROUND(SUM(import_weight_saps.net_wg)/1000, 2) as real_value
            ')
            ->leftJoin('import_weight_saps', 'import_weight_saps.harvest_plan_id', '=', 'harvest_plans.id')
            ->leftJoin('brokers', 'brokers.id', '=', 'harvest_plans.broker_id')
            ->leftJoin('sowings', 'sowings.id', '=', 'harvest_plans.sowing_id')
            ->where('harvest_plans.crop_id', $crop_id)
            ->where('harvest_plans.date_est', $date)
            ->where('sowings.harvest_status', '<>', 'เสียหายทั้งหมด')
            ->groupBy(
                'harvest_plans.crop_id',
                'harvest_plans.area_id',
                'harvest_plans.broker_id',
                'brokers.code',
                'harvest_plans.input_item_id',
                'harvest_plans.date_est',
                'harvest_plans.age',
                'harvest_plans.mat_type',
                'sowings.start_date'
            )
            ->get();

        $sum2Data = [];
        $totalsum = 0;

        foreach ($realWeightRaw as $value) {
            $sum2Data[$value->crop_id][$value->area_id][$value->broker_id][$value->code][$value->input_item_id][$value->date_est][$value->age][trim($value->mat_type)][$value->start_date] = $value->real_value;
            $totalsum += $value->real_value;
        }

        return response()->json([
            'harvestPlanData' => $harvestPlanData,
            'areaList' => $areaList,
            'brokerList' => $brokerList,
            'inputItemList' => $inputItemList,
            'crop' => $crop,
            'date' => $date,
            'type' => $type,
            'sum2Data' => $sum2Data,
            'totalsum' => $totalsum
        ]);
    }

    public function farmerSumDetails(Request $request, $crop_id, $date, $area_id, $broker_id, $input_item_id, $matType, $viewtype = 'N') {
        $user = Auth::user();

        $crop = Crops::find($crop_id);
        $areaList = Areas::pluck('name', 'id');
        $brokerList = Brokers::all();
        $inputItemList = Input_item::all();

        $stkcodeList = [
            1 => ['N' => '1RW3101-070023N', 'C' => '1RW3100-010010N'],
            2 => ['N' => '1RW3100-010020N'],
            3 => ['N' => '1RW3101-010020N'],
        ];

        $stkcode = $stkcodeList[$input_item_id][$matType] . "','1RW3101-020010N";

        $harvestPlanData = HarvestPlan::with(['farmer', 'sowing'])
            ->select(
                'crop_id',
                'area_id',
                'broker_id',
                'id',
                'input_item_id',
                'farmer_id',
                'sowing_id',
                'mat_type',
                'date_est',
                'age',
                'delivery_status',
                'value_est',
                'value_bf_harvest',
                'value_act'
            )
            ->where([
                'crop_id' => $crop_id,
                'date_est' => $date,
                'area_id' => $area_id,
                'broker_id' => $broker_id,
                'input_item_id' => $input_item_id,
                'mat_type' => $matType,
            ])
            ->orderBy('date_est')
            ->orderBy('input_item_id')
            ->orderBy('area_id')
            ->get();

        $sowingList = $harvestPlanData->pluck('sowing_id')->unique()->toArray();
        $farmerList = $harvestPlanData->pluck('farmer_id')->unique()->toArray();

        $userFarmerListRaw = Sowing::whereIn('id', $sowingList)
            ->select('id', 'user_farmer_id')
            ->get();

        $userFarmerIds = $userFarmerListRaw->pluck('user_farmer_id')->unique();

        $userFarmerRaw = User_farmer::with('farmer')
            ->whereIn('id', $userFarmerIds)
            ->get();

        $sowingData = [];
        foreach ($userFarmerListRaw as $item) {
            $sowingData[$item->user_farmer_id] = $item;
        }

        $userFarmerData = [];
        $farmerData = [];
        foreach ($userFarmerRaw as $item) {
            $sowing = $sowingData[$item->id] ?? null;
            if ($sowing) {
                $userFarmerData[$sowing->id] = $item;
                $farmerData[$item->farmer_id] = $item->farmer->citizenid ?? null;
            }
        }

        $accessList = ['all'];
        if ($user->group->name === 'ae') {
            $accessList = User_farmer::checkAccess($farmerList, $user->id, $crop_id)
                ->pluck('farmer_id')->toArray();
        } elseif ($user->group->name === 'ae_manager') {
            $accessList = User_farmer::checkAccess($farmerList, $user->id, $crop_id, 'manager')
                ->pluck('farmer_id')->toArray();
        }

        $qaDataRaw = Qa_group_details::with('qa_master')
            ->whereIn('sowing_id', $sowingList)
            ->select('sowing_id', 'qa_master_id')
            ->get();

        $qaData = [];
        foreach ($qaDataRaw as $qa) {
            $qaData[$qa->sowing_id][] = $qa;
        }

        $sqlText = "
            SELECT
                TbDailyTruckD.f_name,
                TbDailyTruckD.farmer_id AS f_id,
                TbDailyTruckD.stkcode,
                TbDailyTruckD.stkdesc,
                TbDailyTruckD.pack_short,
                SUM(TbDailyTruckD.kgd_s) AS kgd_s,
                TbDailyTruckD.pack_weight,
                SUM(TbDailyTruckD.kgd_r) AS kgd_r,
                SUM(TbDailyTruckD.qultity) AS qultity
            FROM tbDailyTruck_H AS TbDailyTruckH
            LEFT JOIN tbDailyTruck_D AS TbDailyTruckD
                ON TbDailyTruckH.rcv_no = TbDailyTruckD.rcv_no
            WHERE TbDailyTruckH.rcv_datetime BETWEEN ? AND ?
            AND TbDailyTruckD.farmer_id IN (" . implode(',', array_map(fn($id) => "'$id'", array_keys($farmerData))) . ")
            AND TbDailyTruckD.stkcode IN ('{$stkcode}')
            GROUP BY
                TbDailyTruckD.f_name,
                TbDailyTruckD.farmer_id,
                TbDailyTruckD.stkcode,
                TbDailyTruckD.stkdesc,
                TbDailyTruckD.pack_short,
                TbDailyTruckD.pack_weight
        ";

        $dataLoad = DB::select($sqlText, [
            "$date 00:00:00",
            "$date 23:59:00",
        ]);

        return response()->json([
            'harvestPlanData' => $harvestPlanData,
            'areaList' => $areaList,
            'brokerList' => $brokerList,
            'inputItemList' => $inputItemList,
            'crop' => $crop,
            'date' => $date,
            'qaData' => $qaData,
            'accessList' => $accessList,
            'userFarmerData' => $userFarmerData,
            'viewtype' => $viewtype,
            'dataLoad' => $dataLoad,
        ]);
    }

    public function farmerDetails(Request $request, $crop_id, $date, $area_id, $broker_id, $input_item_id, $age, $matType, $viewtype = 'N') 
    {
        // $user = Auth::user();
        $crop = Crops::find($crop_id);
        $areaList = Areas::pluck('name', 'id');
        $brokerList = Brokers::all();
        $inputItemList = Input_item::all();
        $harvestTypeList = Harvest_types::all()->pluck('name', 'code');

        $canEdit = true;
        $canEditToday = date('Y-m-d') === $date;

        $harvestPlanData = HarvestPlan::with(['farmer', 'sowing'])
            ->where([
                'crop_id' => $crop_id,
                'date_est' => $date,
                'area_id' => $area_id,
                'broker_id' => $broker_id,
                'input_item_id' => $input_item_id,
                'age' => $age,
                'mat_type' => $matType,
            ])
            ->whereHas('sowing', function ($q) {
                $q->where('harvest_status', '<>', 'เสียหายทั้งหมด');
            })
            ->orderBy('date_est')
            ->orderBy('input_item_id')
            ->orderBy('area_id')
            ->limit(100)
            ->get();

        $sowingList = $harvestPlanData->pluck('sowing_id')->unique()->toArray();
        $farmerList = $harvestPlanData->pluck('farmer_id')->unique()->toArray();
        $harvestList = $harvestPlanData->pluck('id')->toArray();

        $listMoveRaw = HarvestMoveQ::with('harvestPlan')
            ->whereIn('harvest_plan_id', $harvestList)
            ->where('status', 'W')
            ->get();

        $listMove = $listMoveRaw->keyBy('harvest_plan_id');

        $sowingRaw = Sowing::whereIn('id', $sowingList)->get(['id', 'user_farmer_id']);

        $sowingData = [];
        foreach ($sowingRaw as $s) {
            $sowingData[$s->user_farmer_id][] = $s;
        }

        $userFarmerIds = $sowingRaw->pluck('user_farmer_id')->unique()->toArray();
        $userFarmerRaw = User_farmer::with('farmer')->whereIn('id', $userFarmerIds)->get();

        $userFarmerData = [];
        $farmerData = [];
        foreach ($userFarmerRaw as $u) {
            foreach ($sowingData[$u->id] ?? [] as $s) {
                $userFarmerData[$s->id] = $u;
                $farmerData[$u->farmer_id] = $u->farmer->citizenid ?? null;
            }
        }
        //          Log::info('user', ['response' => $user]);
        //         $accessList = ['all'];
        //         if (\App\Helpers\RoleHelper::getGroupByRole($user->group_id) === 'User') {
        //             $accessList = User_farmer::checkAccess($farmerList, $user->id, $crop_id)
        //                 ->pluck('farmer_id')->toArray();
        //         } else {
        //             $accessList = User_farmer::checkAccess($farmerList, $user->id, $crop_id, 'manager')
        //                 ->pluck('farmer_id')->toArray();
        //         }

        $qaDataRaw = Qa_group_details::with('qa_master')
            ->whereIn('sowing_id', $sowingList)
            ->get();

        $qaData = [];
        foreach ($qaDataRaw as $qa) {
            $qaData[$qa->sowing_id][] = $qa;
        }

        $dataWeight = ImportWeightSAP::whereIn('harvest_plan_id', $harvestList)
            ->select('harvest_plan_id', DB::raw('SUM(net_wg) as sumnetwg'))
            ->groupBy('harvest_plan_id')
            ->pluck('sumnetwg', 'harvest_plan_id');

        $dataQcRaw = ImportQcSAP::whereIn('harvest_plan_id', $harvestList)
            ->select('harvest_plan_id', 'total_defect_sum_perc', DB::raw('COUNT(id) as countcar'))
            ->groupBy('harvest_plan_id', 'total_defect_sum_perc')
            ->get();

        $dataQc = [];
        foreach ($dataQcRaw as $q) {
            $dataQc[$q->harvest_plan_id] = [
                'percent' => $q->total_defect_sum_perc,
                'car' => $q->countcar
            ];
        }

        $importQaResultShowRW = Import_qa_results::where('crop_id', $crop_id)
            ->whereIn('sowing_id', $sowingList)
            ->where('import_status', 'LinkedWebShow')
            ->orderBy('id')
            ->get();

        $importQaResultShow = [];
        foreach ($importQaResultShowRW as $r) {
            $importQaResultShow[$r->sowing_id]['QA_DATA'][] = $r->mic_description . ' ' . $r->result;
        }

        // Fetch material mapping data via raw SQL
        $sqlMatData = "
            SELECT 
                hp.id,
                CASE
                    WHEN hp.custommat != '' THEN hp.custommat
                    WHEN mm.matcode = 'RGKN075HX' AND (b.code = '3103' OR b.code = '3104') AND hp.note != '' THEN hp.note
                    WHEN hp.mat_type = 'R' AND hp.note != '' THEN hp.note
                    WHEN hp.qa_grade != '' THEN hp.qa_grade
                    ELSE mm.matcode
                END AS matcode
            FROM harvest_plans hp
            LEFT JOIN sowings s ON hp.sowing_id = s.id
            LEFT JOIN user_farmer uf ON uf.id = s.user_farmer_id
            LEFT JOIN farmers f ON f.id = uf.farmer_id
            LEFT JOIN brokers b ON b.id = uf.broker_id
            LEFT JOIN map_matcode mm ON mm.crop_id = hp.crop_id
                AND mm.input_item_id = hp.input_item_id
                AND mm.harvest_by = hp.havest_by
                AND mm.harvest_to = hp.mat_type
                AND mm.broker_id = uf.broker_id
            WHERE hp.id IN (" . implode(',', $harvestList) . ")
        ";

        $mapMatWithHarvestPlan = collect(DB::select($sqlMatData))
            ->pluck('matcode', 'id');

        return response()->json([
            'harvestTypeList' => $harvestTypeList,
            'listMove' => $listMove,
            'harvestPlanData' => $harvestPlanData,
            'areaList' => $areaList,
            'brokerList' => $brokerList,
            'inputItemList' => $inputItemList,
            'crop' => $crop,
            'date' => $date,
            'qaData' => $qaData,
            // 'accessList' => $accessList,
            'userFarmerData' => $userFarmerData,
            'viewtype' => $viewtype,
            'canEdit' => $canEdit,
            'canEditToday' => $canEditToday,
            'dataWeight' => $dataWeight,
            'dataQc' => $dataQc,
            'importQaResultShow' => $importQaResultShow,
            'mapMatWithHarvestPlan' => $mapMatWithHarvestPlan,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $harvestPlans = HarvestPlan::with(['crop', 'area', 'harvestType', 'seedcode', 'head', 'ae', 'inputitem', 'sowing', 'farmer', 'broker', 'harvestMoveQ'])->find($id);

        $date = $harvestPlans->date_est;
        $canEditDay   = Carbon::parse("$date 05:00:00")->gt(now()) ? '' : 'disabled';
        $canEditFirst = Carbon::parse("$date 09:58:00")->gt(now()) ? '' : 'disabled';
        $canEditLast  = Carbon::parse("$date 17:58:00")->gt(now()) ? '' : 'disabled';

        $harvestDataCount = HarvestPlan::find($id)
        ->where('sowing_id', $harvestPlans->sowing_id)
        ->count();
        $harvestTypes = Harvest_types::all();

        return response()->json([
            'status' => 'success',
            'data' => $harvestPlans,
            'harvestTypes' => $harvestTypes,
            'harvestDataCount' => $harvestDataCount,
            'canEditDay' => $canEditDay,
            'canEditFirst' => $canEditFirst,
            'canEditLast' => $canEditLast
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function separate(Request $request, string $id, string $type)
    {
        $requestData = $request->all();
        $harvestPlan = HarvestPlan::find($id);
        $harvestMoveQ = HarvestMoveQ::where('crop_id', $requestData['crop_id'])
        ->where('harvest_plan_id', $id)
        ->where('sowing_id', $harvestPlan->sowing_id)
        ->where('status', 'W')
        ->count();
        if($harvestMoveQ == 0){
            if($requestData['change_date'] != $harvestPlan->date_est){
                $startDate = Carbon::parse($harvestPlan->sowing->start_date);
                $toDate  = Carbon::parse($requestData['change_date']);
                $ageInDays = $startDate->diffInDays($toDate);

                $requestData['to_date'] = $toDate;
                $requestData['age'] = $ageInDays;
                $requestData['type'] = $type;
                $requestData['crop_id'] = $harvestPlan->crop_id;
                $requestData['harvest_plan_id'] = $harvestPlan->id;
                $requestData['sowing_id'] = $harvestPlan->sowing_id;
                $requestData['from_date'] = $harvestPlan->date_est;
                $requestData['havest_by'] = $requestData['havest_by'];
                $requestData['modifiedBy'] = auth()->id() ?? 1;
                $requestData['value_est'] = $requestData['value_est'] * 1000;
                $requestData['value_bf_harvest'] = $requestData['value_bf_harvest'] * 1000;
                $requestData['value_act'] = $requestData['value_act'] * 1000;
                $harvestMoveQ = HarvestMoveQ::create($requestData);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $harvestMoveQ
        ]);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, string $type)
    {

        $harvestPlan = HarvestPlan::find($id);
        $date = $harvestPlan->date_est;
        $canEditDay   = Carbon::parse("$date 05:00:00")->gt(now()) ? '' : 'disabled';
        $canEditFirst = Carbon::parse("$date 09:58:00")->gt(now()) ? '' : 'disabled';
        $canEditLast  = Carbon::parse("$date 17:58:00")->gt(now()) ? '' : 'disabled';

        if($type == 'delivery'){
            $harvestPlan->delivery_status = $request->delivery_status;
            $harvestPlan->save();
        }elseif($type == 'reject'){
            if ($request->input('reject_status') == 'yes') {
                $harvestPlan->reject_status = 'yes';
                $harvestPlan->reject_note = $request->input('reject_note');
            } else {
                $harvestPlan->reject_status = null;
                $harvestPlan->reject_note = null;
            }
            $harvestPlan->save();
        }elseif($type == 'manualMat'){
            if ($request->input('custommat')) {
                $harvestPlan->custommat = $request->input('custommat');
            } else {
                $harvestPlan->custommat = null;
            }
            $harvestPlan->save();
        }elseif($type == 'UpdateData'){
            $dataPost = $request->all();
            $dateChange = $harvestPlan->date_est;
            if ($canEditDay == "") {
                $dateChange = $dataPost->date_est;
            } 
            //  Log::info('user', ['dataPost' => $dataPost]);
            $LocksData = HarvestLocks::where('crop_id', $dataPost['crop_id'])
            ->where('lock_date', $dateChange)
            ->first();
            $canChangeDate = false;
            if(isset($LocksData->status) && $LocksData->status == 'U'){
                $canChangeDate = true;
            }
            $dataPost['age'] = $harvestPlan->age;
            if (($harvestPlan->date_est != $dateChange)  && $canChangeDate) {
                $datetime1 = Carbon::parse($harvestPlan->sowing->start_date);
                $datetime2 = Carbon::parse($dateChange);
                $agedate = $datetime1->diffInDays($datetime2);
                $dataPost['age'] = $agedate;
            }

            if (empty($harvestPlan->run_no)) {
                $lastRun = HarvestPlan::where('crop_id', $harvestPlan->crop_id)
                    ->orderByDesc('run_no')
                    ->value('run_no');

                $dataPost['run_no'] = $lastRun ? $lastRun + 1 : 1;
            }

            $harvestPlan->update($dataPost);

            // $harvestPlan = HarvestPlan::with('sowing')->findOrFail($id);
            // $dateChange = $request->input('date') ?? $harvestPlan->date_est;

            // $harvestPlan->save();
        }else{
            return response()->json([
                'status' => 'error',
                'data' => null
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $harvestPlan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


}
