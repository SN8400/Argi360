@extends('Layouts.app')
@section('title', 'Input Items Page')
@section('content')
    <input type="hidden" id="cropId" value="{{ $id ?? '' }}">
    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('InputItems.create') }}" class="btn btn-success my-3">Create New Input Item</a>
            <a href="{{ route('plannings.upload', ['id' => $id ?? '']) }}" class="btn btn-info my-3">Upload แผน</a>
            {{-- <a href="javascript:void(0)" onclick="onExport('G')"; class="btn btn-warning my-3">Download แผนปลูก</a> --}}
            <a href="{{ route('downloadGrowPlanReport', ['crop_id' => $id ?? '']) }}" class="btn btn-warning my-3">Download แผนปลูก</a>
            <a href="{{ route('downloadHarvestPlanReport', ['crop_id' => $id ?? '']) }}" class="btn btn-primary my-3">Download แผนเก็บ</a>
            {{-- <a href="{{ route('downloadYieldReport', ['crop_id' => $id ?? '', 'report_type' => '0']) }}" class="btn btn-primary my-3">Download แผนเก็บ</a> --}}
            {{-- <a href="javascript:void(0)" onclick="onExport('H')"; class="btn btn-primary my-3">Download แผนเก็บ</a> --}}
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
                <th>Crop Name</th>
                <th>Item Name</th>
                <th>Harvest Type</th>
                <th>Target</th>
                <th>Plan Per Fram</th>
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
        $.ajax({
            url: '/api/plannings',
            method: 'GET',
            data: { crop_id },
            success: function(res) {
                console.log(res);
                getTable(res.data)
            },
            error: function(xhr) {
                alert('Error saving data.');
            }
        });
    }

    function onExport(params) {
        console.log(params);
        let crop_id = $('#cropId').val();
        let urls = "";
        if(params == 'H'){
            urls = "/api/uploads/exportHarvest/" + crop_id;
        }
        else if(params == 'G'){
            urls = "/api/uploads/exportGrow/" + crop_id;
        }        
        $.ajax({
            url: urls,
            method: 'POST',
            success: function(res) {
                console.log(res);
            },
            error: function(xhr) {
                
                console.error(xhr);
                // alert('Error saving data.');
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
                { data: 'crop', title: 'Crop Name',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    }
                },
                { data: 'inputitem', title: 'พันธุ์พืช',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    }
                },
                { data: 'harvesttype', title: 'วิธีการเก็บ',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    }
                },
                { data: 'plan_value', title: 'แผนที่ต้องการ (ตัน)',
                    render: function(data, type, row, meta) {
                        return `<span class="plan_value_label">${(data / 1000).toLocaleString(undefined, { maximumFractionDigits: 2 })}</span>
                                <input class="form-control plan_value_input d-none" value="${data}" type="number" step="0.01">`;
                    }
                },
                { data: 'plan_kg_area' , title: 'แผนเมล็ดพันธุ์ต่อไร่',
                    render: function(data, type, row, meta) {
                        var val = data ? data : '';
                        return `<span class="plan_kg_area_label">${val}</span>
                                <input class="form-control plan_kg_area_input d-none" value="${val}" type="number" step="0.01">`;
                    }
                },
                {
                    data: 'id',
                    render: (data, type, row, meta) => `
                        <button class="btn btn-warning btn-sm btn-edit" onclick="onEdit(this)">Edit</button>
                        <button class="btn btn-success btn-sm d-none btn-update" onclick="onUpdate(this)">Update</button>
                        <button class="btn btn-secondary btn-sm d-none btn-cancel" onclick="onCancel(this)">Cancel</button>
                        <a href="/plannings/${row.crop_id}/${row.id}" class="btn btn-info btn-sm btn-manage text-white m-2">Management</a>
                        <button class="btn btn-danger btn-sm" onclick="onDelete(${data})">Delete</button>
                    `
                }
            ]
        });
    }

    function onEdit(ele) {
        const row = $(ele).closest('tr');
        row.find('.plan_value_label, .plan_kg_area_label').addClass('d-none');
        row.find('.plan_value_input, .plan_kg_area_input').removeClass('d-none');

        row.find('.btn-edit').addClass('d-none');
        row.find('.btn-update, .btn-cancel').removeClass('d-none');
    }

    function onCancel(ele) {
        const row = $(ele).closest('tr');
        row.find('.plan_value_input, .plan_kg_area_input').addClass('d-none');
        row.find('.plan_value_label, .plan_kg_area_label').removeClass('d-none');

        row.find('.btn-update, .btn-cancel').addClass('d-none');
        row.find('.btn-edit').removeClass('d-none');
    }

    function onUpdate(ele) {
        const row = $(ele).closest('tr');
        const data = table.row(row).data();

        const plan_value = row.find('.plan_value_input').val();
        const plan_kg_area = row.find('.plan_kg_area_input').val();
        var dataSet = {
            id: data.id,
            plan_value: plan_value,
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
                row.find('.plan_value_label').text((plan_value / 1000).toLocaleString(undefined, { maximumFractionDigits: 2 }));
                row.find('.plan_kg_area_label').text(plan_kg_area);
                // กลับไปเป็นโหมดอ่าน
                row.find('.plan_value_input, .plan_kg_area_input').addClass('d-none');
                row.find('.plan_value_label, .plan_kg_area_label').removeClass('d-none');
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