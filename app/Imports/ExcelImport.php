<?php

namespace App\Imports;

use App\Models\User_farmer;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\Farmers;
use App\Models\Brokers;
use App\Models\Broker_areas;
use App\Models\Provinces;
use App\Models\Cities;
use App\Models\User;
use App\Models\Heads;

class ExcelImport implements OnEachRow, WithHeadingRow
{
    protected $cropId;
    protected $type;

    public function __construct($cropId, $type)
    {
        $this->cropId = $cropId;
        $this->type = $type;
    }


    public function onRow(Row $row)
    {
        $row = $row->toArray();

        // User_farmer::create([
        //     'broker_code'         => trim($row['broker_code']),
        //     'broker_name'         => trim($row['broker_name']),
        //     'sowing_city'         => trim($row['sowing_city']),
        //     'farmer_status'       => trim($this->type),
        //     'crop_id'       => trim($this->cropId),
        //     'sup_ae_name'         => trim($row['sup_ae_name']),
        //     'spray_name'          => trim($row['spray_name']),
        //     'head_name'           => trim($row['head_name']),
        //     'ae_name'             => trim($row['ae_name']),
        //     'farmer_citizen_id'   => trim($row['farmer_citizen_id']),
        //     'farmer_init'         => trim($row['farmer_init']),
        //     'farmer_name'         => trim($row['farmer_name']),
        //     'farmer_address1'     => trim($row['farmer_address1']),
        //     'farmer_address2'     => trim($row['farmer_address2']),
        //     'farmer_address3'     => trim($row['farmer_address3']),
        //     'farmer_city'         => trim($row['farmer_city']),
        //     'farmer_province'     => trim($row['farmer_province']),
        // ]); 

        $aename = explode(" ", $row['broker_name']);
		$tmpaename = array();
        if (sizeof($aename) == 3) {
            $tmpaename[0] = $aename[1];
            $tmpaename[1] = $aename[2];
        } 
        elseif (sizeof($aename) == 2) {
            $tmpaename[0] = $aename[0];
            $tmpaename[1] = $aename[1];
        } elseif (sizeof($aename) == 1) {
            $tmpaename[0] = $aename[0];
            $tmpaename[1] = '';
        } else {
            throw new \Exception("ไม่สามารถแยกชื่อบริษัทคู่ค้า: '{$row['broker_name']}'");
        }


        $broker = Brokers::where(function ($query) use ($tmpaename) {
                    $query->where('fname', trim($tmpaename[0]))
                          ->where('lname', trim($tmpaename[1]));
                    })
                    ->orWhere('code', trim($row['broker_code']))
                    ->first();

        if (!$broker) {
            throw new \Exception("ไม่พบบริษัทคู่ค้า (Broker) ที่มีรหัส: {$row['broker_code']} และชื่อ: {$row['broker_name']}");
        }

        $area = Broker_areas::where('crop_id', trim($this->cropId))
                    ->where('broker_id', trim($broker->id))
                    ->first();

        if (!$broker) {
            throw new \Exception("ไม่พบพื้นที่คู่ค้า (Broker Area) Brokerรหัส: {$row['broker_code']} และ Brokerชื่อ: {$row['broker_name']}");
        }

        $farmer = Farmers::where('citizenid', trim($row['farmer_citizen_id']))
                    ->first();

        if (!$farmer) {
            $aename = explode(" ", $row['farmer_name']);
            $tmpaename = array();
            if (sizeof($aename) == 3) {
                $tmpaename[0] = $aename[1];
                $tmpaename[1] = $aename[2];
            } 
            elseif (sizeof($aename) == 2) {
                $tmpaename[0] = $aename[0];
                $tmpaename[1] = $aename[1];
            } elseif (sizeof($aename) == 1) {
                $tmpaename[0] = $aename[0];
                $tmpaename[1] = '';
            } else {
                throw new \Exception("ไม่สามารถแยกชื่อ Farmer: '{$row['farmer_name']}'");
            }
            $newFarmer = array();
            $newFarmer['citizenid'] = trim($row['farmer_citizen_id']);
            $newFarmer['init'] = trim($row['farmer_init']);
            $newFarmer['fname'] = trim($tmpaename[0]);
            $newFarmer['lname'] = trim($tmpaename[1]);
            $newFarmer['address1'] = trim($row['farmer_address1']);
            $newFarmer['address2'] = trim($row['farmer_address2']);
            $newFarmer['address3'] = trim($row['farmer_address3']);
            $newFarmer['code'] = $this->generateBrokerCode($this->cropId, $broker->id);

            $province = Provinces::where('th_name', trim($row['farmer_province']))->first();
            if (!$province) {
                $newProvince = array();
                $newProvince['th_name'] = trim($row['farmer_province']);
                $province = Provinces::create($newProvince);
            }

            $city = Cities::where('th_name', trim($row['farmer_city']))
                        ->where('province_id', trim($province->id))
                        ->first();
            if (!$city) {
                $newcity = array();
                $newcity['th_name'] = trim($row['farmer_city']);
                $newcity['province_id'] = trim($province->id);
                $city = Cities::create($newcity);
            }
            $newFarmer['city_id'] = $city->id;
            $newFarmer['province_id'] = $province->id;
            $farmer = Farmers::create($newFarmer);
   
        }

        $manager = $this->findUserByName($row['sup_ae_name'], 'Sup_ae');
        $user = $this->findUserByName($row['ae_name'], 'AE');
        $review = $this->findUserByName($row['spray_name'], 'Spray');


        $aename = explode(" ", $row['head_name']);
		$tmpaename = array();
        if (sizeof($aename) == 3) {
            $tmpaename[0] = $aename[1];
            $tmpaename[1] = $aename[2];
        } 
        elseif (sizeof($aename) == 2) {
            $tmpaename[0] = $aename[0];
            $tmpaename[1] = $aename[1];
        } elseif (sizeof($aename) == 1) {
            $tmpaename[0] = $aename[0];
            $tmpaename[1] = '';
        } else {
            throw new \Exception("ไม่สามารถแยกชื่อ Heads: '{$row['head_name']}'");
        }


        $head = Heads::where('fname', trim($tmpaename[0]))
                    ->where('lname', trim($tmpaename[1]))
                    ->first();

        if (!$head) {
            throw new \Exception("ไม่พบHeadsชื่อ ที่ชื่อ: {$row['head_name']}");
        }

        $userFarmer = User_farmer::create([
            'crop_id' => trim($this->cropId),
            'sowing_city' => trim($row['sowing_city']),
            'status' => trim($row['farmer_status']),
            'broker_id' => trim($broker->id),
            'review_id' => trim($review->id),
            'manager_id' => trim($manager->id),
            'user_id' => trim($user->id),
            'head_id' => trim($head->id),
            'area_id' => trim($area->id),
            'farmer_id' => trim($farmer->id),
            'farmer_code' => trim($farmer->code),
            'createdBy' => auth()->id() ?? 1,
            'modifiedBy' => auth()->id() ?? 1,
            'created' => Carbon::now(),
            'modified' => Carbon::now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Farmer created successfully.',
            'data' => $userFarmer
        ], 201);
    }

