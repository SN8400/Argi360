@extends('layouts.app')

@section('topic')
    ADD Farmer
@endsection

@section('content')

<form id="farmerForm">
    @csrf

    <div class="row">
        <div class="col-md-4 mb-3">
            <label><strong>Code</strong></label>
            <input type="text" name="code" class="form-control">
            <div class="invalid-feedback" id="error-code"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Prefix</strong></label>
            <select name="init" id="init" class="form-control">
                <option value="">Select Prefix</option>
                <option value="นาย">นาย</option>
                <option value="นาง">นาง</option>
                <option value="นางสาว">นางสาว</option>
            </select>
            <div class="invalid-feedback" id="error-init"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>First Name</strong></label>
            <input type="text" name="fname" class="form-control">
            <div class="invalid-feedback" id="error-fname"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Last Name</strong></label>
            <input type="text" name="lname" class="form-control">
            <div class="invalid-feedback" id="error-lname"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Citizen ID</strong></label>
            <input type="text" name="citizenid" class="form-control">
            <div class="invalid-feedback" id="error-citizenid"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Address Line 1</strong></label>
            <input type="text" name="address1" class="form-control">
            <div class="invalid-feedback" id="error-address1"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Address Line 2</strong></label>
            <input type="text" name="address2" class="form-control">
            <div class="invalid-feedback" id="error-address2"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Address Line 3</strong></label>
            <input type="text" name="address3" class="form-control">
            <div class="invalid-feedback" id="error-address3"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Sub District</strong></label>
            <input type="text" name="sub_cities" class="form-control">
            <div class="invalid-feedback" id="error-sub_cities"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>City</strong></label>
            <select name="city_id" id="city_id" class="form-control">
                <option value="">Select City</option>
            </select>
            <div class="invalid-feedback" id="error-city_id"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Province</strong></label>
            <select name="province_id" id="province_id" class="form-control">
                <option value="">Select Province</option>
            </select>
            <div class="invalid-feedback" id="error-province_id"></div>
        </div>
    </div>

    <div class="row align-items-center">
        <div class="col-md-2">
            <a href="{{ route('farmers') }}" class="btn btn-danger w-100">Back</a>
        </div>
        <div class="col-md-2 ms-auto">
            <button type="button" class="btn btn-success w-100" onclick="onCreateFarmer()">Save</button>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Load cities
        $.get('/api/cities', function(response) {
            let citySelect = $('#city_id');
            response.data.forEach(city => {
                citySelect.append($('<option>', {
                    value: city.id,
                    text: city.th_name
                }));
            });
        });

        // Load provinces
        $.get('/api/provinces', function(response) {
            let provinceSelect = $('#province_id');
            response.data.forEach(province => {
                provinceSelect.append($('<option>', {
                    value: province.id,
                    text: province.th_name
                }));
            });
        });
    });

    function onCreateFarmer() {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        let formData = {
            code: $('input[name="code"]').val(),
            init: $('#init').val(),
            fname: $('input[name="fname"]').val(),
            lname: $('input[name="lname"]').val(),
            citizenid: $('input[name="citizenid"]').val(),
            address1: $('input[name="address1"]').val(),
            address2: $('input[name="address2"]').val(),
            address3: $('input[name="address3"]').val(),
            sub_cities: $('input[name="sub_cities"]').val(),
            city_id: $('#city_id').val(),
            province_id: $('#province_id').val(),
        };

        $.ajax({
            url: '/api/farmers',
            type: 'POST',
            data: formData,
            success: function (res) {
                alert("✅ บันทึกสำเร็จ");
                // window.location.href = "{{ route('farmers') }}";
            },
            error: function (res) {
                if (res.status === 422) {
                    let errors = res.responseJSON.errors;
                    for (let field in errors) {
                        let el = $('[name="' + field + '"]');
                        el.addClass('is-invalid');
                        $('#error-' + field).html(errors[field][0]);
                    }
                } else {
                    alert("❌ เกิดข้อผิดพลาดในการบันทึก");
                }
            }
        });
    }
</script>
@endpush
