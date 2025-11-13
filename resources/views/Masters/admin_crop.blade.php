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
    <h4 class="text-center"><strong>Admin Crop</strong></h4>
    {{-- <h4 class="text-center">Admin Core <strong> {{ session('fullName') }}</strong></h4> --}}
    <hr>
    <br>
    <div class='row'>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('seedCode', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bxs-coffee-bean'></i> Seed Codes Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('seedPack', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bxs-box'></i> Seed Packs Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('Schedules', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bx-edit'></i> Schedule Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('lock_gps', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bx-map'></i> Lock GPS
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('lock_sowing', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bx-map'></i> Lock Sowing
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('farmers') }}" class="grow_state itemhover">
                <i class='bx bxs-user'></i> Farmer Management
            </a>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('plannings', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bx-map-alt'></i> Planning Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('brokerHead', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bxs-group'></i>Broker Head Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('brokerArea', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bx-map'></i>  Broker Area Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('map_matcodes', ['id' => $id]) }}" class="grow_state itemhover">
                <i class='bx bxs-spa'></i> Map Matcode Management
            </a>
        </div>
    </div>

@endsection
