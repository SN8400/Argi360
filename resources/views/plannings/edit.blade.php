@extends('Layouts.app')
@section('title', 'Input Items Page')
@section('content')
    <input type="hidden" id="cropId" value="{{ $cropId ?? '' }}">
    <input type="hidden" id="planId" value="{{ $planId ?? '' }}">
    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('InputItems.create') }}" class="btn btn-success my-3">Create New Input Item</a>
            <hr>
        </div>
    </div>
    <div class="row my-3">
        <div class="col-md-12">
            <form action="#" method="get" class="row g-2 align-items-center">
            <div class="col-12 col-sm-10">
                <input type="text" class="form-control" name="search-custom" id="search-custom" onkeyUp="searchByText(this)" placeholder="Search...">
            </div>
            <div class="col-12 col-sm-2">
                <button type="button" class="btn btn-danger w-100" onclick="clearInput();">Clear</button>
            </div>
            </form>
        </div>
    </div>
    <table class="table" id="plan-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Broker Name</th>
                <th>Area Name</th>
                <th>Harvest Age (Day)</th>
                <th>Yeilds (ตัน)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    let table;
    window.addEventListener('load', function () {
        getData();
    });

    function getData(){
        let crop_id = $('#cropId').val();
        let plan_id = $('#planId').val();
        $.ajax({
            url: '/api/plannings/' + crop_id +'/' + plan_id,
            method: 'GET',
            data: { crop_id, plan_id },
            success: function(res) {
                console.log(res);
                getTable(res.data)
            },
            error: function(xhr) {
                alert('Error saving data.');
            }
        });
    }

    function clearInput() {
        $('#search-custom').val("");
        if (table) table.search("").draw();
    }

    function getTable(data) {
        table = $('#plan-table').DataTable({
            data: data,
            dom: 'lrtip',
            destroy: true,
            info: false,
            ordering: false,
            pageLength: 100,
            columns: [
                { data: null, render: (data, type, row, meta) => meta.row + 1 },
                { data: 'broker', title: 'Broker Name',
                    render: function(data, type, row, meta) {
                        return data && data.fname ? data.fname + " " + data.lname + " [" + data.code + "] "  : '-';
                    }
                },
                { data: 'area', title: 'Area Name',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    }
                },
                { data: 'harvest_age', title: 'Harvest Age (Day)',
                    render: function(data, type, row, meta) {
                        return `<span class="harvest_age_label">${ data }</span>
                                <input class="form-control harvest_age_input d-none" value="${data}" type="number" step="0.01">`;
                    }
                },
                { data: 'filtered_yeilds' , title: 'แผนเมล็ดพันธุ์ต่อไร่',
                    render: function(data, type, row, meta) {
                        if (!data || data.length === 0) return 'ALL - 0 [update]';

                        let html = '';
                        const cropId = row.planning?.crop_id;
                        const inputItemId = row.planning?.item_input_id;
                        const harvestTypeId = row.planning?.harvest_type_id;
                        const brokerId = row.broker_id;
                        const planningId = row.planning_id;

                        if (data.length > 1) {
                            data.forEach(yeild => {
                                html += `<a href="/yeilds/${row.planning_id}/${row.planning.crop_id}/${row.planning.item_input_id}/${row.area_id}/${row.broker_id}/${row.planning.harvest_type_id}/${yeild.id}">ALL - ${parseFloat(yeild.rate) / 1000}</a>`;
                                html += `<a href="javascript:void(0);" onclick="recalEstimateYeild(${cropId}, ${brokerId}, ${inputItemId}, ${yeild.rate}, ${planningId}, ${harvestTypeId})">[update]</a>`;
                                html += `<a href="javascript:void(0);" onclick="deleteYeild(${yeild.id})">X</a><br/>`;
                            });
                        } else {
                            const yeild = data[0];
                            html += `<a href="/yeilds/${row.planning_id}/${row.planning.crop_id}/${row.planning.item_input_id}/${row.area_id}/${row.broker_id}/${row.planning.harvest_type_id}/${yeild.id}">ALL - ${parseFloat(yeild.rate) / 1000}</a>`;
                            html += `<a href="javascript:void(0);" onclick="recalEstimateYeild(${cropId}, ${brokerId}, ${inputItemId}, ${yeild.rate}, ${planningId}, ${harvestTypeId})">[update]</a>`;
                        }

                        return html;
                    }
                },
                {
                    data: 'id',
                    render: (data, type, row, meta) => `
                        <button class="btn btn-warning btn-sm btn-edit" onclick="onEdit(this)">Edit</button>
                        <button class="btn btn-success btn-sm d-none btn-update" onclick="onUpdate(this)">Update</button>
                        <button class="btn btn-secondary btn-sm d-none btn-cancel" onclick="onCancel(this)">Cancel</button>
                        <a href="/yeilds/${row.planning_id}/${row.planning.crop_id}/${row.planning.item_input_id}/${row.area_id}/${row.broker_id}/${row.planning.harvest_type_id}" class="btn btn-info btn-sm btn-manage text-white m-2">เพิ่ม Yeild</a>
                        <button class="btn btn-danger btn-sm" onclick="onDelete(${data})">Delete</button>
                    `
                }
            ]
        });
    }

    function recalEstimateYeild(cropId, brokerId, inputItemId, yeildRate, planningId, harvestTypeId){
        $.ajax({
            url: '/api/plannings/',
            type: 'POST',
            data: {
                crop_id: cropId,
                broker_id: brokerId,
                input_item_id: inputItemId,
                yeild_rate: yeildRate,
                planning_id: planningId,
                harvest_type_id: harvestTypeId
            },
            success: function(response) {
                console.log(response);
            },
            error: function(xhr) {
                alert('เกิดข้อผิดพลาดในการอัปเดต');
                console.error(xhr.responseText);
            }
        });
    }

    function onEdit(ele) {
        const row = $(ele).closest('tr');
        row.find('.harvest_age_label, .plan_kg_area_label').addClass('d-none');
        row.find('.harvest_age_input, .plan_kg_area_input').removeClass('d-none');

        row.find('.btn-edit').addClass('d-none');
        row.find('.btn-update, .btn-cancel').removeClass('d-none');
    }

    function onCancel(ele) {
        const row = $(ele).closest('tr');
        row.find('.harvest_age_input, .plan_kg_area_input').addClass('d-none');
        row.find('.harvest_age_label, .plan_kg_area_label').removeClass('d-none');

        row.find('.btn-update, .btn-cancel').addClass('d-none');
        row.find('.btn-edit').removeClass('d-none');
    }

    function onUpdate(ele) {
        const row = $(ele).closest('tr');
        const data = table.row(row).data();

        const harvest_age = row.find('.harvest_age_input').val();
        const plan_kg_area = row.find('.plan_kg_area_input').val();
        var dataSet = {
            id: data.id,
            harvest_age: harvest_age,
            plan_kg_area: plan_kg_area
        }
        console.log(dataSet);
        $.ajax({
            url: '/api/plannings/' + data.id, // เปลี่ยนตาม API ของคุณ
            type: 'PUT',
            data: dataSet,
            success: function (res) {
                console.log(res);
                // อัปเดตค่าบน row
                row.find('.harvest_age_label').text((harvest_age / 1000).toLocaleString(undefined, { maximumFractionDigits: 2 }));
                row.find('.plan_kg_area_label').text(plan_kg_area);
                // กลับไปเป็นโหมดอ่าน
                row.find('.harvest_age_input, .plan_kg_area_input').addClass('d-none');
                row.find('.harvest_age_label, .plan_kg_area_label').removeClass('d-none');
                row.find('.btn-update, .btn-cancel').addClass('d-none');
                row.find('.btn-edit').removeClass('d-none');
            },
            error: function (res) {
                console.error(res);
                alert('Update failed');
            }
        });
    }

    function searchByText(params) {
        if (table) table.search(params.value).draw();
    }

    function onDelete(id) {
        if (!confirm('คุณต้องการลบข้อมูลใช่หรือไม่?')) return;

        $.ajax({
            url: `/api/InputItems/${id}`,
            type: 'DELETE',
            success: function () {
                $.get('/api/InputItems', function(response) {
                    getTable(response.data);
                });
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการลบ');
            }
        });
    }
</script>