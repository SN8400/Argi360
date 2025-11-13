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

        <!-- 1. คำนำหน้า -->
        <div class="col-md-4 mb-3">
            <label><strong>คำนำหน้า</strong></label>
            <select name="prefix" id="prefix" class="form-control">
                <option value="">Select คำนำหน้า</option>
                <option value="นาย">นาย</option>
                <option value="นาง">นาง</option>
                <option value="นางสาว">นางสาว</option>
            </select>
            <div class="invalid-feedback" id="error-prefix"></div>
        </div>

        <!-- 2. ชื่อ -->
        <div class="col-md-4 mb-3">
            <label><strong>ชื่อ</strong></label>
            <input type="text" name="fname" id="fname" class="form-control">
            <div class="invalid-feedback" id="error-fname"></div>
        </div>

        <!-- 3. นามสกุล -->
        <div class="col-md-4 mb-3">
            <label><strong>นามสกุล</strong></label>
            <input type="text" name="lname" id="lname" class="form-control">
            <div class="invalid-feedback" id="error-lname"></div>
        </div>

        <!-- 4. เลขบัตรประชาชน -->
        <div class="col-md-4 mb-3">
            <label><strong>เลขบัตรประชาชน</strong></label>
            <input type="text" name="id_card" id="id_card" class="form-control" maxlength="13">
            <div class="invalid-feedback" id="error-id_card"></div>
        </div>

        <!-- 5. บ้านเลขที่ -->
        <div class="col-md-4 mb-3">
            <label><strong>บ้านเลขที่</strong></label>
            <input type="text" name="house_number" id="house_number" class="form-control">
            <div class="invalid-feedback" id="error-house_number"></div>
        </div>

        <!-- 6. หมู่ที่ -->
        <div class="col-md-4 mb-3">
            <label><strong>หมู่ที่</strong></label>
            <input type="text" name="moo" id="moo" class="form-control">
            <div class="invalid-feedback" id="error-moo"></div>
        </div>

        <!-- 7. ชื่อหมู่บ้าน -->
        <div class="col-md-4 mb-3">
            <label><strong>ชื่อหมู่บ้าน</strong></label>
            <input type="text" name="village_name" id="village_name" class="form-control">
            <div class="invalid-feedback" id="error-village_name"></div>
        </div>

        <!-- 8. จังหวัด -->
        <div class="col-md-4 mb-3">
            <label><strong>จังหวัด</strong></label>
            <select name="province_id" id="province_id" class="form-control">
                <option value="">Select จังหวัด</option>
            </select>
            <div class="invalid-feedback" id="error-province_id"></div>
        </div>

        <!-- 9. อำเภอ -->
        <div class="col-md-4 mb-3">
            <label><strong>อำเภอ</strong></label>
            <select name="city_id" id="city_id" class="form-control" disabled>
                <option value="">Select อำเภอ</option>
            </select>
            <div class="invalid-feedback" id="error-city_id"></div>
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

        // Load จังหวัด
        $.get('/api/provinces', function(res) {
            res.data.forEach(province => {
                $('#province_id').append(new Option(province.th_name, province.id));
            });
        });

        // เมื่อเปลี่ยนจังหวัด ให้โหลดอำเภอของจังหวัดนั้น
        $('#province_id').on('change', function () {
            const provinceId = $(this).val();
            $('#city_id').empty().append('<option value="">Select อำเภอ</option>');

            if (!provinceId) {
                $('#city_id').prop('disabled', true);
                return;
            }

            $('#city_id').prop('disabled', false);

            $.get(`/api/cities/provice/${provinceId}`, function(res) {
                console.log();
                res.data.forEach(city => {
                    $('#city_id').append(new Option(city.th_name, city.id));
                });
            });
        });
    });

    // ฟังก์ชันบันทึก
    function onCreateUserFarmer() {
        var cropId = $('#cropId').val();

        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        const formData = {
            broker_id: $('#broker_id').val(),
            head_id: $('#head_id').val(),
            user_id: $('#user_id').val(),
            manager_id: $('#manager_id').val(),
            review_id: $('#review_id').val(),
            area_id: $('#area_id').val(),
            status: 'ใหม่',
            prefix: $('#prefix').val(),
            fname: $('#fname').val(),
            lname: $('#lname').val(),
            id_card: $('#id_card').val(),
            house_number: $('#house_number').val(),
            moo: $('#moo').val(),
            village_name: $('#village_name').val(),
            province_id: $('#province_id').val(),
            city_id: $('#city_id').val(),
        };

        console.log("formData:", formData);

        $.ajax({
            url: '/api/userFarmers/new/' + cropId,
            method: 'POST',
            data: formData,
            success: function (res) {
                console.log("res:", res);
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
