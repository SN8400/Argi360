@extends('Layouts.app')

@section('topic')
    ADD Crop
@endsection

@section('content')

    <form id="cropForm">
        @csrf

        <div class="row">
            <div class="col-xl-4 col-md-6 mb-3">
                <label><strong>Name</strong></label>
                <input type="text" name="name" class="form-control">
                <div class="invalid-feedback" id="error-name"></div>
            </div>

            <div class="col-xl-4 col-md-6 mb-3">
                <label><strong>Sap Code</strong></label>
                <input type="text" name="sap_code" class="form-control">
                <div class="invalid-feedback" id="error-sap_code"></div>
            </div>

            <div class="col-xl-4 col-md-12 mb-3">
                <label><strong>Link URL</strong></label>
                <input type="text" name="linkurl" class="form-control">
                <div class="invalid-feedback" id="error-linkurl"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label><strong>Start Date</strong></label>
                <input type="date" name="startdate" class="form-control">
                <div class="invalid-feedback" id="error-startdate"></div>
            </div>

            <div class="col-md-6 mb-3">
                <label><strong>End Date</strong></label>
                <input type="date" name="enddate" class="form-control">
                <div class="invalid-feedback" id="error-enddate"></div>
            </div>
        </div>

        <div class="mb-3">
            <label><strong>Max Per Day</strong></label>
            <input type="number" name="max_per_day" class="form-control">
            <div class="invalid-feedback" id="error-max_per_day"></div>
        </div>

        <div class="mb-3">
            <label><strong>Details</strong></label>
            <textarea name="details" rows="3" class="form-control" placeholder="Enter details"></textarea>
            <div class="invalid-feedback" id="error-details"></div>
        </div>

        <div class="row align-items-center">
            <div class="col-md-2">
                <a href="{{ route('CropMaster') }}" class="btn btn-danger w-100">Back</a>
            </div>
            <div class="col-md-2 ms-auto">
                <button type="button" class="btn btn-success w-100" onclick="OnCreate()">Save</button>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    function OnCreate() {
        // Clear old errors
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        let formData = {
            name: $('input[name="name"]').val(),
            sap_code: $('input[name="sap_code"]').val(),
            linkurl: $('input[name="linkurl"]').val(),
            startdate: $('input[name="startdate"]').val(),
            enddate: $('input[name="enddate"]').val(),
            max_per_day: $('input[name="max_per_day"]').val(),
            details: $('textarea[name="details"]').val(),
        };

        $.ajax({
            url: '/api/crops',
            type: 'POST',
            data: formData,
            success: function (res) {
                alert("✅ บันทึกสำเร็จ");
                window.location.href = "{{ route('CropMaster') }}";
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
