@extends('Layouts.app')
@section('title', 'Chemicals Page')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('Chemicals.create') }}" class="btn btn-success my-3">Create New Chemical</a>
            <hr>
        </div>
    </div>
    <div class="row my-3">
        <div class="col-md-12">
            <form action="#" method="get" class="row g-2 align-items-center">
                <div class="col-12 col-sm-10">
                    <input type="text" class="form-control" name="search-custom" id="search-custom" onkeyup="searchByText(this)" placeholder="Search...">
                </div>
                <div class="col-12 col-sm-2">
                    <button type="button" class="btn btn-danger w-100" onclick="clearInput();">Clear</button>
                </div>
            </form>
        </div>
    </div>
    <table class="table" id="chemical-table">
        <thead>
            <tr>
                <th>#</th>
                <th>code</th>
                <th>Name</th>
                <th>details</th>
                <th>formula_code</th>
                <th>standard_code_id</th>
                <th>unit_id</th>
                <th>rate_per_land</th>
                <th>bigunit_id</th>
                <th>package_per_bigunit</th>
                <th>ctype</th>

                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    let table;
    $(document).ready(function() {    
        $.get('/api/chemicals', function(response) {
            console.log(response);
            getTable(response.data);
        });
    });


    function clearInput() {
        $('#search-custom').val("");
        if (table) table.search("").draw();
    }

    function getTable(data) {
        table = $('#chemical-table').DataTable({
            data: data,
            dom: 'lrtip',
            destroy: true,
            info: false,
            ordering: false,
            columns: [
                { data: null, render: (data, type, row, meta) => meta.row + 1 },
                { data: 'code' },
                { data: 'name' },
                { data: 'details' },
                { data: 'formula_code' },
                { data: 'standardcode', title: 'Standard',
                    render: function(data, type, row, meta) {
                        return data && data.standard_name ? data.standard_name : '-';
                    }
                },
                { data: 'unit', title: 'Unit Name',
                    render: function(data, type, row, meta) {
                        return data.name;  
                    }
                },
                { data: 'rate_per_land' },
                { data: 'bigunit', title: 'Big Unit Name' },
                { data: 'package_per_bigunit' },
                { data: 'ctype' },
                // { data: 'tmp_schedule_plan_details', title: 'ctype',
                //     render: function(data, type, row, meta) {
                //         return data[0] && data[0].ctype ? data[0].ctype : '-';
                //     }
                // },
                {
                    data: 'id',
                    render: (data, type, row, meta) => `
                        <a href="/Chemicals/${data}/edit" class="btn btn-warning btn-sm">Edit</a>
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
            url: `/api/chemicals/${id}`,
            type: 'DELETE',
            success: function () {
                $.get('/api/chemicals', function(response) {
                    getTable(response.data);
                });
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการลบ');
            }
        });
    }
</script>
