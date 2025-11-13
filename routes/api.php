<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CropController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\InputItemController;
use App\Http\Controllers\Api\UnitsController;
use App\Http\Controllers\Api\StandardController;
use App\Http\Controllers\Api\ChemicalsController;
use App\Http\Controllers\Api\BrokersController;
use App\Http\Controllers\Api\CheckListController;
use App\Http\Controllers\Api\HeadsController;
use App\Http\Controllers\Api\TemplatePlansController;
use App\Http\Controllers\Api\GrowStateController;
use App\Http\Controllers\Api\CheckListCropsController;
use App\Http\Controllers\Api\SeedCodeController;
use App\Http\Controllers\Api\PlantCodeController;
use App\Http\Controllers\Api\BrokerAreaController;
use App\Http\Controllers\Api\SeedPackController;
use App\Http\Controllers\Api\BrokerHeadController;
use App\Http\Controllers\Api\AreaController;
use App\Http\Controllers\Api\MapMatcodeController;
use App\Http\Controllers\Api\PlanningController;
use App\Http\Controllers\Api\YeildController;
use App\Http\Controllers\Api\HarvestTypeController;
use App\Http\Controllers\ExportExcelController;
use App\Http\Controllers\Api\FarmerController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\UserFarmerController;
use App\Http\Controllers\Api\FarmerCardController;
use App\Http\Controllers\Api\PlanScheduleController;
use App\Http\Controllers\Api\LocksGPSController;
use App\Http\Controllers\Api\HarvestPlansController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware('auth:sanctum')->group(function () {
Route::get('/crops', [CropController::class, 'index']);
Route::post('/crops', [CropController::class, 'store']);
Route::get('/crops/{id}', [CropController::class, 'show']);
Route::put('/crops/{id}', [CropController::class, 'update']);
Route::delete('/crops/{id}', [CropController::class, 'destroy']);
// });

Route::get('/roles', [RoleController::class, 'index']);
Route::post('/roles', [RoleController::class, 'store']);
Route::get('/roles/{id}', [RoleController::class, 'show']);
Route::put('/roles/{id}', [RoleController::class, 'update']);
Route::delete('/roles/{id}', [RoleController::class, 'destroy']);

// Route::middleware('check.api.key')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
// });

Route::get('/InputItems', [InputItemController::class, 'index']);
Route::post('/InputItems', [InputItemController::class, 'store']);
Route::get('/InputItems/{id}', [InputItemController::class, 'show']);
Route::put('/InputItems/{id}', [InputItemController::class, 'update']);
Route::delete('/InputItems/{id}', [InputItemController::class, 'destroy']);

Route::get('/units', [UnitsController::class, 'index']);
Route::post('/units', [UnitsController::class, 'store']);
Route::get('/units/{id}', [UnitsController::class, 'show']);
Route::put('/units/{id}', [UnitsController::class, 'update']);
Route::delete('/units/{id}', [UnitsController::class, 'destroy']);

Route::get('/standards', [StandardController::class, 'index']);
Route::post('/standards', [StandardController::class, 'store']);
Route::get('/standards/{id}', [StandardController::class, 'show']);
Route::put('/standards/{id}', [StandardController::class, 'update']);
Route::delete('/standards/{id}', [StandardController::class, 'destroy']);

Route::get('/chemicals', [ChemicalsController::class, 'index']);
Route::post('/chemicals', [ChemicalsController::class, 'store']);
Route::get('/chemicals/{id}', [ChemicalsController::class, 'show']);
Route::put('/chemicals/{id}', [ChemicalsController::class, 'update']);
Route::delete('/chemicals/{id}', [ChemicalsController::class, 'destroy']);

Route::get('/brokers', [BrokersController::class, 'index']);
Route::post('/brokers', [BrokersController::class, 'store']);
Route::get('/brokers/{id}', [BrokersController::class, 'show']);
Route::put('/brokers/{id}', [BrokersController::class, 'update']);
Route::delete('/brokers/{id}', [BrokersController::class, 'destroy']);

Route::get('/checklist', [CheckListController::class, 'index']);
Route::post('/checklist', [CheckListController::class, 'store']);
Route::get('/checklist/{id}', [CheckListController::class, 'show']);
Route::put('/checklist/{id}', [CheckListController::class, 'update']);
Route::delete('/checklist/{id}', [CheckListController::class, 'destroy']);

Route::get('/heads', [HeadsController::class, 'index']);
Route::post('/heads', [HeadsController::class, 'store']);
Route::get('/heads/{id}', [HeadsController::class, 'show']);
Route::put('/heads/{id}', [HeadsController::class, 'update']);
Route::delete('/heads/{id}', [HeadsController::class, 'destroy']);

Route::get('/plantcode', [PlantCodeController::class, 'index']);
Route::post('/plantcode', [PlantCodeController::class, 'store']);
Route::get('/plantcode/{id}', [PlantCodeController::class, 'show']);
Route::put('/plantcode/{id}', [PlantCodeController::class, 'update']);
Route::delete('/plantcode/{id}', [PlantCodeController::class, 'destroy']);

Route::prefix('growstates')->group(function () {
    Route::get('/', [GrowStateController::class, 'index']);
    Route::post('/', [GrowStateController::class, 'store']);
    Route::post('/clone', [GrowStateController::class, 'clone']);
    Route::get('/{id}', [GrowStateController::class, 'show']);
    Route::post('/edit/{id}', [GrowStateController::class, 'edit']);
    Route::post('/update', [GrowStateController::class, 'update']);
    Route::delete('/{id}', [GrowStateController::class, 'destroy']);
});

Route::prefix('checklistcrop')->group(function () {
    Route::get('/', [CheckListCropsController::class, 'index']);
    Route::post('/', [CheckListCropsController::class, 'store']);
    Route::post('/clone', [CheckListCropsController::class, 'clone']);
    Route::get('/{id}', [CheckListCropsController::class, 'show']);
    Route::post('/edit/{id}', [CheckListCropsController::class, 'update']);
    // Route::post('/update', [CheckListCropsController::class, 'update']);
    Route::delete('/{id}', [CheckListCropsController::class, 'destroy']);
});

Route::prefix('seedcodes')->group(function () {
    Route::get('/', [SeedCodeController::class, 'index']);
    Route::post('/', [SeedCodeController::class, 'store']);
    Route::get('/{id}', [SeedCodeController::class, 'show']);
    Route::put('/{id}', [SeedCodeController::class, 'update']);
    Route::delete('/{id}', [SeedCodeController::class, 'destroy']);
});

Route::prefix('tmpSchedules')->group(function () {
    Route::get('/', [TemplatePlansController::class, 'index']);
    Route::post('/', [TemplatePlansController::class, 'store']);
    Route::post('/clone', [TemplatePlansController::class, 'clone']);
    Route::post('/save/{id}', [TemplatePlansController::class, 'save']);
    Route::get('/showmanage/{id}', [TemplatePlansController::class, 'showmanage']);
    Route::get('/viewdetail/{id}', [TemplatePlansController::class, 'viewdetail']);
    Route::get('/{id}', [TemplatePlansController::class, 'show']);
    Route::put('/{id}', [TemplatePlansController::class, 'update']);
    Route::delete('/{id}', [TemplatePlansController::class, 'destroy']);
});

Route::post('/seedPack', [SeedPackController::class, 'store']);
Route::get('/seedPack/index/{id}', [SeedPackController::class, 'index']);
Route::get('/seedPack/{id}', [SeedPackController::class, 'show']);
Route::put('/seedPack/{id}', [SeedPackController::class, 'update']);
Route::delete('/seedPack/{id}', [SeedPackController::class, 'destroy']);

Route::get('/brokerHead', [BrokerHeadController::class, 'index']);
Route::post('/brokerHead', [BrokerHeadController::class, 'store']);
Route::get('/brokerHead/getListByCrop/{cropId}/{brokerId}', [BrokerHeadController::class, 'getListByCrop']);
Route::get('/brokerHead/{id}', [BrokerHeadController::class, 'show']);
Route::put('/brokerHead/{id}', [BrokerHeadController::class, 'update']);
Route::delete('/brokerHead/{id}', [BrokerHeadController::class, 'destroy']);

Route::get('/brokerArea', [BrokerAreaController::class, 'index']);
Route::post('/brokerArea', [BrokerAreaController::class, 'store']);
Route::get('/brokerArea/{id}', [BrokerAreaController::class, 'show']);
Route::put('/brokerArea/{id}', [BrokerAreaController::class, 'update']);
Route::delete('/brokerArea/{id}', [BrokerAreaController::class, 'destroy']);

Route::get('/areas', [AreaController::class, 'index']);
Route::post('/areas', [AreaController::class, 'store']);
Route::get('/areas/{id}', [AreaController::class, 'show']);
Route::put('/areas/{id}', [AreaController::class, 'update']);
Route::delete('/areas/{id}', [AreaController::class, 'destroy']);

Route::prefix('mapMatcodes')->group(function () {
    Route::get('/', [MapMatcodeController::class, 'index']);
    Route::post('/', [MapMatcodeController::class, 'store']);
    Route::post('/clone', [MapMatcodeController::class, 'clone']);
    Route::put('/{id}', [MapMatcodeController::class, 'update']);
    Route::delete('/{id}', [MapMatcodeController::class, 'destroy']);
});

Route::prefix('plannings')->group(function () {
    Route::get('/', [PlanningController::class, 'index']);
    Route::post('/', [PlanningController::class, 'store']);
    Route::get('/{crop_id}/{plan_id}', [PlanningController::class, 'show']);
    Route::put('/{id}', [PlanningController::class, 'update']);
});

Route::prefix('planSchedules')->group(function () {
    Route::get('/{id}', [PlanScheduleController::class, 'index']);
    Route::get('/{id}/{schedule_id}', [PlanScheduleController::class, 'show']);
    Route::get('/{type_id}/{crop_id}/{broker_id}/{input_item_id}', [PlanScheduleController::class, 'getView']);
    
    Route::post('/', [PlanScheduleController::class, 'store']);
    Route::delete('/{id}', [PlanScheduleController::class, 'destroy']);
});

Route::prefix('yields')->group(function () {
    Route::get('/', [YeildController::class, 'index']);
    Route::post('/', [YeildController::class, 'store']);
    Route::get('/getbyplanning', [YeildController::class, 'getbyplanning']);
    Route::post('/editbyplanning', [YeildController::class, 'editbyplanning']);
    Route::get('/{id}', [YeildController::class, 'show']);
    Route::put('/{id}', [YeildController::class, 'update']);
    Route::delete('/{id}', [YeildController::class, 'destroy']);
});

Route::prefix('harvestTypes')->group(function () {
    Route::get('/', [HarvestTypeController::class, 'index']);
    Route::post('/', [HarvestTypeController::class, 'store']);
    Route::put('/{id}', [HarvestTypeController::class, 'update']);
    Route::delete('/{id}', [HarvestTypeController::class, 'destroy']);
});

Route::prefix('uploadplan')->group(function () {
    Route::post('/{id}', [ExportExcelController::class, 'importPlanByExcel']);
});

Route::prefix('farmers')->group(function () {
    Route::get('/', [FarmerController::class, 'index']);
    Route::post('/', [FarmerController::class, 'store']);
    Route::get('/{id}', [FarmerController::class, 'show']);
    Route::put('/{id}', [FarmerController::class, 'update']);
    Route::delete('/{id}', [FarmerController::class, 'destroy']);
});

Route::prefix('provinces')->group(function () {
    Route::get('/', [ProvinceController::class, 'index']);
    Route::post('/', [ProvinceController::class, 'store']);
    Route::get('/{id}', [ProvinceController::class, 'show']);
    Route::put('/{id}', [ProvinceController::class, 'update']);
    Route::delete('/{id}', [ProvinceController::class, 'destroy']);
});

Route::prefix('cities')->group(function () {
    Route::get('/', [CityController::class, 'index']);
    Route::get('/provice/{id}', [CityController::class, 'getByProvince']);
    Route::post('/', [CityController::class, 'store']);
    Route::get('/{id}', [CityController::class, 'show']);
    Route::put('/{id}', [CityController::class, 'update']);
    Route::delete('/{id}', [CityController::class, 'destroy']);
});

Route::prefix('userFarmers')->group(function () {
    Route::get('/{cropId}', [UserFarmerController::class, 'index']);
    Route::post('/{cropId}', [UserFarmerController::class, 'store']);
    Route::post('/new/{cropId}', [UserFarmerController::class, 'storeNew']);
    Route::post('/import/{cropId}', [UserFarmerController::class, 'import']);
    Route::post('/images/{cropId}', [UserFarmerController::class, 'images']);
    Route::get('/{cropId}/{id}', [UserFarmerController::class, 'show']);
    Route::put('/{cropId}/{id}', [UserFarmerController::class, 'update']);
    Route::put('/updateByType/{cropId}/{id}', [UserFarmerController::class, 'updateByType']);
    Route::delete('/{id}', [UserFarmerController::class, 'destroy']);
});

Route::prefix('farmerCards')->group(function () {
    Route::get('/', [FarmerCardController::class, 'index']);
    Route::post('/', [FarmerCardController::class, 'store']);
    Route::get('/{id}', [FarmerCardController::class, 'show']);
    Route::put('/{id}', [FarmerCardController::class, 'update']);
    Route::delete('/{id}', [FarmerCardController::class, 'destroy']);
});

Route::prefix('locks')->group(function () {
    Route::post('/gps', [LocksGPSController::class, 'lock_gps']);
    Route::post('/sowing', [LocksGPSController::class, 'lock_sowing']);
});

Route::prefix('harvestPlans')->group(function () {
    Route::get('/{cropId}', [HarvestPlansController::class, 'index']);
    Route::get('/detail/{harvestId}', [HarvestPlansController::class, 'show']);
    Route::post('/update/{harvestId}/{typeId}', [HarvestPlansController::class, 'update']);
    Route::post('/separate/{harvestId}/{typeId}', [HarvestPlansController::class, 'separate']);
    Route::get('/detailDate/{cropId}/{date}/{type}', [HarvestPlansController::class, 'detailDate']);
    Route::get('/detailFarmer/{cropId}/{date}/{areaId}/{brokerId}/{itemInputId}/{age}/{matType}', [HarvestPlansController::class, 'farmerDetails']);
});