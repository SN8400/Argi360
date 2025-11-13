@extends('layouts.app')

@push('styles')
    <style>
        a {
            width: 200px;
        }

        .grow_state,
        .seed_code,
        .checklist_crop,
        .user_farmer,
        .map_matcode,
        .broker_area,
        .broker_head {
            border-radius: 10px;
            color: black;
            padding: 14px 25px;
            font-size: 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            width: 400px;
        }

        .grow_state {
            background-color: #D4F2D0;
        }

        .seed_code {
            background-color: #DED0F2;
        }

        .checklist_crop {
            background-color: #C8E4F4;
        }

        .user_farmer {
            background-color: #FEE9F0;
        }

        .map_matcode {
            background-color: #FFFAAE;
        }

        .broker_area {
            background-color: #A0CDED;
        }

        .broker_head {
            background-color: #F7B39C;
        }

        .itemhover:hover {
            background-color: #dddddd;
            color: black;
        }
    </style>
@endpush

@section('content')
    {{-- <h4>Crops <strong> {{ $name }}</strong></h4> --}}
    <hr>
    <br>
    <div class='row'>
        <div class='col-md-4 p-2'>
            <a href="{{ route('growstate', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bxs-leaf'></i> Grow state
            </a>
        </div>
        <div class='col-md-4 p-2'>
            <a href="{{ route('seedCode', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bxs-coffee-bean'></i> Seed Code
            </a>
        </div>
        <div class='col-md-4 p-2'>
            <a href="{{ route('crop_list') }}" class="grow_state itemhover">
                <i class='bx bxs-bug'></i> Check List Crops
            </a>
        </div>
        <div class='col-md-4 p-2'>
            <a href="#" class="broker_head itemhover">
                <i class='bx bxs-group'></i> Setup Farmers
            </a>
            </a>
        </div>
        <div class='col-md-4 p-2'>
            <a href="{{ route('map_matcodes', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bx-map-alt'></i> Map matcodes
            </a>
        </div>
        <div class='col-md-4 p-2'>
            <a href="{{ route('brokerArea', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bxs-map-pin'></i> Broker Areas
            </a>
        </div>
        <div class='col-md-4 p-2'>
            <a href="{{ route('brokerHead', ['id' => $id]) }}" class="grow_state itemhover p-2">
                <i class='bx bx-heading'></i> Broker_Heads
            </a>
        </div>
        <div class='col-md-4 p-2'>
            <a href="{{ route('plannings', ['id' => $id]) }}" class="grow_state itemhover p-2">
                <i class='bx bx-heading'></i> Planning
            </a>
        </div>
        <div class='col-md-4 p-2'>
            <a href="{{ route('seedPack', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bxs-leaf'></i> Seed Pack
            </a>
        </div>
    </div>


    <h4>แผนปฏิบัติงาน</h4>

    <div class='row'>
        <div class='col-md-4 p-2'>
            <a href="{{ route('tmpSchedules') }}" class="grow_state itemhover">
                <i class='bx bxs-briefcase-alt-2'></i> tmp_schedule
            </a>
        </div>
    </div>

@endsection