    function findUserByName(string $name, string $mode): ?User
    {
        $aename = explode(' ', $name);
        $tmpaename = [];
        if (sizeof($aename) == 3) {
            $tmpaename[0] = $aename[1];
            $tmpaename[1] = $aename[2];
        } 
        elseif (sizeof($aename) == 2) {
            $tmpaename[0] = $aename[0];
            $tmpaename[1] = $aename[1];
        } elseif (sizeof($aename) == 1) {
            $tmpaename[0] = $aename[0];
            $tmpaename[1] = '';
        } else {
            throw new \Exception("ไม่สามารถแยกชื่อ{$mode}: '{$row['broker_name']}'");
        }

        $result = User::where('fname', trim($tmpaename[0]))
                    ->where('lname', trim($tmpaename[1]))
                    ->first();

        if (!$result) {
            throw new \Exception("ไม่พบ{$mode} ที่ชื่อ: {$name}");
        }

        return $result;
    }
    
    private function generateBrokerCode($cropId, $brokerId)
    {
        // ดึงข้อมูล UserFarmer ล่าสุดตาม crop_id และ broker_id
        $dataFarmer = User_farmer::where('crop_id', $cropId)
            ->where('broker_id', $brokerId)
            ->orderByDesc('id')
            ->first();

        if (!$dataFarmer || empty($dataFarmer->farmer_code)) {
            // ดึง Broker code จาก brokerId
            $brokerCode = Brokers::select('code')->find($brokerId);

            // ถ้าไม่เจอ brokerCode อาจจะต้องเช็ค null
            $codePrefix = $brokerCode ? $brokerCode->code : 'BR'; // กำหนดค่าเริ่มต้นถ้าไม่เจอ

            $farmerCode = $codePrefix . '-' . str_pad(1, 4, '0', STR_PAD_LEFT);
        } else {
            // แยก farmer_code เป็น prefix และเลข
            $base = explode('-', $dataFarmer->farmer_code);
            $prefix = $base[0];
            $number = intval($base[1]) + 1;

            $farmerCode = $prefix . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
        }

        return $farmerCode;
    }
}
