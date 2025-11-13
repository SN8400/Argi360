@extends('Layouts.app')
@section('title', 'Farmers Page')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('farmers.create') }}" class="btn btn-success my-3">Create New Farmers</a>
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
    <table class="table" id="farmer-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Name</th>
                <th>Citizen ID</th>
                <th>Address</th>
                <th>Province</th>
                <th>City</th>
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
        $.ajax({
            url: '/api/farmers',
            method: 'GET',
            success: function(res) {
                console.log(res);
                getTable(res.data);
            },
            error: function(xhr) {
                alert('Error fetching farmer data.');
            }
        });
    }

    function clearInput() {
        $('#search-custom').val("");
        if (table) table.search("").draw();
    }

    function searchByText(params) {
        if (table) table.search(params.value).draw();
    }

    function getTable(data) {
        table = $('#farmer-table').DataTable({
            data: data,
            dom: 'lrtip',
            destroy: true,
            info: false,
            ordering: false,
            pageLength: 50,
            columns: [
                { data: null, render: (data, type, row, meta) => meta.row + 1 },
                { data: 'code', title: 'Code' },
                { 
                    data: null,
                    title: 'Name',
                    render: function (data) {
                        return `${data.init ?? ''} ${data.fname ?? ''} ${data.lname ?? ''}`;
                    }
                },
                { data: 'citizenid', title: 'Citizen ID' },
                { 
                    data: null,
                    title: 'Address',
                    render: function(data) {
                        return `${data.address1 ?? ''} ${data.address2 ?? ''} ${data.address3 ?? ''} ${data.sub_cities ?? ''}`;
                    }
                },
                {
                    data: 'city',
                    render: data => data?.th_name ?? '-'
                },
                {
                    data: 'province',
                    render: data => data?.th_name ?? '-'
                },
                {
                    data: 'id',
                    render: (data) => `
                        <a href="/farmers/${data}/edit" class="btn btn-warning btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm" onclick="onDelete(${data})">Delete</button>
                    `
                }
            ]
        });
    }

    function onDelete(id) {
        if (!confirm('คุณต้องการลบข้อมูลใช่หรือไม่?')) return;

        $.ajax({
            url: `/api/farmers/${id}`,
            type: 'DELETE',
            success: function () {
                getData(); // reload table
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการลบ');
            }
        });
    }
</script>
