@extends('Layouts.app')
@section('title', 'Edit Farmer')

@section('content')
    <input type="hidden" id="farmerId" value="{{ $id ?? '' }}">
    <div class="row my-4">
        <div class="col-md-12">
            <form id="farmer-form" class="row g-3">
                @csrf
                <div class="col-md-2">
                    <label for="code" class="form-label">รหัสเกษตรกร</label>
                    <input type="text" class="form-control" id="code" name="code">
                </div>
                <div class="col-md-2">
                    <label for="init" class="form-label">คำนำหน้า</label>
                    <select id="init" name="init" class="form-select">
                        <option value="">-- เลือก --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="fname" class="form-label">ชื่อ</label>
                    <input type="text" class="form-control" id="fname" name="fname">
                </div>
                <div class="col-md-4">
                    <label for="lname" class="form-label">นามสกุล</label>
                    <input type="text" class="form-control" id="lname" name="lname">
                </div>
                <div class="col-md-3">
                    <label for="citizenid" class="form-label">เลขบัตร ปชช.</label>
                    <input type="text" class="form-control" id="citizenid" name="citizenid">
                </div>
                <div class="col-md-3">
                    <label for="address1" class="form-label">ที่อยู่ 1</label>
                    <input type="text" class="form-control" id="address1" name="address1">
                </div>
                <div class="col-md-3">
                    <label for="address2" class="form-label">ที่อยู่ 2</label>
                    <input type="text" class="form-control" id="address2" name="address2">
                </div>
                <div class="col-md-3">
                    <label for="address3" class="form-label">ที่อยู่ 3</label>
                    <input type="text" class="form-control" id="address3" name="address3">
                </div>
                <div class="col-md-3">
                    <label for="sub_cities" class="form-label">ตำบล</label>
                    <input type="text" class="form-control" id="sub_cities" name="sub_cities">
                </div>
                <div class="col-md-3">
                    <label for="city_id" class="form-label">อำเภอ</label>
                    <select id="city_id" name="city_id" class="form-select"></select>
                </div>
                <div class="col-md-3">
                    <label for="province_id" class="form-label">จังหวัด</label>
                    <select id="province_id" name="province_id" class="form-select"></select>
                </div>
                <div class="col-12 mt-4">
                    <button type="button" class="btn btn-primary" onclick="submitFarmer()">บันทึกข้อมูล</button>
                    <a href="{{ route('farmers') }}" class="btn btn-secondary">กลับ</a>
                </div>
            </form>
        </div>
    </div>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>

    $(document).ready(function () {
        var farmerId = $('#farmerId').val();
        loadDropdowns();
            console.log(farmerId);
        if (farmerId) {
            loadFarmerData(farmerId);
        }
    });

    function loadDropdowns() {
        const inits = ['นาย', 'นาง', 'นางสาว'];
        inits.forEach(value => {
            $('#init').append(`<option value="${value}">${value}</option>`);
        });

        $.get('/api/cities', function (res) {
            res.data.forEach(city => {
                $('#city_id').append(`<option value="${city.id}">${city.th_name}</option>`);
            });
        });

        $.get('/api/provinces', function (res) {
            res.data.forEach(province => {
                $('#province_id').append(`<option value="${province.id}">${province.th_name}</option>`);
            });
        });
    }

    function loadFarmerData(id) {
        $.get(`/api/farmers/${id}`, function (res) {
            console.log(res);
            const data = res.data;
            $('#code').val(data.code);
            $('#init').val(data.init);
            $('#fname').val(data.fname);
            $('#lname').val(data.lname);
            $('#citizenid').val(data.citizenid);
            $('#address1').val(data.address1);
            $('#address2').val(data.address2);
            $('#address3').val(data.address3);
            $('#sub_cities').val(data.sub_cities);
            $('#city_id').val(data.city_id);
            $('#province_id').val(data.province_id);
        });
    }

    function submitFarmer() {
        var farmerId = $('#farmerId').val();
        const payload = {
            code: $('#code').val(),
            init: $('#init').val(),
            fname: $('#fname').val(),
            lname: $('#lname').val(),
            citizenid: $('#citizenid').val(),
            address1: $('#address1').val(),
            address2: $('#address2').val(),
            address3: $('#address3').val(),
            sub_cities: $('#sub_cities').val(),
            city_id: $('#city_id').val(),
            province_id: $('#province_id').val()
        };

        const url = farmerId ? `/api/farmers/${farmerId}` : '/api/farmers';
        const method = farmerId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: payload,
            success: function (res) {
                alert('บันทึกสำเร็จ');
                window.location.href = "{{ route('farmers') }}";
            },
            error: function (xhr) {
                alert('เกิดข้อผิดพลาด');
                console.log(xhr.responseText);
            }
        });
    }
</script>
