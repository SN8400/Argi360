@extends('layouts.app')

@section('topic')
    Edit Broker
@endsection

@section('content')

<form id="brokerForm">
    @csrf
    <input type="hidden" id="brokerId" name="brokerId"  value="{{ $id ?? '' }}">

    <div class="row">
        <div class="col-md-3 mb-3">
            <label><strong>Code</strong></label>
            <input type="text" name="code" class="form-control">
            <div class="invalid-feedback" id="error-code"></div>
        </div>

        <div class="col-md-3 mb-3">
            <label><strong>Init</strong></label>
            <input type="text" name="init" class="form-control">
            <div class="invalid-feedback" id="error-init"></div>
        </div>

        <div class="col-md-3 mb-3">
            <label><strong>First Name</strong></label>
            <input type="text" name="fname" class="form-control">
            <div class="invalid-feedback" id="error-fname"></div>
        </div>

        <div class="col-md-3 mb-3">
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
            <label><strong>Address 1</strong></label>
            <input type="text" name="address1" class="form-control">
            <div class="invalid-feedback" id="error-address1"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Address 2</strong></label>
            <input type="text" name="address2" class="form-control">
            <div class="invalid-feedback" id="error-address2"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Address 3</strong></label>
            <input type="text" name="address3" class="form-control">
            <div class="invalid-feedback" id="error-address3"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Sub Cities</strong></label>
            <input type="text" name="sub_cities" class="form-control">
            <div class="invalid-feedback" id="error-sub_cities"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>City ID</strong></label>
            <input type="text" name="city_id" class="form-control">
            <div class="invalid-feedback" id="error-city_id"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Province ID</strong></label>
            <input type="text" name="province_id" class="form-control">
            <div class="invalid-feedback" id="error-province_id"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Location</strong></label>
            <input type="text" name="loc" class="form-control">
            <div class="invalid-feedback" id="error-loc"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Broker Color</strong></label><br>
            <input type="color" name="broker_color" class="form-control" style="width: 60px;">
            <div class="invalid-feedback" id="error-broker_color"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Created By</strong></label>
            <input type="text" name="createdBy" class="form-control">
            <div class="invalid-feedback" id="error-createdBy"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Modified By</strong></label>
            <input type="text" name="modifiedBy" class="form-control">
            <div class="invalid-feedback" id="error-modifiedBy"></div>
        </div>
    </div>

    <div class="row align-items-center">
        <div class="col-md-2">
            <a href="{{ route('Brokers') }}" class="btn btn-danger w-100">Back</a>
        </div>
        <div class="col-md-2 ms-auto">
            <button type="button" class="btn btn-primary w-100" onclick="onUpdateBroker()">Update</button>
        </div>
    </div>
</form>

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>

$(document).ready(function() {
    let id = $('#brokerId').val();
    if(id){
        $.ajax({
            url: '/api/brokers/' + id,
            type: 'GET',
            success: function(res) {
                if (res.status === 'success') {
                    let item = res.data;
                    $('input[name="code"]').val(item.code);
                    $('input[name="init"]').val(item.init);
                    $('input[name="fname"]').val(item.fname);
                    $('input[name="lname"]').val(item.lname);
                    $('input[name="citizenid"]').val(item.citizenid);
                    $('input[name="address1"]').val(item.address1);
                    $('input[name="address2"]').val(item.address2);
                    $('input[name="address3"]').val(item.address3);
                    $('input[name="sub_cities"]').val(item.sub_cities);
                    $('input[name="city_id"]').val(item.city_id);
                    $('input[name="province_id"]').val(item.province_id);
                    $('input[name="loc"]').val(item.loc);
                    $('input[name="broker_color"]').val(item.broker_color ?? '#000000');
                    $('input[name="createdBy"]').val(item.createdBy);
                    $('input[name="modifiedBy"]').val(item.modifiedBy);
                } else {
                    alert("ไม่พบข้อมูล");
                }
            },
            error: function() {
                alert("เกิดข้อผิดพลาดในการโหลดข้อมูล");
            }
        });
    }
    $.get('/api/brokers/id', function(res) {
        for (const key in res) {
            const el = $('[name="' + key + '"]');
            if (el.attr('type') === 'color') {
                el.val(res[key] || '#000000');
            } else {
                el.val(res[key]);
            }
        }
    });
});

function onUpdateBroker() {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').html('');

    let id = $('#brokerId').val();
    let formData = $('#brokerForm').serialize();

    $.ajax({
        url: '/api/brokers/' + id,
        type: 'PUT',
        data: formData,
        success: function () {
            alert("✅ แก้ไขสำเร็จ");
            window.location.href = "{{ route('Brokers') }}";
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
                alert("❌ เกิดข้อผิดพลาดในการแก้ไข");
            }
        }
    });
}
</script>
