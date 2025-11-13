@extends('Layouts.app')

@section('title', 'Heads Management')

@section('content')
<div class="row">
    <div class="col-md-12">
        <a href="{{ route('Heads.create') }}" class="btn btn-success my-3">Create New Head</a>
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
            <th>Code</th>
            <th>คำนำหน้า</th>
            <th>ชื่อ</th>
            <th>นามสกุล</th>
            <th>เลขบัตรประชาชน</th>
            <th>ที่อยู่ 1</th>
            <th>ที่อยู่ 2</th>
            <th>ที่อยู่ 3</th>
            <th>แขวง/ตำบล</th>
            <th>เขต/อำเภอ</th>
            <th>จังหวัด</th>
            <th>ผู้สร้าง</th>
            <th>ผู้แก้ไขล่าสุด</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    let table;

    $.get('/api/heads', function(response) {
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
                { data: 'code', title: 'Code' },
                { data: 'init', title: 'คำนำหน้า' },
                { data: 'fname', title: 'ชื่อ' },
                { data: 'lname', title: 'นามสกุล' },
                { data: 'citizenid', title: 'เลขบัตรประชาชน' },
                { data: 'address1', title: 'ที่อยู่ 1' },
                { data: 'address2', title: 'ที่อยู่ 2' },
                { data: 'address3', title: 'ที่อยู่ 3' },
                { data: 'sub_cities', title: 'แขวง/ตำบล' },
                { data: 'city_id', title: 'เขต/อำเภอ' },
                { data: 'province_id', title: 'จังหวัด' },
                { data: 'createdBy', title: 'ผู้สร้าง' },
                { data: 'modifiedBy', title: 'ผู้แก้ไขล่าสุด' },
                {
                    data: 'id',
                    title: 'Action',
                    render: function (data, type, row, meta) {
                        return `
                            <div class="btn-group btn-group-toggle">
                                <a href="/Heads/${row.id}/edit" class="btn btn-warning text-white">Edit</a>
                                <button type="button" class="btn btn-danger" onclick="onDelete(${row.id});">
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
            url: `/api/heads/${id}`,
            type: 'DELETE',
            success: function (res) {
                $.get('/api/heads', function(response) {
                    getTable(response.data);
                });
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการลบข้อมูล');
            }
        });
    }
</script>
