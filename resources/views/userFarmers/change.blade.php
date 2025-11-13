@extends('layouts.app')

@section('topic')
    แก้ไขข้อมูล {{ $type ?? '' }}
@endsection

@section('content')
    <input type="hidden" id="cropId" value="{{ $cropId ?? '' }}">
    <input type="hidden" id="type" value="{{ $type ?? '' }}">
    <input type="hidden" id="id" value="{{ $id ?? '' }}">

<form id="userFarmerForm">
    @csrf
    @method('PUT') {{-- ถ้าใช้ form submit แบบปกติ --}}

    <div class="row">
        <!-- 1. Crop -->
        <div class="col-md-4 mb-3">
            <label><strong>Crop</strong></label>
            <input type="text" name="crop_name" id="crop_name" class="form-control" readonly>
        </div>
        @if ($type != "broker")
            <!-- 2. Broker -->
            <div class="col-md-4 mb-3">
                <label><strong>หัวหน้ากลุ่ม</strong></label>
                <input type="text" name="broker_name" id="broker_name" class="form-control" readonly>
            </div>
        @endif
        @if ($type != "head")
            <!-- 3. Head -->
            <div class="col-md-4 mb-3">
                <label><strong>หัวจุด</strong></label>
                <input type="text" name="head_name" id="head_name" class="form-control" readonly>
            </div>
        @endif
        @if ($type != "manager")
            <!-- 4. Manager -->
            <div class="col-md-4 mb-3">
                <label><strong>หัวหน้า</strong></label>
                <input type="text" name="manager_name" id="manager_name" class="form-control" readonly>
            </div>
        @endif
        @if ($type != "user")
            <!-- 5. User -->
            <div class="col-md-4 mb-3">
                <label><strong>พนักงาน</strong></label>
                <input type="text" name="user_name" id="user_name" class="form-control" readonly>
            </div>
        @endif
        @if ($type != "review")
            <!-- 6. Review -->
            <div class="col-md-4 mb-3">
                <label><strong>ส่งเสริม</strong></label>
                <input type="text" name="review_name" id="review_name" class="form-control" readonly>
            </div>
        @endif
        @if ($type != "farmer")
            <!-- 7. Farmer -->
            <div class="col-md-4 mb-3">
                <label><strong>ลูกสวน</strong></label>
                <input type="text" name="farmer_name" id="farmer_name" class="form-control" readonly>
            </div>
        @endif
        {{-- @if ($type != "sowing")
            <!-- 8. Sowing -->
            <div class="col-md-4 mb-3">
                <label><strong>ลูกสวน</strong></label>
                <input type="text" name="sowing_name" id="sowing_name" class="form-control" readonly>
            </div>
        @endif --}}
    </div>

    <div class="row">
        @if ($type == "head")
            <div class="col-md-4 mb-3">
                <label><strong>หัวจุด</strong></label>
                <select name="head_id" id="head_id" class="form-control">
                    <option value="">Select หัวจุด</option>
                </select>
                <div class="invalid-feedback" id="error-head_id"></div>
            </div>
        @elseif ($type == "farmer")
            <div class="col-md-4 mb-3">
                <label><strong>ลูกสวน</strong></label>
                <select name="farmer_id" id="farmer_id" class="form-control">
                    <option value="">Select ลูกสวน</option>
                </select>
                <div class="invalid-feedback" id="error-farmer_id"></div>
            </div>
        @elseif ($type == "user")
            <div class="col-md-4 mb-3">
                <label><strong>พนักงาน</strong></label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">Select พนักงาน</option>
                </select>
                <div class="invalid-feedback" id="error-user_id"></div>
            </div>
        @elseif ($type == "manager")
            <div class="col-md-4 mb-3">
                <label><strong>หัวหน้า</strong></label>
                <select name="manager_id" id="manager_id" class="form-control">
                    <option value="">Select หัวหน้า</option>
                </select>
                <div class="invalid-feedback" id="error-manager_id"></div>
            </div>
        @elseif ($type == "review")
            <div class="col-md-4 mb-3">
                <label><strong>ส่งเสริม</strong></label>
                <select name="review_id" id="review_id" class="form-control">
                    <option value="">Select ส่งเสริม</option>
                </select>
                <div class="invalid-feedback" id="error-review_id"></div>
            </div>
        @elseif ($type == "broker")
            <div class="col-md-4 mb-3">
                <label><strong>หัวหน้ากลุ่ม</strong></label>
                <select name="broker_id" id="broker_id" class="form-control">
                    <option value="">Select หัวหน้ากลุ่ม</option>
                </select>
                <div class="invalid-feedback" id="error-broker_id"></div>
            </div>
        @elseif ($type == "sowing")
            <div class="col-md-4 mb-3">
                <label><strong>บ้านที่ปลูก</strong></label>
                <input type="text" name="sowing_name" id="sowing_name" class="form-control">
            
                <div class="invalid-feedback" id="error-sowing_name"></div>
            </div>
        @else
            <script>
                window.location.href = "{{ route('userFarmers', ['cropId' => $cropId]) }}";
            </script>
        @endif
    </div>

    <div class="row align-items-center">
        <div class="col-md-2">
            <a href="{{ route('userFarmers', ['cropId' => $cropId]) }}" class="btn btn-danger w-100">Back</a>
        </div>
        <div class="col-md-2 ms-auto">
            <button type="button" class="btn btn-primary w-100" onclick="onUpdateUserFarmer()">Update</button>
        </div>
    </div>
</form>

@endsection

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />

