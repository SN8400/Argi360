<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tmp_schedules;
use App\Models\Brokers;
use App\Models\Tmp_schedule_plans;
use App\Models\Tmp_schedule_plan_details;
use App\Models\Input_item;
use App\Models\PlanScheduleDetail;
use App\Models\PlanSchedule;
use App\Models\Broker_areas;
use App\Models\User_farmer;
use App\Models\Sowing;
use App\Models\PlanAct;
use App\Models\PlanActDetail;
use Illuminate\Support\Facades\DB;

class TemplatePlansController extends Controller
{
    public function index()
    {
        $plants = Tmp_schedules::orderBy('modified', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $plants
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'details' => 'required|string|max:1000',
            'broker_id' => 'required|integer|exists:brokers,id',
        ]);

        $newSchedule = Tmp_schedules::create([
            'name' => $request->name, 
            'broker_id' => $request->broker_id,
            'details' => $request->details, 
            'status' => 1,
        ]);

        // ดึงข้อมูล Tmp_schedule_plans และรายละเอียดทั้งหมด
        $clonePlans = Tmp_schedule_plans::with('tmp_schedule_plan_details')
            ->where('tmp_schedule_id', $newSchedule->id)
            ->get();

            foreach ($clonePlans as $plan) {
                // Clone Plan
                $newPlan = Tmp_schedule_plans::create([
                    'tmp_schedule_id' => $newSchedule->id,
                    'input_item_id' => $plan->input_item_id,
                    'code' => $plan->code,
                    'main_code' => $plan->main_code,
                    'name' => $plan->name,
                    'details' => $plan->details,
                    'day' => $plan->day,
                    'priority' => $plan->priority,
                    'can_review' => $plan->can_review,
                    'can_photo' => $plan->can_photo,
                ]);

                // Clone Plan Details
                foreach ($plan->tmp_schedule_plan_details as $detail) {
                    Tmp_schedule_plan_details::create([
                        'tmp_schedule_plan_id' => $newPlan->id,
                        'chemical_id' => $detail->chemical_id,
                        'value' => $detail->value,
                        'unit_id' => $detail->unit_id,
                        'p_value' => $detail->p_value,
                        'p_unit_id' => $detail->p_unit_id,
                        'ctype' => $detail->ctype,
                        'rate' => $detail->rate,
                        'set_group' => $detail->set_group,
                    ]);
                }
            }

