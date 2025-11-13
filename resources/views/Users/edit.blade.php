@extends('layouts.app')

@section('topic')
    Edit User
@endsection

@section('content')

<form id="userForm">
    @csrf
    <input type="hidden" id="userId" value="{{ $id ?? '' }}">

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
            <button type="button" class="btn btn-success w-100" onclick="OnUpdateUser()">Update</button>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // โหลด group (roles)
    $.ajax({
        url: '/api/roles',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
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

    // โหลดข้อมูลผู้ใช้
    let userId = $('#userId').val();
    if (userId) {
        $.ajax({
            url: '/api/users/' + userId,
            type: 'GET',
            success: function(res) {
                if (res.status === 'success') {
                    let user = res.data;
                    $('input[name="username"]').val(user.username);
                    $('input[name="email"]').val(user.email);
                    $('select[name="group_id"]').val(user.group_id);
                    $('input[name="citizenid"]').val(user.citizenid);
                    $('input[name="init"]').val(user.init);
                    $('input[name="fname"]').val(user.fname);
                    $('input[name="lname"]').val(user.lname);
                    $('select[name="canEdit"]').val(user.canEdit);
                    $('input[name="reviewTeam"]').val(user.reviewTeam);
                } else {
                    alert("ไม่พบข้อมูลผู้ใช้");
                }
            },
            error: function() {
                alert("เกิดข้อผิดพลาดในการโหลดข้อมูลผู้ใช้");
            }
        });
    }
});

// ฟังก์ชันอัปเดตข้อมูลผู้ใช้
function OnUpdateUser() {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').html('');

    let userId = $('#userId').val();
    let formData = {
        username: $('input[name="username"]').val(),
        email: $('input[name="email"]').val(),
        group_id: $('select[name="group_id"]').val(),
        citizenid: $('input[name="citizenid"]').val(),
        init: $('input[name="init"]').val(),
        fname: $('input[name="fname"]').val(),
        lname: $('input[name="lname"]').val(),
        canEdit: $('select[name="canEdit"]').val(),
        reviewTeam: $('input[name="reviewTeam"]').val(),
        _token: $('input[name="_token"]').val()
    };

    $.ajax({
        url: '/api/users/' + userId,
        type: 'PUT',
        data: formData,
        success: function(res) {
            alert("✅ อัปเดตสำเร็จ");
            window.location.href = "{{ route('Users.index') }}";
        },
        error: function(res) {
            if (res.status === 422) {
                let errors = res.responseJSON.errors;
                for (let field in errors) {
                    let el = $('[name="' + field + '"]');
                    el.addClass('is-invalid');
                    $('#error-' + field).html(errors[field][0]);
                }
            } else if (res.status === 404) {
                alert("❌ ไม่พบข้อมูลผู้ใช้");
            } else {
                alert("❌ เกิดข้อผิดพลาดในการอัปเดต");
            }
        }
    });
}
</script>
@endpush
