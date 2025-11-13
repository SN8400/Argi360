<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExportExcelController;

// หน้าฟอร์ม login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/forgot_password', [LoginController::class, 'forgotPassword'])->name('forgot_password');
Route::middleware(['auth'])->group(function () {
    Route::view('/', 'index')->name('index');
    Route::view('/index', 'index')->name('index');
    Route::view('/report', 'Masters.report_list')->name('report');
    Route::view('/dashboard', 'Masters.dashboard')->name('dashboard');


    /* ************** Admin Core ************************** */
    Route::view('/crop_list', 'Masters.crop_list')->name('crop_list');
    Route::view('/admin_core', 'Masters.admin_core')->name('admin_core');
    Route::view('/roleMaster', 'Masters.role')->name('roleMaster');
    Route::view('/Growstate', 'Admin.growstate')->name('Growstate');
    Route::view('/Checklist', 'Admin.checklist')->name('Checklist');
    Route::view('/standardMaster', 'Masters.standard')->name('standardMaster');

    Route::prefix('/User')->group(function () {
        Route::view('/', 'Users.index')->name('Users');
        Route::view('/create', 'Users.create')->name('Users.create');
        Route::get('/{id}/edit', function ($id) {
            return view('Users.edit', ['id' => $id]);
        })->name('Users.edit');
    });

    Route::prefix('/InputItems')->group(function () {
        Route::view('/', 'InputItems.index')->name('InputItems');
        Route::view('/create', 'InputItems.create')->name('InputItems.create');
        Route::get('/{id}/edit', function ($id) {
            return view('InputItems.edit', ['id' => $id]);
        })->name('InputItems.edit');
    });

    Route::prefix('/Brokers')->group(function () {
        Route::view('/', 'Brokers.index')->name('Brokers');
        Route::view('/create', 'Brokers.create')->name('Brokers.create');
        Route::get('/{id}/edit', function ($id) {
            return view('Brokers.edit', ['id' => $id]);
        })->name('Brokers.edit');
    });

    Route::prefix('/Heads')->group(function () {
        Route::view('/', 'Heads.index')->name('Heads');
        Route::view('/create', 'Heads.create')->name('Heads.create');
        Route::get('/{id}/edit', function ($id) {
            return view('Heads.edit', ['id' => $id]);
        })->name('Heads.edit');
    });

    Route::prefix('/Chemicals')->group(function () {
        Route::view('/', 'chemicals.index')->name('Chemicals');
        Route::view('/create', 'chemicals.create')->name('Chemicals.create');
        Route::get('/{id}/edit', function ($id) {
            return view('chemicals.edit', ['id' => $id]);
        })->name('Chemicals.edit');
    });

    Route::prefix('/TmpSchedules')->group(function () {
        Route::view('/', 'TmpSchedules.index')->name('tmpSchedules');
        Route::view('/new', 'TmpSchedules.new')->name('tmpSchedules.new');
        Route::get('/{id}/edit', function ($id) {
            return view('TmpSchedules.edit', ['id' => $id]);
        })->name('tmpSchedules.edit');
        Route::get('/{id}/clone', function ($id) {
            return view('TmpSchedules.clone', ['id' => $id]);
        })->name('tmpSchedules.clone');
        Route::get('/{id}/review', function ($id) {
            return view('TmpSchedules.review', ['id' => $id]);
        })->name('tmpSchedules.review');
        Route::get('/{id}/manage', function ($id) {
            return view('TmpSchedules.manage', ['id' => $id]);
        })->name('tmpSchedules.manage');
    });

    Route::prefix('CropMaster')->group(function () {
        Route::view('/', 'Admin.crops.index')->name('CropMaster');
        Route::view('/create', 'Admin.crops.create')->name('CropMaster.create');
        Route::get('/{id}/edit', function ($id) {
            return view('Admin.crops.edit', ['id' => $id]);
        })->name('CropMaster.edit');
    });

    /* ************** End Admin Core ************************** */

    Route::get('{dep}/selectedCrop/{id}', function ($dep, $id) {
        return view('Masters.selected', ['dep' => $dep, 'id' => $id]);
    })->name('selectedCrop');

    /* ************** Admin Crop ************************** */
    Route::view('/{dep}/admin_crop/{id}', 'Masters.admin_crop')->name('admin_crop');
    Route::view('/{dep}/operation/{id}', 'Masters.operation')->name('operation');
    // Route::view('/seedCode', 'Admin.seed_code')->name('seedCode');
    Route::view('/seedCode/{id}', 'Masters.seed_code')->name('seedCode');
    Route::view('/seedPack/{id}', 'Masters.seed_packs')->name('seedPack');
    Route::view('/brokerHead/{id}', 'Masters.broker_heads')->name('brokerHead');
    Route::view('/brokerArea/{id}', 'Masters.broker_areas')->name('brokerArea');
    // Route::view('/brokerHead', 'Admin.broker_heads')->name('brokerHead');
    // Route::view('/brokerArea', 'Admin.broker_areas')->name('brokerArea');
    Route::view('/map_matcodes/{id}', 'Masters.map_matcodes')->name('map_matcodes');
    Route::view('/lock_gps/{id}', 'Masters.lock_gps')->name('lock_gps');
    Route::view('/lock_sowing/{id}', 'Masters.lock_sowing')->name('lock_sowing');

    Route::prefix('/farmers')->group(function () {
        Route::view('/', 'farmers.index')->name('farmers');
        Route::view('/create', 'farmers.create')->name('farmers.create');
        Route::get('/{id}/edit', function ($id) {
            return view('farmers.edit', ['id' => $id]);
        })->name('farmers.edit');   
    });

    Route::prefix('/plannings')->group(function () {
        Route::get('/upload/{id}', function ($id) {
            return view('plannings.upload', ['id' => $id]);
        })->name('plannings.upload');        
        Route::get('/{id}', function ($id) {
            return view('plannings.index', ['id' => $id]);
        })->name('plannings');
        Route::get('/{cropId}/{planId}', function ($cropId, $planId) {
            return view('plannings.edit', ['cropId' => $cropId,'planId' => $planId]);
        })->name('plannings.edit');
    });

    Route::prefix('/Schedules')->group(function () {  
        Route::get('/{id}', function ($id) {
            return view('PlanSchedules.index', ['id' => $id]);
        })->name('Schedules');
        Route::get('/detail/{id}/{schedule_id}', function ($id, $schedule_id) {
            return view('PlanSchedules.detail', ['id' => $id, 'schedule_id' => $schedule_id]);
        })->name('Schedules.detail');
        Route::get('/view/{type_id}/{crop_id}/{broker_id}/{input_item_id}', function ($type_id, $crop_id, $broker_id, $input_item_id) {
            return view('PlanSchedules.view', ['type_id' => $type_id, 'crop_id' => $crop_id, 'broker_id' => $broker_id, 'input_item_id' => $input_item_id]);
        })->name('Schedules.view');
    });
    /* ************** End Admin Crop ************************** */

    /* ************** Operation ************************** */
    Route::prefix('/HarvestPlans')->group(function () {
        Route::view('/{id}', 'HarvestPlans.index')->name('HarvestPlans');
        Route::get('/detailDate/{cropId}/{date}/{type}', function ($cropId, $date, $type) {
            return view('HarvestPlans.detailDate', ['cropId' => $cropId, 'date' => $date, 'type' => $type]);
        })->name('HarvestPlans.detailDate');
        Route::get('/detailFarmer/{cropId}/{date}/{areaId}/{brokerId}/{itemInputId}/{age}/{type}', function ($cropId, $date, $areaId, $brokerId, $itemInputId, $age, $type) {
            return view('HarvestPlans.detailFarmer', ['cropId' => $cropId, 'date' => $date, 'areaId' => $areaId, 'brokerId' => $brokerId, 'itemInputId' => $itemInputId, 'age' => $age, 'type' => $type]);
        })->name('HarvestPlans.detailFarmer');
        
        Route::get('/edit/{cropId}/{harvestId}', function ($cropId, $harvestId) {
            return view('HarvestPlans.edit', ['cropId' => $cropId, 'harvestId' => $harvestId]);
        })->name('HarvestPlans.edit');
        
        Route::get('/request/{cropId}/{harvestId}/{status}', function ($cropId, $harvestId, $status) {
            return view('HarvestPlans.request', ['cropId' => $cropId, 'harvestId' => $harvestId, 'status' => $status]);
        })->name('HarvestPlans.request');

        Route::get('/separate/{cropId}/{harvestId}/{status}', function ($cropId, $harvestId, $status) {
            return view('HarvestPlans.requestSeparate', ['cropId' => $cropId, 'harvestId' => $harvestId, 'status' => $status]);
        })->name('HarvestPlans.requestSeparate');
    });

    Route::prefix('/sowing')->group(function () {
        Route::view('/', 'HarvestPlans.index')->name('sowing');
        Route::view('/create', 'HarvestPlans.edit')->name('sowing.create');
        // Route::get('/{id}/edit', function ($id) {
        //     return view('HarvestPlans.edit', ['id' => $id]);
        // })->name('sowing.edit');   
    });    

    Route::prefix('/activities')->group(function () {
        Route::view('/', 'HarvestPlans.index')->name('activities');
        Route::view('/create', 'HarvestPlans.edit')->name('activities.create');
        // Route::get('/{id}/edit', function ($id) {
        //     return view('HarvestPlans.edit', ['id' => $id]);
        // })->name('activities.edit');   
    }); 
    Route::prefix('/qa_sample')->group(function () {
        Route::view('/', 'HarvestPlans.index')->name('qa_sample');
        Route::view('/create', 'HarvestPlans.edit')->name('qa_sample.create');
        // Route::get('/{id}/edit', function ($id) {
        //     return view('HarvestPlans.edit', ['id' => $id]);
        // })->name('qa_sample.edit');   
    });






    /* *************************************************** */



    Route::view('/UnitMaster', 'Masters.unit')->name('UnitMaster');
    Route::view('/checkListMaster', 'Masters.CheckList')->name('checkListMaster');
    Route::view('/plant_code', 'Masters.plant_code')->name('plant_code');
    Route::view('/areaMaster', 'Masters.area')->name('areaMaster');
    Route::view('/harvestType', 'Masters.harvest_types')->name('harvestType');
    Route::view('/citiesMaster', 'Masters.city')->name('citiesMaster');
    Route::view('/provincesMaster', 'Masters.province')->name('provincesMaster');

    Route::get('/download/{crop_id}/{report_type}', [ExportExcelController::class, 'testexportExcel'])->name('downloadYieldReport');
    Route::get('/download/{crop_id}', [ExportExcelController::class, 'exportGrowPlanByCrop'])->name('downloadGrowPlanReport');
    Route::get('/downloadplan/{crop_id}', [ExportExcelController::class, 'exportHarvestPlanByCrop'])->name('downloadHarvestPlanReport');

    Route::view('/growstate/{id}', 'Masters.growstate')->name('growstate');

    Route::prefix('/yeilds')->group(function () {
        Route::view('/', 'Yeilds.index')->name('yeilds');

        Route::get('/{planId}/{cropId}/{itemInputId}/{areaId}/{brokerId}/{harvestType}/{yieldId?}', function ($planId , $cropId, $itemInputId, $areaId, $brokerId, $harvestType, $yieldId = null) {
            return view('Yeilds.add', ['planId' => $planId ,'cropId' => $cropId,'inputItemId' => $itemInputId,'areaId' => $areaId,'brokerId' => $brokerId,'harvestTypeId' => $harvestType,'yieldId' => $yieldId]);
        })->name('yeilds.add');

    });

    Route::prefix('/userFarmers')->group(function () {
        Route::get('/{cropId}', function ($cropId) {
            return view('userFarmers.index', ['cropId' => $cropId]);
        })->name('userFarmers');   
        
        Route::get('/{cropId}/create', function ($cropId) {
            return view('userFarmers.create', ['cropId' => $cropId]);
        })->name('userFarmers.create');   
        
        Route::get('/{cropId}/new', function ($cropId) {
            return view('userFarmers.new', ['cropId' => $cropId]);
        })->name('userFarmers.new');   

        Route::get('/{cropId}/{id}/edit', function ($cropId, $id) {
            return view('userFarmers.edit', ['id' => $id, 'cropId' => $cropId]);
        })->name('userFarmers.edit');   

        Route::get('/{cropId}/{type}/{id}/upload', function ($cropId, $type, $id) {
            return view('userFarmers.upload', ['cropId' => $cropId, 'type' => $type, 'id' => $id]);
        })->name('userFarmers.upload');   

        Route::get('/{cropId}/{type}/uploadUser', function ($cropId, $type) {
            return view('userFarmers.uploadUser', ['cropId' => $cropId, 'type' => $type]);
        })->name('userFarmers.uploadUser');   

        Route::get('/{cropId}/download', function ($cropId) {
            return view('userFarmers.download', ['cropId' => $cropId]);
        })->name('userFarmers.download');   

        Route::get('/{cropId}/print', function ($cropId) {
            return view('userFarmers.print', ['cropId' => $cropId]);
        })->name('userFarmers.print');   

        Route::get('/{cropId}/{type}/{id}/change', function ($cropId, $type, $id) {
            return view('userFarmers.change', ['cropId' => $cropId, 'type' => $type, 'id' => $id]);
        })->name('userFarmers.change');   

        Route::get('/downloadFarmer/{crop_id}', [ExportExcelController::class, 'exportFarmerByCrop'])->name('downloadFarmerReport');

    });

});