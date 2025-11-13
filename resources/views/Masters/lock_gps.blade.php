<!-- resources/views/index.blade.php -->
@extends('Layouts.app')

@section('title', 'Welcome Page')

@section('content')
    <style>
        #datetime {
            background-color: #e9f5f9; /* สีฟ้าอ่อนนุ่มตา */
            border: 2px solid #0d6efd; /* ขอบสีฟ้า Bootstrap */
            border-radius: 6px;
            box-shadow: 0 0 5px rgba(13, 110, 253, 0.5);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        #datetime:focus {
            background-color: #d0ebff; /* ฟ้าสดใสขึ้น */
            border-color: #084298;
            box-shadow: 0 0 10px rgba(13, 110, 253, 0.8);
            outline: none;
        }
    </style>

    <input type="hidden" id="crop_id" name="crop_id" value="{{ $id ?? '' }}">
    <h2>Lock GPX เพื่อใช้งาน | Crop : <span id="crop_name"></span></h2>
    <div class="row">
            <div class="col-md-9">
                <label for="datetime" class="form-label">วันที่ Lock</label>
                <input type="text" id="datetime" name="datetime" class="form-control" placeholder="กดที่นี่เพื่อเลือกวันและเวลาล็อค">
            </div>
            <div class="col-md-3">
                <label for="datetime" class="form-label">Lock GPS</label>
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
    let time_selected;
    window.addEventListener('load', function () {
        let crop_id = $('#crop_id').val();
        time_selected = flatpickr("#datetime", {
            enableTime: true,
            time_24hr: true,
            dateFormat: "ํY/m/d H:i",
            defaultDate: new Date(),
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
function getSelectedDate() {
    return flatpickr.formatDate(time_selected.selectedDates[0], "Y-m-d H:i") || null;
}

// ฟังก์ชันเรียกตอนกดปุ่ม Lock
function openCreateModal() {
    let crop_id = $('#crop_id').val();
    const selected_date = getSelectedDate();
    if(selected_date == null){
        alert("กรุณาเลือกวันและเวลาล็อค");
    }
    else{
        $.ajax({
            url: '/api/locks/gps',
            type: 'POST',
            data: { crop_id, selected_date },
            success: function(res) {
                    alert(res.message);          
            },
            error: function(err) {
                console.error(err);
                alert("เกิดข้อผิดพลาดในการโหลดข้อมูล");
            }
        });
    }
}
</script>
