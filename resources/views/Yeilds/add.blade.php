@extends('layouts.app')

@section('topic')
    เพิ่ม Yeild
@endsection

@section('content')
<form id="harvestEditForm">
    @csrf
    <input type="hidden" id="yieldId" value="{{ $yieldId ?? '' }}">
    <input type="hidden" id="planId" value="{{ $planId ?? '' }}">
    <input type="hidden" id="cropId" value="{{ $cropId ?? '' }}">
    <input type="hidden" id="inputItemId" value="{{ $inputItemId ?? '' }}">
    <input type="hidden" id="areaId" value="{{ $areaId ?? '' }}">
    <input type="hidden" id="brokerId" value="{{ $brokerId ?? '' }}">
    <input type="hidden" id="harvestTypeId" value="{{ $harvestTypeId ?? '' }}">
    <div class="row">
        <div class="col-md-12">
            <label>Crop : <span id="crop_name"></span> | <span id="start_time"></span> | <span id="end_time"></span></labebl>
            <hr>
        </div>
        <div class="col-md-12">
            <label>พันธุ์ : <span id="item_name"></span> | เขต : <span id="address"></span> | หัวหน้ากลุ่ม : <span id="broker"></span></labebl>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label><strong>Start Date</strong></label>
            <input type="date" name="start_date" class="form-control" id="start_date">
            <div class="invalid-feedback" id="error-start_date"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>End Date</strong></label>
            <input type="date" name="end_date" class="form-control" id="end_date">
            <div class="invalid-feedback" id="error-end_date"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Yield (kg)</strong></label>
            <input type="text" name="yield" class="form-control" id="yield">
            <div class="invalid-feedback" id="error-yield"></div>
        </div>

        <div class="col-md-4 mb-3">
            <label><strong>Seed per Area (kg)</strong></label>
            <input type="text" name="kg_per_area" class="form-control" id="kg_per_area">
            <div class="invalid-feedback" id="error-kg_per_area"></div>
        </div>
    </div>

    <div class="row align-items-center">
        <div class="col-md-2">
            <a href="javascript:history.back()" class="btn btn-danger w-100">Back</a>
        </div>
        <div class="col-md-2 ms-auto">
            <button type="button" class="btn btn-success w-100" onclick="submitHarvestEdit()">Save</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>


    window.addEventListener('load', function () {
        const yieldId = $('#yieldId').val();
        const urlParams = {
            plan_id: $('#planId').val(),
            crop_id: $('#cropId').val(),
            input_item_id: $('#inputItemId').val(),
            area_id: $('#areaId').val(),
            broker_id: $('#brokerId').val(),
            harvest_type_id: $('#harvestTypeId').val()
        };

        if (yieldId) {
            $.ajax({
                url: '/api/yields/' + yieldId,
                method: 'GET',
                data: urlParams,
                success: function (res) {
                    console.log(res);
                    // Fill info
                    $('#crop_name').text(res.plannings.crop.name);
                    $('#start_time').text(res.plannings.crop.startdate);
                    $('#end_time').text(res.plannings.crop.enddate);
                    $('#item_name').text(res.plannings.inputitem.name);
                    $('#address').text(res.planDetails.area.name);
                    $('#broker').text(res.planDetails.broker.fname + " " + res.planDetails.broker.lname + " [" + res.planDetails.broker.code + "]");

                    // Fill input fields
                    $('#start_date').val(res.yield.start_date);
                    $('#end_date').val(res.yield.end_date);
                    $('#yield').val(res.yield.rate);
                    $('#kg_per_area').val(res.yield.kg_per_area);
                },
                error: function () {
                    alert("❌ ไม่สามารถโหลดข้อมูล Yield ได้");
                }
            });
        } else {

            getData(urlParams);
        }
        
    });

    function getData(urlParams) {
        $.ajax({
            url: '/api/yields/getbyplanning/',
            method: 'GET',
            data: urlParams,
            success: function(res) {
                console.log(res);
                $('#crop_name').text(res.plannings.crop.name);
                $('#start_time').text(res.plannings.crop.startdate);
                $('#end_time').text(res.plannings.crop.enddate);
                $('#item_name').text(res.plannings.inputitem.name);
                $('#address').text(res.planDetails.area.name);
                $('#broker').text(res.planDetails.broker.fname + " " + res.planDetails.broker.lname + " [" + res.planDetails.broker.code + "]");
            },
            error: function(xhr) {
                alert('Error saving data.');
            }
        });
    }

    function submitHarvestEdit() {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').html('');

        let formData = {
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            rate: $('#yield').val(),
            kg_per_area: $('#kg_per_area').val(),
            plan_id: $('#planId').val(),
            crop_id: $('#cropId').val(),
            input_item_id: $('#inputItemId').val(),
            area_id: $('#areaId').val(),
            broker_id: $('#brokerId').val(),
            harvest_type_id: $('#harvestTypeId').val()
        };

        const yieldId = $('#yieldId').val();
        const method = yieldId ? 'PUT' : 'POST';
        const url = yieldId ? '/api/yields/' + yieldId : '/api/yields/';

        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function (res) {
                console.log(res);
                // alert("✅ บันทึกสำเร็จ");
                // window.history.back();
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
