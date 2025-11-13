@extends('Layouts.app')

@section('title', 'Template Plan Schedules')

@section('content')
<div class="row">
    <div class="col-md-12">
        <a href="{{ route('tmpSchedules.new') }}" class="btn btn-success my-3">Create New Template</a>
        <hr>
    </div>
</div>

<div class="row my-3">
    <div class="col-md-12">
        <form action="#" method="get" class="row g-2 align-items-center">
            <div class="col-12 col-sm-10">
                <input type="text" class="form-control" name="search-custom" id="search-custom" onkeyup="searchByText(this)" placeholder="Search..." value="">
            </div>
            <div class="col-12 col-sm-2">
                <button type="button" class="btn btn-danger w-100" onclick="clearInput();">Clear</button>
            </div>
        </form>
    </div>
</div>

<table class="table" id="head-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Template Name</th>
            <th>Detail</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    let table;

    $.get('/api/tmpSchedules', function(response) {
        console.log(response);
        getTable(response.data);
    });

    function clearInput() {
        $('#search-custom').val("");
        if (table) {
            table.search("").draw();
        }
    }

    function getTable(data) {
        table = $('#head-table').DataTable({
            data: data,
            dom: 'lrtip',
            destroy: true,
            info: false,
            ordering: false,
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'modified', title: 'วันเวลา' },
                { data: 'name', title: 'ชื่อ Template' },
                { data: 'details', title: 'รายละเอียด' },
                {
                    data: 'id',
                    title: 'Action',
                    render: function (data, type, row, meta) {
                        return `
                            <div class="btn-group btn-group-toggle">
                                <a href="/TmpSchedules/${row.id}/edit" class="btn btn-warning text-white m-2">แก้ไข</a>
                                <a href="/TmpSchedules/${row.id}/clone" class="btn btn-primary text-white m-2">คัดลอกไปใช้งาน</a>
                                <a href="/TmpSchedules/${row.id}/review" class="btn btn-info text-white m-2">รายละเอียด</a>
                                <button type="button" class="btn btn-danger m-2" onclick="onDelete(${row.id});">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });
    }

    function searchByText(input) {
        if (table) {
            table.search(input.value).draw();
        }
    }

    function onDelete(id) {
        if (!confirm('คุณต้องการลบข้อมูล Head นี้หรือไม่?')) return;

        $.ajax({
            url: `/api/tmpSchedules/${id}`,
            type: 'DELETE',
            success: function (res) {
                $.get('/api/tmpSchedules', function(response) {
                    getTable(response.data);
                });
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการลบข้อมูล');
            }
        });
    }
</script>
