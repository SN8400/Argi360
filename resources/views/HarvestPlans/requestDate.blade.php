@extends('Layouts.app')
@section('title', 'Edit Farmer')

@section('content')
    <style>
        #change_date {
            background-color: #e9f5f9; /* สีฟ้าอ่อนนุ่มตา */
            border: 2px solid #0d6efd; /* ขอบสีฟ้า Bootstrap */
            border-radius: 6px;
            box-shadow: 0 0 5px rgba(13, 110, 253, 0.5);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        #change_date:focus {
            background-color: #d0ebff; /* ฟ้าสดใสขึ้น */
            border-color: #084298;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.8);
            outline: none;
        }
    </style>
    <input type="hidden" id="cropId" value="{{ $cropId ?? '' }}">
    <input type="hidden" id="harvestId" value="{{ $harvestId ?? '' }}">
    <input type="hidden" id="status" value="{{ $status ?? '' }}">
    <input type="hidden" id="role" value="{{ \App\Helpers\RoleHelper::getGroupByRole(Auth::user()->group_id) ?? '-' }}">
    <h2>ขอแยกวันเก็บเกี่ยว</h2>
    <h2>หัวหน้ากลุ่ม: <span id="area_name"></span>/<span id="broker_name"></h2>
    <h2>Farmer: <span id="farmer_name"></h2>
    <h2>แปลง : <span id="sowing_name"></span> ขนาด <span id="sowing_value"></span> ไร่ <span id="yield_rate"></span> ตัน </h2>
    <h2>วันที่เก็บเกี่ยว : <span id="date_est">ตัน</h2>
    <form id="brokerForm">
        @csrf
        <div class="row">
            <div class="col-md-3 mb-3">
                <label><strong>วันที่ต้องการแยกไป</strong></label>
                <input type="text" name="change_date" id="change_date" class="form-control">
                <div class="invalid-feedback" id="error-change_date"></div>
            </div>

            <div class="col-md-3 mb-3">
                <label><strong>สาเหตุการย้าย</strong></label>
                <input type="text" name="note" id="note" class="form-control">
                <div class="invalid-feedback" id="error-note"></div>
            </div>
        </div>

        <div class="row align-items-center">
            <div class="col-md-2">
                <a href="{{ route('HarvestPlans', ['id' => $cropId]) }}" class="btn btn-danger w-100">Back</a>
            </div>
            <div class="col-md-2 ms-auto">
                <button type="button" class="btn btn-success w-100" onclick="onSenddingData()">Save</button>
            </div>
        </div>
    </form>

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    let areaMap = [];
    let brokerMap = [];
    let inputItemMap = [];
    let time_selected;

    $(document).ready(function () {
        var cropId = $('#cropId').val();
        var harvestId = $('#harvestId').val();
        var role = document.getElementById('role').value;
        time_selected = flatpickr("#change_date", {
            dateFormat: "ํY/m/d",
            defaultDate: new Date(),
        });

        $.ajax({
            url: '/api/harvestPlans/detail/' + harvestId,
            method: 'GET',
            success: function(res) {
                $('#area_name').text(res.data.area.name);
                $('#broker_name').text(res.data.broker.fname + " " + res.data.broker.lname);
                $('#farmer_name').text(res.data.farmer.fname + " " + res.data.farmer.lname);
                $('#sowing_name').text(res.data.sowing.name);
                $('#sowing_value').text(res.data.sowing.current_land);
                $('#date_est').text(res.data.date_est);
                if(res.data.sowing.yield_rate7){
                    $('#yield_rate').text(parseInt(parseInt(res.data.sowing.current_land) * parseInt(parseInt(res.data.sowing.yield_rate7) / 1000)).toFixed(2));
                }
                else{
                    $('#yield_rate').text(parseInt(parseInt(res.data.sowing.current_land) * parseInt(parseInt(res.data.sowing.yield_rate) / 1000)).toFixed(2));
                }
                
                console.log(res);
            },
            error: function(xhr) {
                alert('Error fetching farmer data.');
            }
        });
    });

    function onSenddingData() {
        var cropId = $('#cropId').val();
        var harvestId = $('#harvestId').val();        
        let change_date = $('#change_date').val();
        let note = $('#note').val();

         $.ajax({
            url: '/api/harvestPlans/update/' + harvestId + '/reject',
            method: 'POST',
            data: { cropId, change_date, note }
            success: function(res) {
                console.log(res);
            },
            error: function(xhr) {
                alert('Error fetching farmer data.');
            }
        });
    }
</script>
