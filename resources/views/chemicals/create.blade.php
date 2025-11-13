@extends('layouts.app')

@section('topic')
    Create Chemical
@endsection

@section('content')

<form id="chemicalForm">
    @csrf
    <input type="hidden" id="chemicalId" value="">

    <div class="row">
        <div class="col-md-4 mb-3">
            <label><strong>Code</strong></label>
            <input type="text" name="code" class="form-control">
            <div class="invalid-feedback" id="error-code"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Name</strong></label>
            <input type="text" name="name" class="form-control" required>
            <div class="invalid-feedback" id="error-name"></div>
        </div>

        <div class="col-md-12 mb-3">
            <label><strong>Details</strong></label>
            <textarea name="details" class="form-control" rows="3"></textarea>
            <div class="invalid-feedback" id="error-details"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Formula Code</strong></label>
            <input type="text" name="formula_code" class="form-control">
            <div class="invalid-feedback" id="error-formula_code"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Standard Code</strong></label>
            <select name="standard_code_id" id="standard_code_id" class="form-control">
                <option value="">Select Standard</option>
            </select>
            <div class="invalid-feedback" id="error-standard_code_id"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Unit</strong></label>
            <select name="unit_id" id="unit_id" class="form-control">
                <option value="">Select Unit</option>
            </select>
            <div class="invalid-feedback" id="error-unit_id"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Rate per Land</strong></label>
            <input type="number" step="0.01" name="rate_per_land" class="form-control">
            <div class="invalid-feedback" id="error-rate_per_land"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Big Unit</strong></label>
            <select name="bigunit_id" id="bigunit_id" class="form-control">
                <option value="">Select Big Unit</option>
            </select>
            <div class="invalid-feedback" id="error-bigunit_id"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Package per Big Unit</strong></label>
            <input type="number" step="0.01" name="package_per_bigunit" class="form-control">
            <div class="invalid-feedback" id="error-package_per_bigunit"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Chemical Type</strong></label>
            <input type="text" name="ctype" class="form-control">
            <div class="invalid-feedback" id="error-ctype"></div>
        </div>
    </div>

    <div class="row align-items-center">
        <div class="col-md-2">
            <a href="{{ route('Chemicals') }}" class="btn btn-danger w-100">Back</a>
        </div>
        <div class="col-md-2 ms-auto">
            <button type="button" class="btn btn-success w-100" onclick="OnCreateChemical()">Save</button>
        </div>
    </div>
</form>

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Load Units (for both unit_id and bigunit_id)
    $.get('/api/units', function(res) {
        $.each(res.data, function(_, item) {
            $('#unit_id, #bigunit_id').append(`<option value="${item.id}">${item.name}</option>`);
        });
    });

    // Load Standards
    $.get('/api/standards', function(res) {
        $.each(res.data, function(_, item) {
            $('#standard_code_id').append(`<option value="${item.id}">${item.standard_name}</option>`);
        });
    });
});

function OnCreateChemical() {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').html('');

    let formData = {
        code: $('input[name="code"]').val(),
        name: $('input[name="name"]').val(),
        details: $('textarea[name="details"]').val(),
        formula_code: $('input[name="formula_code"]').val(),
        standard_code_id: $('#standard_code_id').val(),
        unit_id: $('#unit_id').val(),
        rate_per_land: $('input[name="rate_per_land"]').val(),
        bigunit_id: $('#bigunit_id').val(),
        package_per_bigunit: $('input[name="package_per_bigunit"]').val(),
        ctype: $('input[name="ctype"]').val(),
        _token: $('input[name="_token"]').val()
    };

    $.ajax({
        url: '/api/chemicals',
        type: 'POST',
        data: formData,
        success: function(res) {
            alert("✅ สร้าง Chemical สำเร็จ");
            window.location.href = "{{ route('Chemicals') }}";
        },
        error: function(res) {
            if (res.status === 422) {
                let errors = res.responseJSON.errors;
                for (let field in errors) {
                    let el = $('[name="' + field + '"]');
                    el.addClass('is-invalid');
                    $('#error-' + field).html(errors[field][0]);
                }
            } else {
                alert("❌ เกิดข้อผิดพลาดในการสร้าง Chemical");
                console.error(res);
            }
        }
    });
}
</script>
