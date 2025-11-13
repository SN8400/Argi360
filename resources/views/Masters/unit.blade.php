@extends('Layouts.app')

@section('title', 'Unit Management')

@section('content')
    <div class="row">
        <div class="col-md-12">
           <a href="javascript:void(0)" class="btn btn-success my-3" onclick="openCreateModal()">Create New Unit</a>
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
    <table class="table" id="unit-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Unit Name</th>
                <th>Detail</th>
                <th>Modified</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unitModalLabel">Unit Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="unit_id">
                    <div class="mb-3">
                        <label for="unit_name" class="form-label">Unit Name</label>
                        <input type="text" class="form-control" id="unit_name" placeholder="Enter unit name">
                    </div>
                    <div class="mb-3">
                        <label for="unit_detail" class="form-label">Detail</label>
                        <textarea class="form-control" id="unit_detail" placeholder="Enter detail"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="clearModal()" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveUnit()">Save</button>
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
    let unitModal;
    let table;

    window.addEventListener('load', function () {
        unitModal = new bootstrap.Modal(document.getElementById('unitModal'), {
            backdrop: 'static',
            keyboard: false
        });

        $.get('/api/units', function(response) {
            getTable(response.data);
        });
    });

    function openCreateModal() {
        $('#unitModalLabel').text('Create Unit');
        $('#unit_id').val('');
        $('#unit_name').val('');
        $('#unit_detail').val('');
        unitModal.show();
    }

    function openEditModal(id, name, detail) {
        $('#unitModalLabel').text('Edit Unit');
        $('#unit_id').val(id);
        $('#unit_name').val(name);
        $('#unit_detail').val(detail);
        unitModal.show();
    }

    function clearModal() {
        $('#unit_id').val('');
        $('#unit_name').val('');
        $('#unit_detail').val('');
    }

    function saveUnit() {
        const id = $('#unit_id').val();
        const name = $('#unit_name').val();
        const detail = $('#unit_detail').val();

        if (name.trim() === '') {
            alert('Please enter a unit name.');
            return;
        }

        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/units/${id}` : `/api/units`;

        $.ajax({
            url: url,
            method: method,
            data: { name, detail },
            success: function(res) {
                unitModal.hide();
                clearModal();
                $.get('/api/units', function(response) {
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
        table = $('#unit-table').DataTable({
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
                { data: 'name', title: 'Unit Name' },
                { data: 'detail', title: 'Detail' },
                { data: 'modified', title: 'Modified' },
                {
                    data: 'id',
                    title: 'Action',
                    render: function (data, type, row, meta) {
                        return `
                        <form action="/units/${row.id}" method="POST" class="align-middle mt-1" onsubmit="return myFunction();">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <a href="javascript:void(0)" class="text-decoration-none me-2" onclick="openEditModal(${row.id}, \`${row.name}\`, \`${row.detail}\`)">
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
            url: `/api/units/${id}`,
            type: 'DELETE',
            success: function (res) {
                $.get('/api/units', function(response) {
                    getTable(response.data);
                });
            },
            error: function (xhr) {
                alert('เกิดข้อผิดพลาดในการลบ กรุณาลองใหม่');
            }
        });
    }
</script>
