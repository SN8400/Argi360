@extends('layouts.app')

@section('topic')
    TmpSchedule
@endsection

@section('content')

<form id="tmpScheduleForm">
    @csrf
 <input type="hidden" id="schedulesId" name="schedules_id" value="{{ $id ?? '' }}">
   
    <div class="row">
        <div class="col-md-6 mb-3">
            <label><strong>Crop</strong></label>
            <select name="crop_id" class="form-control" id="cropSelect"></select>
            <div class="invalid-feedback" id="error-crop_id"></div>
        </div>

        <div class="col-md-6 mb-3">
            <label><strong>Broker</strong></label>
            <select name="broker_id" class="form-control" id="brokerSelect"></select>
            <div class="invalid-feedback" id="error-broker_id"></div>
        </div>

        <div class="col-md-6 mb-3">
            <label><strong>Input Item</strong></label>
            <select name="input_item_id" class="form-control" id="itemInputSelect"></select>
            <div class="invalid-feedback" id="error-input_item_id"></div>
        </div>

        <div class="col-md-6 mb-3">
            <label><strong>Schedule</strong></label>
            <div id="schedule_text" class="font-weight-bold">—</div>
        </div>
    </div>

    <div class="row align-items-center">
        <div class="col-md-2">
            <a href="{{ route('tmpSchedules') }}" class="btn btn-danger w-100">Back</a>
        </div>
        <div class="col-md-2 ms-auto">
            <button type="button" class="btn btn-success w-100" onclick="onCloneTmpSchedule()">Clone</button>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    let schedulesId = $('#schedulesId').val();
    // Load templists
    $.get('/api/crops', function (res) {
        let options = '<option value="">-- Select Crop --</option>';
        res.data.forEach(function (item) {
            options += `<option value="${item.id}">${item.name}</option>`;
        });
        $('#cropSelect').html(options);
    });

    // Load brokers
    $.get('/api/brokers', function (res) {
        let options = '<option value="">-- Select Broker --</option>';
        res.data.forEach(function (item) {
            options += `<option value="${item.id}">${item.code} ${item.fname} ${item.lname}</option>`;
        });
        $('#brokerSelect').html(options);
    });

    // Load InputItems
    $.get('/api/InputItems', function (res) {
        let options = '<option value="">-- พืชทั้งหมด --</option>';
        res.data.forEach(function (item) {
            options += `<option value="${item.id}">${item.name}</option>`;
        });
        $('#itemInputSelect').html(options);
    });

    if (schedulesId) {
        $.ajax({
            url: '/api/tmpSchedules/' + schedulesId,
            method: 'GET',
            success: function(res) {
                if(res.status === 'success') {
                    $('#schedule_text').text(res.schedule.name);
                } else {
                    alert("Data not found.");
                }
            },
            error: function() {
                alert("Error loading data.");
            }
        });
    }
});

function onCloneTmpSchedule() {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').html('');

    let formData = $('#tmpScheduleForm').serialize();
    $.ajax({
        url: "/api/tmpSchedules/clone",
        type: 'POST',
        data: formData,
        success: function (res) {
            if(res.status === 'success') {
                alert("✅ บันทึกสำเร็จ");
                window.location.href = "{{ route('tmpSchedules') }}";
            } else {
                alert("❌ บันทึกไม่สำเร็จ");
            }
            console.log(res);
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
