@extends('layouts.app')

@section('topic')
    ADD Input Item
@endsection

@section('content')

<form id="inputItemForm">
    @csrf

    <div class="row">
        <div class="col-md-4 mb-3">
            <label><strong>Name</strong></label>
            <input type="text" name="name" class="form-control">
            <div class="invalid-feedback" id="error-name"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Code</strong></label>
            <input type="text" name="code" class="form-control">
            <div class="invalid-feedback" id="error-code"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Trade Name</strong></label>
            <input type="text" name="tradename" class="form-control">
            <div class="invalid-feedback" id="error-tradename"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Common Name</strong></label>
            <input type="text" name="common_name" class="form-control">
            <div class="invalid-feedback" id="error-common_name"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Size</strong></label>
            <input type="text" name="size" class="form-control">
            <div class="invalid-feedback" id="error-size"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Unit</strong></label>
            <select name="unit_id" id="unit_id" class="form-control">
                <option value="">Select Unit</option>
            </select>
            <div class="invalid-feedback" id="error-unit_id"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Purpose of Use</strong></label>
            <input type="text" name="pur_of_use" class="form-control">
            <div class="invalid-feedback" id="error-pur_of_use"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>RM Group</strong></label>
            <input type="text" name="RM_Group" class="form-control">
            <div class="invalid-feedback" id="error-RM_Group"></div>
        </div>
    </div>

    <div class="row align-items-center">
        <div class="col-md-2">
            <a href="{{ route('InputItems') }}" class="btn btn-danger w-100">Back</a>
        </div>
        <div class="col-md-2 ms-auto">
            <button type="button" class="btn btn-success w-100" onclick="OnCreateInputItem()">Save</button>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $.ajax({
            url: '/api/units',      // URL ของ Route API
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // สมมติ response เป็น array ของ role objects ที่มี id กับ name
                // เช่น [{id:1, name:"Admin"}, {id:2, name:"User"}, ...]
                var select = $('#unit_id');
                $.each(response.data, function(index, role) {
                    select.append($('<option>', {
                        value: role.id,
                        text: role.name
                    }));
                });
            },
            error: function(xhr) {
                console.error('Failed to fetch roles:', xhr);
            }
        });
    });

    function OnCreateInputItem() {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        let formData = {
            name: $('input[name="name"]').val(),
            code: $('input[name="code"]').val(),
            tradename: $('input[name="tradename"]').val(),
            common_name: $('input[name="common_name"]').val(),
            size: $('input[name="size"]').val(),
            unit_id: $('select[name="unit_id"]').val(),
            pur_of_use: $('input[name="pur_of_use"]').val(),
            RM_Group: $('input[name="RM_Group"]').val(),
        };

        $.ajax({
            url: '/api/InputItems',
            type: 'POST',
            data: formData,
            success: function (res) {
                alert("✅ บันทึกสำเร็จ");
                window.location.href = "{{ route('InputItems') }}";
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
