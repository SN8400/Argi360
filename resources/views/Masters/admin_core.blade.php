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
    <h4 class="text-center"><strong>Admin Core</strong></h4>
    {{-- <h4 class="text-center">Admin Core <strong> {{ session('fullName') }}</strong></h4> --}}
    <hr>
    <br>
    <div class='row'>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('Users') }}" class="grow_state itemhover">
                <i class='bx bxs-group'></i> User Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('roleMaster') }}" class="grow_state itemhover">
                <i class='bx bxs-user-circle'></i> Role Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('CropMaster') }}" class="grow_state itemhover">
                </i> <i class='bx bxs-leaf'></i> Crops Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('InputItems') }}" class="grow_state itemhover">
                <i class='bx bxs-coffee-bean'></i> Input Item Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('Brokers') }}" class="grow_state itemhover">
                <i class='bx bxs-user'></i> Brokers Management
            </a>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('Growstate') }}" class="grow_state itemhover">
                <i class='bx bx-map-alt'></i> GrowStages Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('Checklist') }}" class="grow_state itemhover">
                <i class='bx bxs-edit'></i> Check List Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('Heads') }}" class="grow_state itemhover">
                <i class='bx bx-heading'></i> Head Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('Chemicals') }}" class="grow_state itemhover">
                <i class='bx bxs-spa'></i> Chemical Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('standardMaster') }}" class="grow_state itemhover">
                Chemical Standard Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('tmpSchedules') }}" class="grow_state itemhover">
                Template Schedule Management
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <a href="{{ route('crop_list') }}" class="grow_state itemhover">
                Crops List
            </a>
        </div>
    </div>

@endsection
