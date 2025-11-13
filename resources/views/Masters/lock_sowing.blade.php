<!-- resources/views/index.blade.php -->
@extends('Layouts.app')

@section('title', 'Welcome Page')

@section('content')
    <style>
        #start_datetime, #end_datetime {
            background-color: #e9f5f9; /* สีฟ้าอ่อนนุ่มตา */
            border: 2px solid #0d6efd; /* ขอบสีฟ้า Bootstrap */
            border-radius: 6px;
            box-shadow: 0 0 5px rgba(13, 110, 253, 0.5);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        #start_datetime, #end_datetime:focus {
            background-color: #d0ebff; /* ฟ้าสดใสขึ้น */
            border-color: #084298;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.8);
            outline: none;
        }
    </style>
    <input type="hidden" id="crop_id" name="crop_id" value="{{ $id ?? '' }}">
    <h2>Lock แปลง เพื่อใช้งาน | Crop : <span id="crop_name"></span></h2>

    <div class="row">
        <div class="col-md-6">
            <label for="start_datetime" class="form-label">วันที่เริ่ม Lock</label>
            <input type="text" id="start_datetime" name="start_datetime" class="form-control" placeholder="กดที่นี่เพื่อเลือกวันและเวลาเริ่มล็อค">
        </div>

        <div class="col-md-6">
            <label for="end_datetime" class="form-label">วันที่สิ้นสุดการ Lock</label>
            <input type="text" id="end_datetime" name="end_datetime" class="form-control" placeholder="กดที่นี่เพื่อเลือกวันและเวลาสิ้นสุด">
        </div>

        <div class="col-md-9">
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="0">unactive</option>
                <option value="1" selected >active</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label invisible">Lock GPS</label>
            <a href="javascript:void(0)" class="btn btn-success form-control" onclick="openCreateModal()">Lock</a>
        </div>
    </div>
@endsection

<!-- Include CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- JavaScript -->
<script>
    let start_datetime;
    let end_datetime;
    window.addEventListener('load', function () {
        let crop_id = $('#crop_id').val();
        end_datetime = flatpickr("#end_datetime", {
            dateFormat: "ํY/m/d",
            time_24hr: true,
            defaultDate: new Date().fp_incr(30),
        });
        start_datetime = flatpickr("#start_datetime", {
            dateFormat: "ํY/m/d",
            time_24hr: true,
            defaultDate: "today",
        });
        $.ajax({
            url: '/api/crops/' + crop_id,
            type: 'GET',
            dataType: 'json',
            async: false, 
            success: function(data) {
                $('#crop_name').text(data.data.name);
            }
        });
    });

    // ดึง Date object ตัวแรกที่เลือกมา
    function getStartDate() {
        return flatpickr.formatDate(start_datetime.selectedDates[0], "Y-m-d") || null;
    }

    function getEndDate() {
        return flatpickr.formatDate(end_datetime.selectedDates[0], "Y-m-d") || null;
    }

    // ฟังก์ชันเรียกตอนกดปุ่ม Lock
    function openCreateModal() {
        const start_date = getStartDate();
        const end_date = getEndDate();
        let crop_id = $('#crop_id').val();
        let status = $('#status').val();

        if(start_date == null || end_date == null){
            alert("กรุณาเลือกวันและเวลาล็อค");
        }
        else{
            $.ajax({
                url: '/api/locks/sowing',
                type: 'POST',
                data: { crop_id, start_date, end_date, status },
                success: function(res) {
                    alert(res.message);              
                },
                error: function() {
                    alert("เกิดข้อผิดพลาดในการโหลดข้อมูล");
                }
            });
        }
    }
</script>
