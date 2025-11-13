<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User_farmer;
use App\Models\Brokers;
use App\Models\Farmers;
use App\Models\Farmer_cards;
use App\Models\Farmer_images;
use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class UserFarmerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $cropId)
    {
        $userFarmers = User_farmer::with([
            'crop', 'user', 'manager', 'review',
            'head', 'broker', 'area', 'farmer', 
            'sowings','farmer.farmer_card',
            'farmer.farmer_image',
        ])
        ->where('crop_id', $cropId)
        ->orderByDesc('id')
        ->get();

        return response()->json([
            'status' => 'success',
            'data' => $userFarmers,
            'cropId' => $cropId
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(string $cropId, Request $request)
    {

        $requestData = $request->all();
        $requestData["crop_id"] = $cropId;
        $requestData["farmer_code"] = $this->generateBrokerCode($cropId, $requestData["broker_id"]);
        // $validated["head_id"] = $cropId;
        // $validated["farmer_id"] = $cropId;
        // $validated["user_id"] = $cropId;
        // $validated["manager_id"] = $cropId;
        // $validated["review_id"] = $cropId;
        // $validated["sowing_city"] = $cropId;

        $userFarmer = User_farmer::create($requestData);

        return response()->json([
            'status' => 'success',
            'message' => 'User Farmer created successfully.',
            'data' => $userFarmer
        ], 201);
    }

    public function storeNew(string $cropId, Request $request)
    {

        $requestData = $request->all();

        $requestNewFarmer = array();
        $requestNewFarmer["code"] = $this->generateBrokerCode($cropId, $requestData["broker_id"]);
        $requestNewFarmer["init"] = $requestData["prefix"];
        $requestNewFarmer["fname"] = $requestData["fname"];
        $requestNewFarmer["lname"] = $requestData["lname"];
        $requestNewFarmer["citizenid"] = $requestData["id_card"];
        $requestNewFarmer["province_id"] = $requestData["province_id"];
        $requestNewFarmer["city_id"] = $requestData["city_id"];
        $requestNewFarmer["sub_cities"] = $requestData["village_name"];
        $requestNewFarmer["address1"] = $requestData["moo"];
        $requestNewFarmer["address2"] = $requestData["house_number"];


        $farmer = Farmers::create($requestNewFarmer);

        $requestData["crop_id"] = $cropId;
        $requestData["farmer_code"] = $farmer->code;
        $requestData["farmer_id"] = $farmer->id;
        $requestData["sowing_city"] = $farmer->sub_cities;
        // $validated["farmer_id"] = $cropId;
        // $validated["farmer_id"] = $cropId;
        // $validated["user_id"] = $cropId;
        // $validated["manager_id"] = $cropId;
        // $validated["review_id"] = $cropId;
        // $validated["sowing_city"] = $cropId;

        $userFarmer = User_farmer::create($requestData);

        return response()->json([
            'status' => 'success',
            'message' => 'User Farmer created successfully.',
            'data' => $requestData
        ], 201);
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

    /**
     * Display the specified resource.
     */
    public function show(string $cropId, string $id)
    {
        $userFarmer = User_farmer::with([
            'crop', 'user', 'manager', 'review',
            'head', 'broker', 'area', 'farmer', 'farmer.farmer_card',
            'farmer.farmer_image',
            'created_by', 'modified_by'
        ])->find($id);

        if (!$userFarmer) {
            return response()->json(['status' => 'error', 'message' => 'User Farmer not found'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $userFarmer]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $cropId, string $id)
    {
        $userFarmer = User_farmer::find($id);

        if (!$userFarmer) {
            return response()->json(['status' => 'error', 'message' => 'User Farmer not found'], 404);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'manager_id' => 'nullable|exists:users,id',
            'review_id' => 'nullable|exists:users,id',
            'broker_id' => 'nullable|exists:brokers,id',
            'area_id' => 'nullable|exists:areas,id',
            'farmer_id' => 'nullable|exists:farmers,id',
            'head_id' => 'nullable|exists:heads,id',
            'sowing_city' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
        ]);

        $userFarmer->fill($validated)->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User Farmer updated successfully.',
            'data' => $userFarmer
        ]);
    }

    public function updateByType(Request $request, string $cropId, string $id)
    {
        $userFarmer = User_farmer::find($id);
        

        if (!$userFarmer) {
            return response()->json(['status' => 'error', 'message' => 'User Farmer not found'], 404);
        }
        $requestData = $request->all();
        $typeName = $requestData["type"];
        switch($typeName){
            case 'head':
            $userFarmer->head_id = $requestData["head_id"];
            $userFarmer->save();
            break;
            case 'farmer':
            $userFarmer->farmer_id = $requestData["farmer_id"];
            $userFarmer->save();
            break;
            case 'user':
            $userFarmer->user_id = $requestData["user_id"];
            $userFarmer->save();
            break;
            case 'manager':
            $userFarmer->manager_id = $requestData["manager_id"];
            $userFarmer->save();
            break;
            case 'review':
            $userFarmer->review_id = $requestData["review_id"];
            $userFarmer->save();
            break;
            case 'broker':
            $userFarmer->broker_id = $requestData["broker_id"];
            $userFarmer->save();
            break;
            case 'sowing':
            $userFarmer->sowing_city = $requestData["sowing_name"];
            $userFarmer->save();
            break;
            case 'statusName':
            $userFarmer->status = $requestData["statusName"];
            $userFarmer->save();
            break;
        }

        return response()->json([
            'status' => 'success',
            'message' => $typeName.' updated successfully.',
            'data' => $userFarmer
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $userFarmer = User_farmer::find($id);

        if (!$userFarmer) {
            return response()->json(['status' => 'error', 'message' => 'User Farmer not found'], 404);
        }

        $userFarmer->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User Farmer deleted successfully.'
        ]);
    }

    public function import(Request $request, string $cropId)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx',
            'type' => 'required|string|max:10'
        ]);

        Excel::import(new ExcelImport($cropId, $request->input('type')), $request->file('file'));

        return response()->json([
            'success' => true,
            'message' => 'นำเข้าข้อมูลสำเร็จแล้ว'
        ]);
    }
    
    public function images(Request $request, string $cropId)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,gif',
            'type' => 'required|string|max:10',
            'farmerId' => 'required|string|max:10'
        ]);
        $farmer = array();
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            if ($file->isValid()) {
                if($request->input('type') == "card"){
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('public/images', $filename);
                    $dbPath = str_replace('public/', 'storage/', $path);
                    $image_tmp = array();
                    $image_tmp["farmer_id"] = $request->input('farmerId');
                    $image_tmp["attach_dir"] = 'storage/images';
                    $image_tmp["attach"] = $filename;
                    $farmer = new Farmer_cards();
                    $farmer = Farmer_cards::where('farmer_id', $request->input('farmerId'))->first();
                    if (!$farmer) {
                        $farmer = Farmer_cards::create($image_tmp);
                    }else{
                        $farmer->farmer_id = $request->input('farmerId');
                        $farmer->attach_dir = 'storage/images';
                        $farmer->attach = $filename;
                        $farmer->save();
                    }
                    return response()->json([
                        'success' => true,
                        'message' => 'Upload card success',
                        'path' => $farmer->attach_dir.'/'.$farmer->attach,
                    ]);
                }else if($request->input('type') == "image"){
                    $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('public/images', $filename);
                    $dbPath = str_replace('public/', 'storage/', $path);
    
                    $farmer_images = new Farmer_images();
                    $farmer_images = Farmer_images::where('farmer_id', $request->input('farmerId'))
                                    ->where('crop_id', $cropId)
                                    ->first();
                                    
                    if (!$farmer_images) {
                        
                        $farmer_images = Farmer_images::create([
                            'farmer_id' => $request->input('farmerId'),
                            'attach_dir' => 'storage/images',
                            'crop_id' => intval($cropId),
                            'attach' => $filename,
                        ]);
                    }else{
                        $farmer_images->farmer_id = $request->input('farmerId');
                        $farmer_images->attach_dir = 'storage/images';
                        $farmer_images->attach = $filename;
                        $farmer_images->save();
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Upload image success',
                        'path' => $farmer_images->attach_dir.'/'.$farmer_images->attach,
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'No Type Error',
                    ]);
                }


            }
        }
        return response()->json([
            'success' => false,
            'message' => 'No valid image uploaded',
        ]);
    }
}
