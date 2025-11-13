<?php

namespace App\Exports;

use App\Models\User_farmer;
use App\Models\Sowing;
use App\Models\Farmer_cards;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Log;

class FarmerExport implements FromCollection, WithHeadings, WithMapping
{
    protected $cropId;
    protected $sowingCountMap;
    protected $farmerCardMap;

    public function __construct($cropId)
    {
        $this->cropId = $cropId;
        $this->prepareSowingCounts();
        $this->prepareFarmerCards();
    }

    public function headings(): array
    {
        return ['Broker', 'หัวจุด', 'บ้าน', 'ลูกสวน', 'เลขบัตร', 'จำนวนแปลง', 'หัวหน้าส่งเสริม', 'ส่งเสริม', 'link บัตร'];
    }
        
    public function map($userFarmer): array
    {
        
        $farmer = $userFarmer->farmer;
        $sowingCount = $this->sowingCountMap[$userFarmer->id] ?? 0;
        $cardLink = $this->farmerCardMap[$farmer->id] ?? '';

        return [
            $userFarmer->broker->code ?? '',
            optional($userFarmer->head)->fname . ' ' . optional($userFarmer->head)->lname,
            $userFarmer->sowing_city,
            $farmer->fname . ' ' . $farmer->lname,
            "'" . $farmer->citizenid,
            $sowingCount,
            optional($userFarmer->manager)->fname . ' ' . optional($userFarmer->manager)->lname,
            optional($userFarmer->user)->fname . ' ' . optional($userFarmer->user)->lname,
            $cardLink,
        ];
    }
        
    protected function prepareSowingCounts()
    {
        $sowingData = Sowing::selectRaw('user_farmer_id, COUNT(id) as usesowing')
            ->where('crop_id', $this->cropId)
            ->groupBy('user_farmer_id')
            ->pluck('usesowing', 'user_farmer_id');

        $this->sowingCountMap = $sowingData->toArray();
    }

    protected function prepareFarmerCards()
    {
        $farmerIds = User_farmer::where('crop_id', $this->cropId)->pluck('farmer_id');

        $cards = Farmer_cards::whereIn('farmer_id', $farmerIds)->get();

        $map = [];
        foreach ($cards as $card) {
            $map[$card->farmer_id] = url('attachs/farmercard/' . $card->attach_path);
        }

        $this->farmerCardMap = $map;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {    
        return User_farmer::with(['broker', 'head', 'manager', 'user', 'farmer'])
        ->where('crop_id', $this->cropId)
        ->get()
        ->filter(function ($item) {
            return $item->farmer !== null;
        });
    }
}
