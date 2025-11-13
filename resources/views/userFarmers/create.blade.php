@extends('layouts.app')

@section('topic')
    Add User Farmer
@endsection

@section('content')
    <input type="hidden" id="cropId" value="{{ $cropId ?? '' }}">

<form id="userFarmerForm">
    @csrf

    <div class="row">
        <!-- 1. Broker -->
        <div class="col-md-4 mb-3">
            <label><strong>Broker</strong></label>
            <select name="broker_id" id="broker_id" class="form-control">
                <option value="">Select Broker</option>
            </select>
            <div class="invalid-feedback" id="error-broker_id"></div>
        </div>

        <!-- 2. พื้นที่ (จาก broker.area) -->
        <div class="col-md-4 mb-3">
            <label><strong>Area</strong></label>
            <input type="text" name="area" id="area" class="form-control" readonly>
            <input type="hidden" name="area_id" id="area_id">
        </div>

        <!-- 3. หัวจุด -->
        <div class="col-md-4 mb-3">
            <label><strong>หัวจุด</strong></label>
            <select name="head_id" id="head_id" class="form-control">
                <option value="">Select หัวจุด</option>
            </select>
            <div class="invalid-feedback" id="error-head_id"></div>
        </div>

        <!-- 4. ส่งเสริม -->
        <div class="col-md-4 mb-3">
            <label><strong>ส่งเสริม</strong></label>
            <select name="user_id" id="user_id" class="form-control">
                <option value="">Select ส่งเสริม</option>
            </select>
            <div class="invalid-feedback" id="error-user_id"></div>
        </div>

        <!-- 5. หัวหน้า -->
        <div class="col-md-4 mb-3">
            <label><strong>หัวหน้า</strong></label>
            <select name="manager_id" id="manager_id" class="form-control">
                <option value="">Select หัวหน้า</option>
            </select>
            <div class="invalid-feedback" id="error-manager_id"></div>
        </div>

        <!-- 6. ทีมพ่นยา -->
        <div class="col-md-4 mb-3">
            <label><strong>ทีมพ่นยา</strong></label>
            <select name="review_id" id="review_id" class="form-control">
                <option value="">Select ทีมพ่นยา</option>
            </select>
            <div class="invalid-feedback" id="error-review_id"></div>
        </div>

        <!-- 7. ลูกสวน -->
        <div class="col-md-4 mb-3">
            <label><strong>ลูกสวน</strong></label>
            <input type="text" name="farmer_search" id="farmer_search" class="form-control" autocomplete="on">
            <input type="hidden" name="farmer_id" id="farmer_id">
            <div class="invalid-feedback" id="error-farmer_id"></div>
        </div>

        <!-- 8. บ้านที่ปลูก -->
        <div class="col-md-4 mb-3">
            <label><strong>บ้านที่ปลูก</strong></label>
            <input type="text" name="sowing_city" class="form-control">
            <div class="invalid-feedback" id="error-sowing_city"></div>
        </div>
    </div>

    <div class="row align-items-center">
        <div class="col-md-2">
            <a href="{{ route('userFarmers', ['cropId' => $cropId]) }}" class="btn btn-danger w-100">Back</a>
        </div>
        <div class="col-md-2 ms-auto">
            <button type="button" class="btn btn-success w-100" onclick="onCreateUserFarmer()">Save</button>
        </div>
    </div>
</form>

@endsection

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let farmerItems = [];

    $(document).ready(function () {
        var cropId = $('#cropId').val();
        // 1. Load Brokers
        $.get('/api/brokers', function (res) {
            res.data.forEach(broker => {
                $('#broker_id').append(new Option(`${broker.code} - ${broker.fname} ${broker.lname}`, broker.id));
            });
        });

        // 4-6. Load Users for ส่งเสริม, หัวหน้า, ทีมพ่นยา
        $.get('/api/users', function (res) {
            res.data.forEach(user => {
                const option = new Option(`${user.fname} ${user.lname}`, user.id);
                $('#user_id, #manager_id, #review_id').append(option.cloneNode(true));
            });
        });

        // 2. เมื่อเลือก broker, โหลดพื้นที่และหัวจุด
        $('#broker_id').on('change', function () {
            const brokerId = $(this).val();
            if (!brokerId) {
                $('#area').val('');
                $('#head_id').empty().append('<option value="">Select หัวจุด</option>');
                return;
            }

            // ดึงพื้นที่จาก broker
            $.get(`/api/brokers/${brokerId}`, function (res) {
                $('#area').val(res.data.address2);
                $('#area_id').val(res.data.id);
                
            });

            // ดึงหัวจุดจาก broker
            $.get(`/api/brokerHead/getListByCrop/${cropId}/${brokerId}`, function (res) {
                $('#head_id').empty().append('<option value="">Select หัวจุด</option>');
                res.data.forEach(item => {
                    $('#head_id').append(new Option(`${item.head.fname} ${item.head.lname}`, item.head.id));
                });
            });
        });

        $.get('/api/farmers', function (res) {
            console.log("Selected res:", res);
            // map ข้อมูลให้อยู่ในรูปแบบที่ autocomplete ใช้ได้
            farmerItems = res.data.map(function(item) {
                return {
                    label: item.fname + " " + item.lname, // หรือเปลี่ยนเป็น field ที่คุณต้องการแสดง
                    value: item.fname + " " + item.lname, // หรือ value ที่ต้องการใส่ใน input
                    data: item
                };
            });

            $("#farmer_search").autocomplete({
                source: farmerItems,
                minLength: 2, // ใส่เพื่อให้เริ่มค้นเมื่อพิมพ์ครบ 2 ตัวอักษร
                select: function(event, ui) {
                    $('#farmer_id').val(ui.item.data.id)
                }
            });
        });

        // handle click
        $(document).on('click', '.autocomplete-item', function () {
            $('#farmer_id').val($(this).data('id'));
            $('#farmer_search').val($(this).text());
            $('.autocomplete-list').remove();
        });

        // clear list if clicked outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#farmer_search').length) {
                $('.autocomplete-list').remove();
            }
        });

        function showAutocompleteList($input, html) {
            $('.autocomplete-list').remove();
            const $list = $('<div class="autocomplete-list list-group shadow position-absolute w-100"></div>').html(html);
            $input.after($list);
        }
    });

    // ฟังก์ชันบันทึก
    function onCreateUserFarmer() {
        var cropId = $('#cropId').val();

        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        const formData = {
            broker_id: $('#broker_id').val(),
            head_id: $('#head_id').val(),
            farmer_id: $('#farmer_id').val(),
            user_id: $('#user_id').val(),
            manager_id: $('#manager_id').val(),
            review_id: $('#review_id').val(),
            area_id: $('#area_id').val(),
            status: 'เก่า',
            sowing_city: $('input[name="sowing_city"]').val(),
        };
        console.log("formData:", formData);
        $.ajax({
            url: '/api/userFarmers/' + cropId,
            method: 'POST',
            data: formData,
            success: function () {
                alert('✅ บันทึกสำเร็จ');
                window.location.href = "{{ route('userFarmers', ['cropId' => $cropId]) }}";
            },
            error: function (res) {
                if (res.status === 422) {
                    const errors = res.responseJSON.errors;
                    for (let field in errors) {
                        $('[name="' + field + '"]').addClass('is-invalid');
                        $('#error-' + field).html(errors[field][0]);
                    }
                } else {
                    alert('❌ เกิดข้อผิดพลาดในการบันทึก');
                }
            }
        });
    }
</script>
