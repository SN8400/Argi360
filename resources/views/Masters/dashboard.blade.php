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
    <h4 class="text-center"><strong>Dashboard {{ \App\Helpers\RoleHelper::getDepartmentByRole(Auth::user()->group_id) ?? '-' }}</strong></h4>
    <hr>
    <br>
@endsection
