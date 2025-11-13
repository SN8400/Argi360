<!-- resources/views/index.blade.php -->
@extends('Layouts.app')

@section('title', 'Welcome Page')

@section('content')
    <div class="row">
        <div class="col-md-12">
           <a href="javascript:void(0)" class="btn btn-success my-3" onclick="openCreateModal()">Create New Role</a>
            <hr>
        </div>
    </div>
    <div class="row my-3">
        <div class="col-md-12">
            <form action="#" method="get" class="row g-2 align-items-center">
                <div class="col-12 col-sm-10 order-sm-1">
                    <input type="text" class="form-control" name="search-custom" id="search-custom" onkeyup="searchByText(this)" placeholder="Search..." value="">
                </div>
                <div class="col-12 col-sm-2 order-sm-2">
                    <button type="button" class="btn btn-danger w-100" onclick="clearInput();">Clear</button>
                </div>
            </form>
        </div>
    </div>
    <table class="table" id="role-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Role Name</th>
                <th>Modified</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Role Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="role_id">
                    <div class="mb-3">
                        <label for="role_name" class="form-label">Role Name</label>
                        <input type="text" class="form-control" id="role_name" placeholder="Enter role name">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="clearModal()" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveRole()">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

<!-- Include CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript -->
<script>
    let roleModal;
    let table;

    window.addEventListener('load', function () {
        roleModal = new bootstrap.Modal(document.getElementById('roleModal'), {
            backdrop: 'static',
            keyboard: false
        });

        // Load initial data
        $.get('/api/roles', function(response) {
            getTable(response.data);
        });
    });

    function openCreateModal() {
        $('#roleModalLabel').text('Create Role');
        $('#role_id').val('');
        $('#role_name').val('');
        roleModal.show();
    }

    function openEditModal(id, name) {
        $('#roleModalLabel').text('Edit Role');
        $('#role_id').val(id);
        $('#role_name').val(name);
        roleModal.show();
    }

    function clearModal() {
        $('#role_id').val('');
        $('#role_name').val('');
    }

    function saveRole() {
        const id = $('#role_id').val();
        const name = $('#role_name').val();

        if (name.trim() === '') {
            alert('Please enter a role name.');
            return;
        }

        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/roles/${id}` : `/api/roles`;

        $.ajax({
            url: url,
            method: method,
            data: { name },
            success: function(res) {
                roleModal.hide();
                clearModal();
                $.get('/api/roles', function(response) {
                    getTable(response.data);
                });
            },
            error: function(xhr) {
                alert('Error saving data.');
            }
        });
    }

    function clearInput() {
        $('#search-custom').val("");
        if (table) {
            table.search("").draw();
        }
    }

    function getTable(data) {
        table = $('#role-table').DataTable({
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
                { data: 'name', title: 'ชื่อตำแหน่ง' },
                { data: 'modified', title: 'วันเวลาแก้ไขล่าสุด' },
                {
                    data: 'id',
                    title: 'Action',
                    render: function (data, type, row, meta) {
                        return `
                        <form action="/roles/${row.id}" method="POST" class="align-middle mt-1" onsubmit="return myFunction();">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <a href="javascript:void(0)" class="text-decoration-none me-2" onclick="openEditModal(${row.id}, \`${row.name}\`)">
                                    <label class="btn btn-warning text-white">Edit</label>
                                </a>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" class="btn btn-danger" title="Delete" onclick="onDelete(${row.id});">
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
            return;
        }

        $.ajax({
            url: `/api/roles/${id}`,
            type: 'DELETE',
            success: function (res) {
                $.get('/api/roles', function(response) {
                    getTable(response.data);
                });
            },
            error: function (xhr) {
                alert('เกิดข้อผิดพลาดในการบันทึก กรุณาตรวจสอบข้อมูลอีกครั้ง');
            }
        });
    }
</script>
