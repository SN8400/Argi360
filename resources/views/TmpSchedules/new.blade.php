@extends('layouts.app')

@section('topic')
    ADD tmpSchedule
@endsection

@section('content')

<form id="tmpScheduleForm">
    @csrf

    <div class="row">
        <div class="col-md-6 mb-3">
            <label><strong>Templist</strong></label>
            <select name="templist_id" class="form-control" id="templistSelect"></select>
            <div class="invalid-feedback" id="error-templist_id"></div>
        </div>

        <div class="col-md-6 mb-3">
            <label><strong>Broker</strong></label>
            <select name="broker_id" class="form-control" id="brokerSelect"></select>
            <div class="invalid-feedback" id="error-broker_id"></div>
        </div>

        <div class="col-md-6 mb-3">
            <label><strong>Name</strong></label>
            <input type="text" name="name" class="form-control">
            <div class="invalid-feedback" id="error-name"></div>
        </div>

        <div class="col-md-12 mb-3">
            <label><strong>Detail</strong></label>
            <textarea name="details" class="form-control" rows="4"></textarea>
            <div class="invalid-feedback" id="error-detail"></div>
        </div>
    </div>

    <div class="row align-items-center">
        <div class="col-md-2">
            <a href="{{ route('tmpSchedules') }}" class="btn btn-danger w-100">Back</a>
        </div>
        <div class="col-md-2 ms-auto">
            <button type="button" class="btn btn-success w-100" onclick="onCreateTmpSchedule()">Save</button>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Load templists
    $.get('/api/tmpSchedules', function (res) {
        let options = '<option value="">-- Select Templist --</option>';
        res.data.forEach(function (item) {
            options += `<option value="${item.id}">${item.name ?? 'Templist #' + item.id}</option>`;
        });
        $('#templistSelect').html(options);
    });

    // Load brokers
    $.get('/api/brokers', function (res) {
        console.log(res);
        let options = '<option value="">-- Select Broker --</option>';
        res.data.forEach(function (item) {
            options += `<option value="${item.id}">${item.code} ${item.fname} ${item.lname}</option>`;
        });
        $('#brokerSelect').html(options);
    });
});

function onCreateTmpSchedule() {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').html('');

    let formData = $('#tmpScheduleForm').serialize();
    console.log(formData);
    $.ajax({
        url: "/api/tmpSchedules",
        type: 'POST',
        data: formData,
        success: function (res) {
            if(res.status === 'success') {
                alert("✅ บันทึกสำเร็จ");
                window.location.href = "{{ route('tmpSchedules') }}";
            } else {
                alert("❌ บันทึกไม่สำเร็จ");
            }
        },
        error: function (res) {
            console.error(res);
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
