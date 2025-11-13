@extends('Layouts.app')
@section('title', 'Input Items Page')
@section('content')
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
    <table class="table" id="inputitem-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Code</th>
                <th>Tradename</th>
                <th>Common Name</th>
                <th>Size</th>
                <th>Unit ID</th>
                <th>Purpose of Use</th>
                <th>RM Group</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    let table;
    $.get('/api/InputItems', function(response) {
        console.log(response);
        getTable(response.data);
    });

    function clearInput() {
        $('#search-custom').val("");
        if (table) table.search("").draw();
    }

    function getTable(data) {
        table = $('#inputitem-table').DataTable({
            data: data,
            dom: 'lrtip',
            destroy: true,
            info: false,
            ordering: false,
            columns: [
                { data: null, render: (data, type, row, meta) => meta.row + 1 },
                { data: 'name' },
                { data: 'code' },
                { data: 'tradename' },
                { data: 'common_name' },
                { data: 'size' },
                { data: 'unit', title: 'unitName',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    }
                },
                { data: 'pur_of_use' },
                { data: 'RM_Group' },
                {
                    data: 'id',
                    render: (data, type, row, meta) => `
                        <a href="/InputItems/${data}/edit" class="btn btn-warning btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" onclick="onDelete(${data})">Delete</button>
                    `
                }
            ]
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