        return response()->json([
            'status' => 'success',
            'message' => 'Plant template created successfully.'
        ], 201);
    }

    public function showmanage($id)
    {
        // $plans = Tmp_schedule_plans::find($id);
        $plans = Tmp_schedule_plans::with(['Input_item', 'Tmp_schedule_plan_details'])->where('id', $id)->first();

        if (!$plans) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $plans]);
    }

    public function show($id)
    {
        $plans = Tmp_schedule_plans::with(['Tmp_schedule_plan_details', 'Input_item', 'Tmp_schedules'])
            ->where('tmp_schedule_id', $id)
            ->orderBy('input_item_id')
            ->orderBy('day')
            ->get();

        $schedule = Tmp_schedules::find($id);
        
        $inputItems = Input_item::whereHas('tmp_schedule_plans', function ($query) use ($id) {
            $query->where('tmp_schedule_id', $id);
        })->get();
        
        if (!$plans) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $plans, 'inputItems' => $inputItems, 'schedule' => $schedule]);
    }

    public function viewdetail($id)
    {
        $schedule = Tmp_schedules::with([
            'tmp_schedule_plans.input_item',
            'tmp_schedule_plans.tmp_schedule_plan_details.chemical',
            'tmp_schedule_plans.tmp_schedule_plan_details.unit',
            'tmp_schedule_plans.tmp_schedule_plan_details.punit',
        ])
        ->where('id', $id)
        ->first();
        if (!$schedule) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }
        $tmpname = $schedule->name ?? '';

        $data = [];

        foreach ($schedule->tmp_schedule_plans as $plan) {
            foreach ($plan->tmp_schedule_plan_details as $detail) {
                if ($detail->value === null) {
                    continue; // ข้ามถ้า value เป็น null
                }

                $tradename = $plan->input_item->tradename ?? 'N/A';
                $agedate = $plan->day ?? 0;
                $set_group = $detail->set_group ?? 0;

                $data[$tradename][$agedate][$set_group][] = [
                    'tmpname'       => $tmpname,
                    'tradename'     => $tradename,
                    'actname'       => $plan->name,
                    'agedate'       => $agedate,
                    'chem_id'       => $detail->chemical->id ?? null,
                    'chem_name'     => $detail->chemical->name ?? null,
                    'use_value'     => $detail->value,
                    'unit_name'     => $detail->unit->name ?? null,
                    'pic_value'     => $detail->p_value,
                    'pic_unit_name' => $detail->punit->name ?? null,
                    'set_group'     => $set_group,
                    'ctype'        => $detail->ctype,
                    'rate'         => $detail->rate,
                ];
            }
        }

        // ส่งข้อมูลไป view หรือ json ตามต้องการ
        return response()->json([
            'status' => 'success',
            'tmpname' => $tmpname,
            'data' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $plant = Tmp_schedules::find($id);

        if (!$plant) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'details' => 'nullable|string|max:1000',
        ]);

        $plant->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Plant template updated successfully.',
            'data' => $plant
        ]);
    }

    public function destroy($id)
    {
        $plant = Tmp_schedules::find($id);

        if (!$plant) {
            return response()->json(['status' => 'error', 'message' => 'Not found'], 404);
        }

        $plant->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Plant template deleted successfully.'
        ]);
    }

    public function clone(Request $request)
    {
        $validated = $request->validate([
            'schedules_id' => 'required|integer',
            'crop_id' => 'required|integer|exists:crops,id',
            'broker_id' => 'nullable|integer|exists:brokers,id',
            'input_item_id' => 'nullable|integer|exists:input_items,id',
        ]);

        $schedules_id = $request->schedules_id ?? null;
        $crop_id = $request->crop_id ?? null;
        $broker_id = $request->broker_id ?? null;
        $input_item_id = $request->input_item_id ?? null;

        if ($crop_id && $broker_id && $input_item_id) {
            $this->__cloneAndCleanWithBrokerAndInputItem($schedules_id, $crop_id, $broker_id, $input_item_id);
            $this->__remapdatawithinputis($crop_id, $broker_id, $input_item_id);
            $this->addandremoveplan($crop_id, $broker_id);
        } elseif ($crop_id && $broker_id) {
            $this->__cloneAndCleanWithBroker($schedules_id, $crop_id, $broker_id);
            $this->__remapdata($crop_id, $broker_id);
            $this->addandremoveplan($crop_id, $broker_id);
        } elseif ($crop_id) {
            $brokerList = Brokers::all();
            foreach ($brokerList as $broker_id => $value) {
                $this->__cloneAndCleanWithBroker($schedules_id, $crop_id, $broker_id);
                $this->__remapdata($crop_id, $broker_id);
                $this->addandremoveplan($crop_id, $broker_id);
            }
        }



        return response()->json([
            'status' => 'success',
            'message' => 'Plant template updated successfully.',
            'data' => $request
        ]);
    }

    private function addandremoveplan($crop_id, $broker_id) {
        // ดึงข้อมูลผู้ปลูก (UserFarmer) ตาม crop และ broker ที่กำหนด
        $userFarmers = User_farmer::where('crop_id', $crop_id)
            ->where('broker_id', $broker_id)
            ->get();

        $userFarmerIds = $userFarmers->pluck('id');

        // ดึงข้อมูลการหว่านเมล็ด
        $sowings = Sowing::whereIn('user_farmer_id', $userFarmerIds)->get()->keyBy('id');

        // ดึงข้อมูลตารางแผนการเพาะปลูก (PlanSchedule) ของ crop และ broker
        $planSchedules = PlanSchedule::with('planScheduleDetails')
            ->where('crop_id', $crop_id)
            ->where('broker_id', $broker_id)
            ->orderBy('day')
            ->get();

        // จัดข้อมูล PlanSchedule เป็น 2 array เพื่อให้เข้าถึงง่าย
        $planScheduleData = [];
        $planScheduleById = [];
        foreach ($planSchedules as $ps) {
            $planScheduleData[$ps->input_item_id][$ps->day] = $ps;
            $planScheduleById[$ps->id] = $ps;
        }

        $planActDataFlag = [];

        // สำหรับแต่ละการหว่านเมล็ด
        foreach ($sowings as $sowing) {
            // ดึง PlanAct ของ sowing
            $planActs = PlanAct::where('sowing_id', $sowing->id)->get();

            // ตรวจสอบตาม schedule
            if (isset($planScheduleData[$sowing->input_item_id])) {
                foreach ($planScheduleData[$sowing->input_item_id] as $day => $planSchedule) {
                    $flagHave = 0;
                    $id = $planSchedule->id;

                    foreach ($planActs as $planAct) {
                        if ($day == $planAct->age) {
                            $flagHave = $planAct->plan_schedule_id > 0 ? $planAct->plan_schedule_id : -1;
                            $id = $planAct->id;
                            break;
                        }
                    }

                    $planActDataFlag[$sowing->id][$day] = [
                        'flag' => $flagHave,
                        'id' => $id
                    ];
                }
            }

            // ตรวจสอบ PlanAct ที่ไม่อยู่ใน schedule
            foreach ($planActs as $planAct) {
                if (!isset($planScheduleData[$sowing->input_item_id][$planAct->age])) {
                    $planActDataFlag[$sowing->id][$planAct->age] = [
                        'flag' => -2,
                        'id' => $planAct->id
                    ];
                }
            }
        }

        // ดำเนินการเพิ่ม/ลบ PlanAct
        foreach ($planActDataFlag as $sowingId => $days) {
            foreach ($days as $day => $data) {
                if ($data['flag'] === 0) {
                    $planSchedule = $planScheduleById[$data['id']];
                    $sowing = $sowings[$sowingId];

                    $planAct = PlanAct::create([
                        'crop_id' => $planSchedule->crop_id,
                        'sowing_id' => $sowingId,
                        'plan_schedule_id' => $planSchedule->id,
                        'plan_code' => $planSchedule->main_code,
                        'farmer_id' => $sowing->farmer_id,
                        'priority' => $planSchedule->priority,
                        'age' => $planSchedule->day,
                        'createdBy' => 1,
                        'modifiedBy' => 1,
                        'plan_date' => now()->addDays($planSchedule->day)->format('Y-m-d'),
                    ]);

                    foreach ($planSchedule->planScheduleDetails as $detail) {
                        PlanActDetail::create([
                            'plan_act_id' => $planAct->id,
                            'chemical_id' => $detail->chemical_id,
                            'value' => $detail->value,
                            'unit_id' => $detail->unit_id,
                            'cal_val' => $detail->value * $sowing->current_land,
                            'rate' => $detail->rate,
                            'ctype' => $detail->ctype,
                            'set_group' => $detail->set_group
                        ]);
                    }

                } elseif ($data['flag'] === -2) {
                    PlanActDetail::where('plan_act_id', $data['id'])->delete();
                    PlanAct::where('id', $data['id'])->delete();
                }
            }
        }
    }

    private function __remapdatawithinputis($crop_id, $broker_id, $input_item_id)
    {
        // ดึง PlanSchedule แบบมีเงื่อนไข
        $planschedules = PlanSchedule::where('crop_id', $crop_id)
            ->where('broker_id', $broker_id)
            ->where('input_item_id', $input_item_id)
            ->orderBy('day')
            ->get()
            ->keyBy('id'); // key by id for lookup later

        // SQL raw query ตาม logic CakePHP (แปลง %d เป็น binding)
        $sqlBase = "
            select 
                newplan.id as newid,
                plan_schedules.id as oldscheduleid,
                plan_acts.plan_schedule_id as plan_schedule_id,
                plan_acts.id as actid,
                sowings.current_land as current_land
            from plan_acts
            left join plan_schedules on plan_schedules.id = plan_acts.plan_schedule_id
            left join sowings on sowings.id = plan_acts.sowing_id
            left join user_farmer on user_farmer.id = sowings.user_farmer_id
            left join plan_schedules as newplan
                on newplan.crop_id = plan_acts.crop_id
                and newplan.input_item_id = sowings.input_item_id
                and newplan.broker_id = user_farmer.broker_id
                and newplan.day = plan_acts.age
            where plan_acts.crop_id = ?
                and user_farmer.broker_id = ?
                and sowings.input_item_id = ?
                and plan_schedules.id is null
        ";

        $rawdata = DB::select($sqlBase, [$crop_id, $broker_id, $input_item_id]);

        foreach ($rawdata as $value) {
            $tmpData = PlanAct::with('plan_act_details')->find($value->actid);
            if ($tmpData) {
                // ลบข้อมูล plan_act_details เดิม
                PlanActDetail::where('plan_act_id', $tmpData->id)->delete();

                // กำหนด plan_schedule_id ใหม่
                $tmpData->plan_schedule_id = $value->newid;
                $tmpData->save();

                // copy plan_act_details จาก plan_schedules เดิม
                $plandetails = PlanScheduleDetail::where('plan_schedule_id', $value->oldscheduleid)->get();
                foreach ($plandetails as $plandetail) {
                    $planActDetail = new PlanActDetail();
                    $planActDetail->plan_act_id = $tmpData->id;
                    $planActDetail->chemical_id = $plandetail->chemical_id;
                    $planActDetail->value = $plandetail->value;
                    $planActDetail->unit_id = $plandetail->unit_id;
                    $planActDetail->p_value = $plandetail->p_value;
                    $planActDetail->p_unit_id = $plandetail->p_unit_id;
                    $planActDetail->rate = $plandetail->rate;
                    $planActDetail->ctype = $plandetail->ctype;
                    $planActDetail->set_group = $plandetail->set_group;
                    $planActDetail->save();
                }
            }
        }
    }

    private function __cloneAndCleanWithBrokerAndInputItem($tmp_id, $crop_id, $broker_id, $input_item_id)
    {
        // ลบข้อมูลเก่าของ PlanScheduleDetail และ PlanSchedule
        PlanScheduleDetail::whereHas('planSchedule', function($query) use ($crop_id, $broker_id, $input_item_id) {
            $query->where('crop_id', $crop_id)
                ->where('broker_id', $broker_id)
                ->where('input_item_id', $input_item_id);
        })->delete();

        PlanSchedule::where('crop_id', $crop_id)
            ->where('broker_id', $broker_id)
            ->where('input_item_id', $input_item_id)
            ->delete();

        // ดึง BrokerArea ตาม crop_id และ broker_id
        $allBase = Broker_areas::select('broker_id', 'area_id')
            ->where('crop_id', $crop_id)
            ->where('broker_id', $broker_id)
            ->groupBy('broker_id', 'area_id')
            ->get();

        // ดึง TmpSchedulePlan ตาม tmp_schedule_id และ input_item_id
        $allTemplate = Tmp_schedule_plans::with('tmp_schedule_plan_details')
            ->where('tmp_schedule_id', $tmp_id)
            ->where('input_item_id', $input_item_id)
            ->get();

        foreach ($allBase as $area) {
            foreach ($allTemplate as $templateData) {
                $planSchedule = new PlanSchedule();
                $planSchedule->crop_id = $crop_id;
                $planSchedule->input_item_id = $templateData->input_item_id;
                $planSchedule->code = $templateData->code;
                $planSchedule->main_code = $templateData->main_code;
                $planSchedule->name = $templateData->name;
                $planSchedule->details = $templateData->details;
                $planSchedule->day = $templateData->day;
                $planSchedule->area_id = $area->area_id;
                $planSchedule->broker_id = $area->broker_id;
                $planSchedule->priority = $templateData->priority;
                $planSchedule->can_review = $templateData->can_review;
                $planSchedule->can_photo = $templateData->can_photo;
                $planSchedule->save();

                foreach ($templateData->tmp_schedule_plan_details as $templateDetailData) {
                    $planScheduleDetail = new PlanScheduleDetail();
                    $planScheduleDetail->plan_schedule_id = $planSchedule->id;
                    $planScheduleDetail->chemical_id = $templateDetailData->chemical_id;
                    $planScheduleDetail->value = $templateDetailData->value;
                    $planScheduleDetail->unit_id = $templateDetailData->unit_id;
                    $planScheduleDetail->p_value = $templateDetailData->p_value;
                    $planScheduleDetail->p_unit_id = $templateDetailData->p_unit_id;
                    $planScheduleDetail->rate = $templateDetailData->rate;
                    $planScheduleDetail->ctype = $templateDetailData->ctype;
                    $planScheduleDetail->set_group = $templateDetailData->set_group;
                    $planScheduleDetail->save();
                }
            }
        }
    }

    private function __cloneAndCleanWithBroker($tmp_id, $crop_id, $broker_id)
    {
        // 🔥 1. ลบข้อมูลเก่า (PlanScheduleDetail และ PlanSchedule)
        $scheduleIds = PlanSchedule::where('crop_id', $crop_id)
            ->where('broker_id', $broker_id)
            ->pluck('id');

        PlanScheduleDetail::whereIn('plan_schedule_id', $scheduleIds)->delete();
        PlanSchedule::whereIn('id', $scheduleIds)->delete();

        // 🧩 2. ดึงพื้นที่ทั้งหมดของ broker
        $allBase = Broker_areas::where('crop_id', $crop_id)
            ->where('broker_id', $broker_id)
            ->select('broker_id', 'area_id')
            ->groupBy('broker_id', 'area_id')
            ->get();

        // 📑 3. ดึง template ทั้งหมดที่เกี่ยวกับ tmp_id
        $allTemplate = Tmp_schedule_plans::with('details') // ต้องมี relation 'details'
            ->where('tmp_schedule_id', $tmp_id)
            ->get();

        // 🔁 4. วนลูปสร้าง PlanSchedule และ PlanScheduleDetail
        foreach ($allBase as $area) {
            foreach ($allTemplate as $template) {
                // ✅ สร้าง PlanSchedule ใหม่
                $schedule = new PlanSchedule();
                $schedule->crop_id = $crop_id;
                $schedule->input_item_id = $template->input_item_id;
                $schedule->code = $template->code;
                $schedule->main_code = $template->main_code;
                $schedule->name = $template->name;
                $schedule->details = $template->details;
                $schedule->day = $template->day;
                $schedule->area_id = $area->area_id;
                $schedule->broker_id = $area->broker_id;
                $schedule->priority = $template->priority;
                $schedule->can_review = $template->can_review;
                $schedule->can_photo = $template->can_photo;
                $schedule->save();

                // ✅ สร้าง PlanScheduleDetail หลายรายการ
                $details = [];
                foreach ($template->details as $detail) {
                    $details[] = new PlanScheduleDetail([
                        'plan_schedule_id' => $schedule->id,
                        'chemical_id' => $detail->chemical_id,
                        'value' => $detail->value,
                        'unit_id' => $detail->unit_id,
                        'p_value' => $detail->p_value,
                        'p_unit_id' => $detail->p_unit_id,
                        'rate' => $detail->rate,
                        'ctype' => $detail->ctype,
                        'set_group' => $detail->set_group
                    ]);
                }

                $schedule->details()->saveMany($details); // 🔁 ต้องมี relation details() ใน PlanSchedule
            }
        }
    }

    private function __remapdata($crop_id, $broker_id)
    {
        // ดึง PlanSchedule ทั้งหมดที่ตรงกับเงื่อนไข
        $planscheduleraw = PlanSchedule::where('crop_id', $crop_id)
            ->where('broker_id', $broker_id)
            ->orderBy('day')
            ->with('planScheduleDetails') // สมมุติชื่อ relation
            ->get();

        // สร้างแผนที่ id => planScheduleObj
        $planscheduledata2 = [];
        foreach ($planscheduleraw as $planSchedule) {
            $planscheduledata2[$planSchedule->id] = $planSchedule;
        }

        // Query แบบ raw ที่ดัดแปลงเป็น Laravel ORM
        $rawdata = DB::table('plan_acts')
            ->leftJoin('plan_schedules', 'plan_schedules.id', '=', 'plan_acts.plan_schedule_id')
            ->leftJoin('sowings', 'sowings.id', '=', 'plan_acts.sowing_id')
            ->leftJoin('user_farmer', 'user_farmer.id', '=', 'sowings.user_farmer_id')
            ->leftJoin('plan_schedules as newplan', function($join) {
                $join->on('newplan.crop_id', '=', 'plan_acts.crop_id')
                    ->on('newplan.input_item_id', '=', 'sowings.input_item_id')
                    ->on('newplan.broker_id', '=', 'user_farmer.broker_id')
                    ->on('newplan.day', '=', 'plan_acts.age');
            })
            ->select(
                'newplan.id as newid',
                'plan_schedules.id as oldscheduleid',
                'plan_acts.plan_schedule_id',
                'plan_acts.id as actid',
                'sowings.current_land'
            )
            ->where('plan_acts.crop_id', $crop_id)
            ->where('user_farmer.broker_id', $broker_id)
            ->whereNull('plan_schedules.id')
            ->get();

        foreach ($rawdata as $value) {
            // โหลด PlanAct พร้อมรายละเอียด
            $tmpData = PlanAct::with('planActDetails')->find($value->actid);

            if ($tmpData) {
                // สร้างแผนที่ chemical_id => detail
                $tmpDetailData = [];
                foreach ($tmpData->planActDetails as $detail) {
                    $tmpDetailData[$detail->chemical_id] = $detail;
                }

                // อัปเดต plan_schedule_id ใหม่
                $tmpData->plan_schedule_id = $value->newid;
                $tmpData->save();

                // เช็คถ้ามี PlanScheduleDetail
                if (isset($planscheduledata2[$value->newid]) && $planscheduledata2[$value->newid]->planScheduleDetails) {
                    foreach ($planscheduledata2[$value->newid]->planScheduleDetails as $planscheduleDetailObj) {
                        if (!isset($tmpDetailData[$planscheduleDetailObj->chemical_id])) {
                            // สร้าง PlanActDetail ใหม่
                            $tmpsub = new PlanActDetail();
                            $tmpsub->chemical_id = $planscheduleDetailObj->chemical_id;
                            $tmpsub->value = $planscheduleDetailObj->value;
                            $tmpsub->unit_id = $planscheduleDetailObj->unit_id;
                            $tmpsub->cal_val = $planscheduleDetailObj->value * $value->current_land;
                            $tmpsub->rate = $planscheduleDetailObj->rate;
                            $tmpsub->ctype = $planscheduleDetailObj->ctype;
                            $tmpsub->set_group = $planscheduleDetailObj->set_group;
                            $tmpsub->plan_act_id = $value->actid;
                            $tmpsub->save();
                        }
                    }
                }
            }
        }

        // ส่วนที่ 2: กรณี plan_code = 'H'

        $rawdata = DB::table('plan_acts')
            ->leftJoin('plan_schedules', 'plan_schedules.id', '=', 'plan_acts.plan_schedule_id')
            ->leftJoin('sowings', 'sowings.id', '=', 'plan_acts.sowing_id')
            ->leftJoin('user_farmer', 'user_farmer.id', '=', 'sowings.user_farmer_id')
            ->leftJoin('plan_schedules as newplan', function($join) {
                $join->on('newplan.crop_id', '=', 'plan_acts.crop_id')
                    ->on('newplan.input_item_id', '=', 'sowings.input_item_id')
                    ->on('newplan.broker_id', '=', 'user_farmer.broker_id')
                    ->on('newplan.main_code', '=', 'plan_acts.plan_code');
            })
            ->select(
                'newplan.id as newid',
                'plan_schedules.id as oldscheduleid',
                'plan_acts.plan_schedule_id',
                'plan_acts.id as actid'
            )
            ->where('plan_acts.crop_id', $crop_id)
            ->where('user_farmer.broker_id', $broker_id)
            ->where('plan_acts.plan_code', 'H')
            ->whereNull('plan_schedules.id')
            ->get();

        foreach ($rawdata as $value) {
            $tmpData = PlanAct::find($value->actid);
            if ($tmpData) {
                $tmpData->plan_schedule_id = $value->newid;
                $tmpData->save();
            }
        }
    }

    public function save(Request $request, $schedulesId)
    {
        // $validated = $request->validate([
        //     'schedules_id' => 'required|integer',
        //     'crop_id' => 'required|integer|exists:crops,id',
        //     'broker_id' => 'nullable|integer|exists:brokers,id',
        //     'input_item_id' => 'nullable|integer|exists:input_items,id',
        // ]);

        // 1. อัปเดต TmpSchedulePlan
        $plan = Tmp_schedule_plans::findOrFail($schedulesId);
        $plan->name = $request['activity_name'];
        $plan->day = $request['activity_age'];
        $plan->can_review = $request['check_disease'];
        $plan->can_photo = $request['check_image'];
        $plan->save();

        // 2. ลบรายละเอียดเดิมทั้งหมด (ก่อนเพิ่มใหม่)
        Tmp_schedule_plan_details::where('tmp_schedule_plan_id', $schedulesId)->delete();
        
        // 3. เตรียมข้อมูลใหม่จาก chemicals[]
        $details = [];
        foreach ($request['chemicals'] as $chem) {
            $details[] = [
                'tmp_schedule_plan_id' => $schedulesId,
                'chemical_id' => $chem['chemical_id'],
                'value' => $chem['value'],
                'unit_id' => $chem['unit_id'],
                'p_value' => $chem['p_value'],
                'p_unit_id' => $chem['p_unit_id'],
                'set_group' => $chem['set_group'],
                'ctype' => $chem['ctype'],
                'rate' => $chem['rate']
            ];
        }

        // 4. Insert ข้อมูลใหม่ทั้งหมด
        Tmp_schedule_plan_details::insert($details);

        return response()->json([
            'status' => 'success',
            'message' => 'Plant template updated successfully.',
            'data' => $details
        ]);
    }

}
