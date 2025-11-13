@extends('layouts.app')

@section('topic')
    ADD Broker
@endsection

@section('content')

<form id="brokerForm">
    @csrf

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
            <button type="button" class="btn btn-success w-100" onclick="onCreateBroker()">Save</button>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
function onCreateBroker() {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').html('');

    let formData = $('#brokerForm').serialize();

    $.ajax({
        url: "/api/brokers",
        type: 'POST',
        data: formData,
        success: function (res) {
            alert("✅ บันทึกสำเร็จ");
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
                alert("❌ เกิดข้อผิดพลาดในการบันทึก");
            }
        }
    });
}
</script>
@endpush
