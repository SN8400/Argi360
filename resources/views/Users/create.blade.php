@extends('layouts.app')

@section('topic')
    ADD User
@endsection

@section('content')

<form id="userForm">
    @csrf

    <div class="row">
        <div class="col-md-4 mb-3">
            <label><strong>Username</strong></label>
            <input type="text" name="username" class="form-control">
            <div class="invalid-feedback" id="error-username"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Email</strong></label>
            <input type="email" name="email" class="form-control">
            <div class="invalid-feedback" id="error-email"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Password</strong></label>
            <input type="password" name="password" class="form-control">
            <div class="invalid-feedback" id="error-password"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Retype Password</strong></label>
            <input type="password" name="password_confirmation" class="form-control">
            <div class="invalid-feedback" id="error-password_confirmation"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Group</strong></label>
            <select name="group_id" id="group_id" class="form-control">
                <option value="">Select Group</option>
            </select>
            <div class="invalid-feedback" id="error-group_id"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Citizen Id</strong></label>
            <input type="text" name="citizenid" class="form-control">
            <div class="invalid-feedback" id="error-citizenid"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Init</strong></label>
            <input type="text" name="init" class="form-control">
            <div class="invalid-feedback" id="error-init"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Firstname</strong></label>
            <input type="text" name="fname" class="form-control">
            <div class="invalid-feedback" id="error-fname"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Lastname</strong></label>
            <input type="text" name="lname" class="form-control">
            <div class="invalid-feedback" id="error-lname"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Can Edit</strong></label>
            <select name="canEdit" class="form-control">
                <option value="">Select Can Edit</option>
                <option value="Y">Yes</option>
                <option value="N">No</option>
            </select>
            <div class="invalid-feedback" id="error-canEdit"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Review Team</strong></label>
            <input type="text" name="reviewTeam" class="form-control">
            <div class="invalid-feedback" id="error-reviewTeam"></div>
        </div>
    </div>

    <div class="row align-items-center">
        <div class="col-md-2">
            <a href="{{ route('Users.index') }}" class="btn btn-danger w-100">Back</a>
        </div>
        <div class="col-md-2 ms-auto">
            <button type="button" class="btn btn-success w-100" onclick="OnCreateUser()">Save</button>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $.ajax({
        url: '/api/roles',      // URL ของ Route API
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            // สมมติ response เป็น array ของ role objects ที่มี id กับ name
            // เช่น [{id:1, name:"Admin"}, {id:2, name:"User"}, ...]
            var select = $('#group_id');
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




    function OnCreateUser() {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        let formData = {
            username: $('input[name="username"]').val(),
            email: $('input[name="email"]').val(),
            password: $('input[name="password"]').val(),
            password_confirmation: $('input[name="password_confirmation"]').val(),
            group_id: $('select[name="group_id"]').val(),
            citizenid: $('input[name="citizenid"]').val(),
            init: $('input[name="init"]').val(),
            fname: $('input[name="fname"]').val(),
            lname: $('input[name="lname"]').val(),
            canEdit: $('select[name="canEdit"]').val(),
            reviewTeam: $('input[name="reviewTeam"]').val(),
        };
        console.log(formData);
        $.ajax({
            url: '/api/users', // หรือเปลี่ยนตาม route ที่กำหนดไว้
            type: 'POST',
            data: formData,
            success: function (res) {
                alert("✅ บันทึกสำเร็จ");
                window.location.href = "{{ route('Users.index') }}";
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