<script>
    let farmerItems = [];

    $(document).ready(function () {
        var cropId = $('#cropId').val();
        var type = $('#type').val();
        var id = $('#id').val();

        $.ajax({
            url: '/api/userFarmers/' + cropId + '/' + id,
            method: 'GET',
            success: function(res) {
                bindData(res.data);
                
                switch (type) {
                    case 'head':
                        var headId = res.data.head.id;
                        var brokerId = res.data.broker.id;
                        $.get(`/api/brokerHead/getListByCrop/${cropId}/${brokerId}`, function (res) {
                            $('#head_id').empty().append('<option value="">Select หัวจุด</option>');
                            res.data.forEach(item => {
                                $('#head_id').append(new Option(`${item.head.fname} ${item.head.lname}`, item.head.id));
                            });
                            $('#head_id').val(headId);
                        });                
                    break;
                    case 'farmer':
                        var farmerId = res.data.farmer.id;
                        $.get('/api/farmers', function (res) {
                            res.data.forEach(user => {
                                const option = new Option(`${user.fname} ${user.lname}`, user.id);
                                $('#farmer_id').append(option.cloneNode(true));
                            });
                            $('#farmer_id').val(farmerId);
                        });                
                    break;
                    case 'user':
                        var userId = res.data.user.id;
                        $.get('/api/users', function (res) {
                            res.data.forEach(user => {
                                const option = new Option(`${user.fname} ${user.lname}`, user.id);
                                $('#user_id').append(option.cloneNode(true));
                            });
                            $('#user_id').val(userId);
                        });
                    break;
                    case 'manager':
                        var managerId = res.data.manager.id;
                        $.get('/api/users', function (res) {
                            res.data.forEach(user => {
                                const option = new Option(`${user.fname} ${user.lname}`, user.id);
                                $('#manager_id').append(option.cloneNode(true));
                            });
                            $('#manager_id').val(managerId);
                        });
                        
                    break;
                    case 'review':
                        var reviewId = res.data.review.id;
                        $.get('/api/users', function (res) {
                            res.data.forEach(user => {
                                const option = new Option(`${user.fname} ${user.lname}`, user.id);
                                $('#review_id').append(option.cloneNode(true));
                            });
                            $('#review_id').val(reviewId);
                        });
                        
                    break;
                    case 'broker':
                        var brokerId = res.data.broker.id;
                        $.get('/api/brokers', function (res) {
                            res.data.forEach(broker => {
                                $('#broker_id').append(new Option(`${broker.code} - ${broker.fname} ${broker.lname}`, broker.id));
                            });
                            $('#broker_id').val(brokerId);
                        });
                    break;
                
                    default:
                        break;
                }
            },
            error: function(xhr) {
                alert('Error fetching user farmer data.');
            }
        });
    });

    function bindData(params) {
        $('#crop_name').val(params.crop.name);
        var bName = (params?.broker?.init ?? '-') + ' ' + (params?.broker?.fname ?? '-') + ' '  + (params?.broker?.lname ?? '-')
        $('#broker_name').val(bName);
        var hName = (params?.head?.init ?? '-') + ' ' + (params?.head?.fname ?? '-') + ' '  + (params?.head?.lname ?? '-')
        $('#head_name').val(hName);
        var mName = (params?.manager?.init ?? '-') + ' ' + (params?.manager?.fname ?? '-') + ' '  + (params?.manager?.lname ?? '-')
        $('#manager_name').val(mName);
        var uName = (params?.user?.init ?? '-') + ' ' + (params?.user?.fname ?? '-') + ' '  + (params?.user?.lname ?? '-')
        $('#user_name').val(uName);
        var rName = (params?.review?.init ?? '-') + ' ' + (params?.review?.fname ?? '-') + ' '  + (params?.review?.lname ?? '-')
        $('#review_name').val(rName);
        var fName = (params?.farmer?.init ?? '-') + ' ' + (params?.farmer?.fname ?? '-') + ' '  + (params?.farmer?.lname ?? '-')
        $('#farmer_name').val(fName);  
        $('#sowing_name').val(params?.sowing_city);    
        
    }

    function onUpdateUserFarmer() {
        const formData = {
            id: $('#id').val(),
            type: $('#type').val(),
            crop_id: $('#cropId').val(),
            crop_name: $('#crop_name').val(),
            broker_id: $('#broker_id').val(),
            broker_name: $('#broker_name').val(),
            head_name: $('#head_name').val(),
            manager_name: $('#manager_name').val(),
            user_name: $('#user_name').val(),
            review_name: $('#review_name').val(),
            farmer_name: $('#farmer_name').val(),
            sowing_name: $('#sowing_name').val(),
            review_id: $('#review_id').val(),
            manager_id: $('#manager_id').val(),
            user_id: $('#user_id').val(),
            farmer_id: $('#farmer_id').val(),
            head_id: $('#head_id').val(),
        };

        console.log(formData);
        $.ajax({
            url: '/api/userFarmers/updateByType/' + $('#cropId').val() + '/' + $('#id').val(),
            method: 'PUT', 
            data: formData,
            success: function (res) {
                // console.log(res);
                alert('✅ อัปเดตข้อมูลสำเร็จ');
                window.location.href = "{{ route('userFarmers', ['cropId' => $cropId]) }}";
            },
            error: function (res) {
                // console.error(res);
                if (res.status === 422) {
                    const errors = res.responseJSON.errors;
                    for (let field in errors) {
                        $('[name="' + field + '"]').addClass('is-invalid');
                        $('#error-' + field).html(errors[field][0]);
                    }
                } else {
                    alert('❌ เกิดข้อผิดพลาดในการอัปเดตข้อมูล');
                }
            }
        });
    }

</script>
