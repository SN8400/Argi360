@extends('Layouts.app')

@section('title', 'Brokers Management')

@section('content')
<div class="row">
    <div class="col-md-12">
        <a href="{{ route('Brokers.create') }}" class="btn btn-success my-3">Create New Broker</a>
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

<table class="table" id="broker-table">
    <thead>
        <tr>
            <th>#</th>
            <th>code</th>
            <th>init</th>
            <th>fname</th>
            <th>lname</th>
            <th>citizenid</th>
            <th>address1</th>
            <th>address2</th>
            <th>address3</th>
            <th>sub_cities</th>
            <th>city_id</th>
            <th>province_id</th>
            <th>loc</th>
            <th>broker_color</th>
            <th>createdBy</th>
            <th>modifiedBy</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    let table;

    $.get('/api/brokers', function(response) {
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
        table = $('#broker-table').DataTable({
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
                { data: 'code', title: 'รหัส' },
                { data: 'init', title: 'คำนำหน้า' },
                { data: 'fname', title: 'ชื่อ' },
                { data: 'lname', title: 'นามสกุล' },
                { data: 'citizenid', title: 'เลขบัตรประชาชน' },
                { data: 'address1', title: 'address1' },
                { data: 'address2', title: 'address2' },
                { data: 'address3', title: 'address3' },
                { data: 'sub_cities', title: 'sub_cities' },
                { data: 'city_id', title: 'city_id' },
                { data: 'province_id', title: 'province_id' },
                { data: 'loc', title: 'province_id' },
                { data: 'broker_color', title: 'Color' ,
                    render: function (data, type, row, meta) {
                        return '<td><div class="rounded-2" style="background-color: ' + data + '">&nbsp;</div></td>';
                    }
                },
                { data: 'createdBy', title: 'createdBy' },
                { data: 'modifiedBy', title: 'modifiedBy' },
                {
                    data: 'id',
                    title: 'Action',
                    render: function (data, type, row, meta) {
                        return `
                            <div class="btn-group btn-group-toggle">
                                <a href="/Brokers/${row.id}/edit" class="btn btn-warning text-white">Edit</a>
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
        if (!confirm('คุณต้องการลบข้อมูล Broker นี้หรือไม่?')) return;

        $.ajax({
            url: `/api/brokers/${id}`,
            type: 'DELETE',
            success: function (res) {
                $.get('/api/brokers', function(response) {
                    getTable(response.data);
                });
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการลบข้อมูล');
            }
        });
    }
</script>
