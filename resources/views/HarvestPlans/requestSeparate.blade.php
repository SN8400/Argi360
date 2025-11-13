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
                <label><strong>ประเภทสินค้า</strong></label>
                <select name="mat_type" id="mat_type" class="form-control">
                    <option value="N">ปรกติ</option>
                    <option value="C">Cluster</option>
                    <option value="S">รีดเมล็ด</option>
                    <option value="R">แยกตัดเกรด</option>
                    <option value="T">ปรกติหัวแปลง</option>
                    <option value="O">อื่นๆ</option>
                </select>
                <div class="invalid-feedback" id="error-mat_type"></div>
            </div>

            <div class="col-md-3 mb-3">
                <label><strong>วิธีการเก็บ</strong></label>
                <select name="harvest_type" id="harvest_type" class="form-control">
                    <option value="0">==เลือกวิธีการเก็บ==</option>
                </select>
                <div class="invalid-feedback" id="error-harvest_type"></div>
            </div>

            <div class="col-md-3 mb-3">
                <label><strong>ปริมาณผลผลิตจากแผน (ตัน)</strong></label>
                <input type="text" name="value_est" id="value_est" class="form-control">
                <div class="invalid-feedback" id="error-value_est"></div>
            </div>

            <div class="col-md-3 mb-3">
                <label><strong>ปริมาณผลผลิตก่อนเก็บ (ตัน)</strong></label>
                <input type="text" name="value_bf_harvest" id="value_bf_harvest" class="form-control">
                <div class="invalid-feedback" id="error-value_bf_harvest"></div>
            </div>

            <div class="col-md-3 mb-3">
                <label><strong>ปริมาณผลผลิตออกจากสวน (ตัน)</strong></label>
                <input type="text" name="value_act" id="value_act" class="form-control">
                <div class="invalid-feedback" id="error-value_act"></div>
            </div>

            <div class="col-md-3 mb-3">
                <label><strong>สาเหตุการแยก</strong></label>
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
        // time_selected = flatpickr("#change_date", {
        //     dateFormat: "ํY/m/d",
        //     defaultDate: new Date(),
        // });

        $.ajax({
            url: '/api/harvestPlans/detail/' + harvestId,
            method: 'GET',
            success: function(res) {

                time_selected = flatpickr("#change_date", {
                    dateFormat: "Y/m/d",
                    defaultDate: new Date(res.data?.harvest_move_q?.to_date || Date.now()),
                });
                $('#area_name').text(res.data.area.name);
                $('#broker_name').text(res.data.broker.fname + " " + res.data.broker.lname);
                $('#farmer_name').text(res.data.farmer.fname + " " + res.data.farmer.lname);
                $('#sowing_name').text(res.data.sowing.name);
                $('#sowing_value').text(res.data.sowing.current_land);
                $('#date_est').text(res.data.date_est);
                $('#note').val(res.data?.harvest_move_q?.note);
                document.getElementById('mat_type').value = res.data?.harvest_move_q?.mat_type || 'N';

                if(res.data.sowing.yield_rate7){
                    $('#yield_rate').text(parseInt(parseInt(res.data.sowing.current_land) * parseInt(parseInt(res.data.sowing.yield_rate7) / 1000)).toFixed(2));
                }
                else{
                    $('#yield_rate').text(parseInt(parseInt(res.data.sowing.current_land) * parseInt(parseInt(res.data.sowing.yield_rate) / 1000)).toFixed(2));
                }
                
                $('#value_est').val(parseInt(parseInt(res.data.harvest_move_q?.value_est ?? 0) / 1000).toFixed(2));
                $('#value_bf_harvest').val(parseInt(parseInt(res.data.harvest_move_q?.value_bf_harvest ?? 0) / 1000).toFixed(2));
                $('#value_act').val(parseInt(parseInt(res.data.harvest_move_q?.value_act ?? 0) / 1000).toFixed(2));
       
                const havestBy = res.data.harvest_move_q?.havest_by || '0';

                let harvest_type = $('#harvest_type');
                res.harvestTypes.forEach(element => {   
                    harvest_type.append($('<option>', {
                        value: element.id,
                        text: element.name,
                        selected: element.id == havestBy
                    }));
                });
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
        let mat_type = $('#mat_type').val();
        let harvest_type = $('#harvest_type').val();
        let value_est = $('#value_est').val();
        let value_bf_harvest = $('#value_bf_harvest').val();
        let value_act = $('#value_act').val();
        let note = $('#note').val();
        let data = {
                crop_id: cropId,
                harvest_id: harvestId,
                change_date: change_date,
                mat_type:mat_type,
                havest_by: harvest_type,
                value_est: value_est,
                value_bf_harvest: value_bf_harvest,
                value_act: value_act,
                note: note
            };
            console.log(data);
         $.ajax({
            url: '/api/harvestPlans/separate/' + harvestId +'/M',
            method: 'POST',
            data: data ,
            success: function(res) {
                console.log(res);
                alert("✅ บันทึกสำเร็จ");
                window.location.href = "{{ route('HarvestPlans', ['id' => $cropId]) }}";
            },
            error: function(xhr) {
                alert('Error fetching farmer data.');
            }
        });
    }
</script>
