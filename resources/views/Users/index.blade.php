<!-- resources/views/index.blade.php -->
@extends('Layouts.app')

@section('title', 'Welcome Page')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('Users.create') }}" class="btn btn-success my-3">Create New post</a>
            <hr>
        </div>
    </div>
    <div class="row my-3">
        <div class="col-md-12">
            <form action="#" method="get" class="row g-2 align-items-center">
            <div class="col-12 col-sm-10 order-sm-1 order-md-1 order-lg-1 order-xl-1">
                <input type="text" class="form-control" name="search-custom" id="search-custom" onkeyUp="searchByText(this)" placeholder="Search..." value="">
            </div>
            <div class="col-12 col-sm-2 order-sm-2 order-md-3 order-lg-3 order-xl-3">
                <button type="button" class="btn btn-danger w-100" onclick="clearInput();">Clear</button>
            </div>
            </form>
        </div>
    </div>
    <table class="table" id="crop-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>UserName</th>
                <th>Name</th>
                <th>Group</th>
                <th>Can Edit</th>
                <th>Review Team</th>
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
    $.ajax({
        url: '/api/users',
        method: 'GET',
        headers: {
            'x-api-key': 'popLnwZa007_NajaBrooo'
        },
        success: function(response) {
            getTable(response.data);
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
        }
    });

    function clearInput() {
        $('#search-custom').val("");
        if (table) {
            table.search("").draw();
        }
    }

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
                { data: 'username', title: 'User Name' },
                { data: null, title: 'ชื่อ-นามสกุล',
                    render: function (data, type, row, meta) {
                        return row.fname + " " + row.lname;  // เช่นเดียวกันถ้าต้องการ 
                    }
                },
                { data: 'group', title: 'Role',
                    render: function(data, type, row, meta) {
                        return data.name;  // เอาค่า group.name มาแสดง
                    }
                },
                { data: 'canEdit', title: 'canEdit' },
                { data: 'reviewteam', title: 'Review Team' },
                { data: 'id', title: 'Action',
                    render: function (data, type, row, meta) {
                        // return params.length - meta.row;  // เช่นเดียวกันถ้าต้องการ
                    return `
                        <form action="/crops/${row.id}" method="POST" class="align-middle" onsubmit="return myFunction();">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <a href="/selectcrop/${row.id}" class="text-decoration-none text-white">
                                <label class="btn btn-primary active">View</label>
                            </a>
                            <a href="/User/${row.id}/edit" class="text-decoration-none">
                                <label class="btn btn-warning text-white">Edit</label>
                            </a>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="button" class="btn btn-danger" title="Delete" onclick="onDelete(${row.id});" >
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

    function onDelete(id) {
        if (!confirm('คุณต้องการลบข้อมูลใช่หรือไม่?')) {
            return; // ยกเลิกถ้าผู้ใช้กด Cancel
        }

        $.ajax({
            url: `/api/users/${id}`,  // ✅ ส่ง id ไปด้วย
            type: 'DELETE',           // ✅ ใช้ method DELETE
            success: function (res) {
                $.get('/api/users', function(response) {
                    getTable(response.data)
                });
            },
            error: function (xhr) {
                alert('เกิดข้อผิดพลาดในการบันทึก กรุณาตรวจสอบข้อมูลอีกครั้ง');
            }
        });
    }
</script>