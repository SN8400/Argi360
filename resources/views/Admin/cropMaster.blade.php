<!-- resources/views/index.blade.php -->
@extends('Layouts.app')

@section('title', 'Welcome Page')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <a href="#" class="btn btn-success my-3">Create New post</a>
            <hr>
        </div>
    </div>
    <div class="row my-3">
        <div class="col-md-12">
            <form action="#" method="get" class="row g-2 align-items-center">
            <div class="col-12 col-sm-10 order-sm-1 order-md-1 order-lg-1 order-xl-1">
                <input type="text" class="form-control" name="search-custom" id="search-custom" onkeyUp="searchByText(this)" placeholder="Search..." value="">
            </div>
            {{-- <div class="col-6 col-sm-2 order-sm-3 order-md-2 order-lg-2 order-xl-2">
                <button type="submit" class="btn btn-success w-100">Search</button>
            </div> --}}
            <div class="col-12 col-sm-2 order-sm-2 order-md-3 order-lg-3 order-xl-3">
                <button type="button" class="btn btn-danger w-100" onclick="this.form.reset();">Clear</button>
            </div>
            </form>
        </div>
    </div>
    <table class="table" id="crop-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Details</th>
                <th>SAP Code</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Link URL</th>
                <th>Max Per Day</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
@endsection
<!-- jQuery CDN (Google) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    let table;
    $.get('/api/crops', function(response) {
        getTable(response.data)
    });

    function getTable(params) {
        
        table = $('#crop-table').DataTable({
            data: params,
            dom: 'lrtip', // ซ่อน search box เดิม
            destroy: true,
            // serverSide: true,
            info: false,
            ordering: false,
            // paging: false,
            // searching: false,
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'name', title: 'ชื่อ Crop' },
                { data: 'details', title: 'รายละเอียด' },
                { data: 'sap_code', title: 'SAP Code' },
                { data: 'startdate', title: 'วันที่เริ่ม' },
                { data: 'enddate', title: 'วันที่สิ้นสุด' },
                { data: 'linkurl', title: 'URL Link' },
                { data: 'max_per_day', title: 'Max Per Day' },
                { data: 'id', title: 'Action',
                    render: function (data, type, row, meta) {
                        // return params.length - meta.row;  // เช่นเดียวกันถ้าต้องการ
                    return `
                        <form action="/crops/${row.id}" method="POST" class="align-middle" onsubmit="return myFunction();">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <a href="/selectcrop/${row.id}" class="text-decoration-none text-white">
                                <label class="btn btn-primary active">View</label>
                            </a>
                            <a href="/crops/${row.id}/edit" class="text-decoration-none">
                                <label class="btn btn-warning text-white">Edit</label>
                            </a>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger" title="Delete">
                                <i class='bx bx-trash'></i>
                            </button>
                            </div>
                        </form>
                        `;
                    }
                }
            ],
            order: []
        });
    }

    function searchByText(params) {
        if (table) {
            table.search(params.value).draw();
        }
    }
</script>