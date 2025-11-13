@extends('Layouts.app')

@section('title', 'Template Plan Management')

@section('content')
<div class="row">
    <div class="col-md-12">
        <a href="javascript:void(0)" class="btn btn-success my-3" onclick="openCreateModal()">Create New Plant Code</a>
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
<table class="table" id="template-plan-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Code</th>
            <th>Name</th>
            <th>Details</th>
            <th>Modified</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="templatePlanModal" tabindex="-1" aria-labelledby="templatePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="templatePlanModalLabel">Plant Code Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="template_id">
                <div class="mb-3">
                    <label for="template_code" class="form-label">Code</label>
                    <input type="text" class="form-control" id="template_code" placeholder="Enter code">
                </div>
                <div class="mb-3">
                    <label for="template_name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="template_name" placeholder="Enter name">
                </div>
                <div class="mb-3">
                    <label for="template_detail" class="form-label">Details</label>
                    <textarea class="form-control" id="template_detail" placeholder="Enter detail"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="clearModal()" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveTemplate()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Include CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" />

<!-- JavaScript -->
<script>
    let templateModal;
    let table;

    window.addEventListener('load', function () {
        templateModal = new bootstrap.Modal(document.getElementById('templatePlanModal'), {
            backdrop: 'static',
            keyboard: false
        });

        fetchData();
    });

    function fetchData() {
        $.get('/api/plantcode', function(response) {
            getTable(response.data);
        });
    }

    function openCreateModal() {
        $('#templatePlanModalLabel').text('Create Template Plan');
        $('#template_id').val('');
        $('#template_code').val('');
        $('#template_name').val('');
        $('#template_detail').val('');
        templateModal.show();
    }

    function openEditModal(id, code, name, detail) {
        $('#templatePlanModalLabel').text('Edit Template Plan');
        $('#template_id').val(id);
        $('#template_code').val(code);
        $('#template_name').val(name);
        $('#template_detail').val(detail);
        templateModal.show();
    }

    function clearModal() {
        $('#template_id').val('');
        $('#template_code').val('');
        $('#template_name').val('');
        $('#template_detail').val('');
    }

    function saveTemplate() {
        const id = $('#template_id').val();
        const code = $('#template_code').val();
        const name = $('#template_name').val();
        const details = $('#template_detail').val();

        if (!code.trim() || !name.trim()) {
            alert('Please enter code and name.');
            return;
        }

        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/plantcode/${id}` : `/api/plantcode`;

        $.ajax({
            url: url,
            method: method,
            data: { code, name, details },
            success: function(res) {
                templateModal.hide();
                clearModal();
                fetchData();
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
        table = $('#template-plan-table').DataTable({
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
                { data: 'name', title: 'Name' },
                { data: 'details', title: 'Details' },
                { data: 'modified', title: 'Modified' },
                {
                    data: 'id',
                    title: 'Action',
                    render: function (data, type, row, meta) {
                        return `
                        <div class="btn-group">
                            <a href="javascript:void(0)" class="btn btn-warning btn-sm text-white" onclick="openEditModal(${row.id}, \`${row.code}\`, \`${row.name}\`, \`${row.details}\`)">Edit</a>
                            <button type="button" class="btn btn-danger btn-sm" onclick="onDelete(${row.id})">Delete</button>
                        </div>`;
                    }
                }
            ]
        });
    }

    function searchByText(params) {
        if (table) {
            table.search(params.value).draw();
        }
    }

    function onDelete(id) {
        if (!confirm('คุณต้องการลบข้อมูลใช่หรือไม่?')) return;

        $.ajax({
            url: `/api/plantcode/${id}`,
            type: 'DELETE',
            success: function (res) {
                fetchData();
            },
            error: function (xhr) {
                alert('เกิดข้อผิดพลาดในการลบ กรุณาลองใหม่');
            }
        });
    }
</script>
