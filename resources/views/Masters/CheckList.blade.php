@extends('Layouts.app')

@section('title', 'Checklist Management')

@section('content')
<div class="row">
    <div class="col-md-12">
        <a href="javascript:void(0)" class="btn btn-success my-3" onclick="openCreateModal()">Create New Checklist</a>
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

<table class="table" id="checklist-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Type</th>
            <th>Seq</th>
            <th>Name</th>
            <th>Name (Eng)</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="checklistModal" tabindex="-1" aria-labelledby="checklistModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Checklist Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="clearModal()"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="checklist_id">
                <div class="mb-2">
                    <label class="form-label">Type</label>
                    <input type="text" class="form-control" id="checklist_type">
                </div>
                <div class="mb-2">
                    <label class="form-label">Sequence</label>
                    <input type="number" class="form-control" id="checklist_seq">
                </div>
                <div class="mb-2">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" id="checklist_name">
                </div>
                <div class="mb-2">
                    <label class="form-label">Name (Eng)</label>
                    <input type="text" class="form-control" id="checklist_name_eng">
                </div>
                <div class="mb-2">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="checklist_desc"></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="checklist_status">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="clearModal()" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveChecklist()">Save</button>
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

<!-- JavaScript -->
<script>
    let checklistModal;
    let table;

    window.addEventListener('load', function () {
        checklistModal = new bootstrap.Modal(document.getElementById('checklistModal'), {
            backdrop: 'static',
            keyboard: false
        });

        $.get('/api/checklist', function(response) {
            getTable(response.data);
        });
    });

    function openCreateModal() {
        $('#checklistModalLabel').text('Create Checklist');
        clearModal();
        checklistModal.show();
    }

    function openEditModal(item) {
        $('#checklistModalLabel').text('Edit Checklist');
        $('#checklist_id').val(item.id);
        $('#checklist_type').val(item.type);
        $('#checklist_seq').val(item.seq);
        $('#checklist_name').val(item.name);
        $('#checklist_name_eng').val(item.name_eng);
        $('#checklist_desc').val(item.desc);
        $('#checklist_status').val(item.status);
        checklistModal.show();
    }

    function clearModal() {
        $('#checklist_id').val('');
        $('#checklist_type').val('');
        $('#checklist_seq').val('');
        $('#checklist_name').val('');
        $('#checklist_name_eng').val('');
        $('#checklist_desc').val('');
        $('#checklist_status').val('active');
        clearInput();
    }

    function saveChecklist() {
        const id = $('#checklist_id').val();
        const data = {
            type: $('#checklist_type').val(),
            seq: $('#checklist_seq').val(),
            name: $('#checklist_name').val(),
            name_eng: $('#checklist_name_eng').val(),
            desc: $('#checklist_desc').val(),
            status: $('#checklist_status').val()
        };

        if (!data.name.trim()) {
            alert("Please enter name");
            return;
        }

        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/checklist/${id}` : `/api/checklist`;

        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function (res) {
                checklistModal.hide();
                clearModal();
                $.get('/api/checklist', function(response) {
                    getTable(response.data);
                });
            },
            error: function () {
                alert("Error saving data");
            }
        });
    }

    function onDelete(id) {
        if (!confirm('คุณต้องการลบข้อมูลใช่หรือไม่?')) return;

        $.ajax({
            url: `/api/checklist/${id}`,
            type: 'DELETE',
            success: function () {
                $.get('/api/checklist', function(response) {
                    getTable(response.data);
                });
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการลบ กรุณาลองใหม่');
            }
        });
    }

    function getTable(data) {
        table = $('#checklist-table').DataTable({
            data: data,
            dom: 'lrtip',
            destroy: true,
            info: false,
            ordering: false,
            columns: [
                { data: null, render: (_, __, ___, meta) => meta.row + 1 },
                { data: 'type' },
                { data: 'seq' },
                { data: 'name' },
                { data: 'name_eng' },
                { data: 'desc' },
                { data: 'status' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <div class="btn-group">
                                <button class="btn btn-warning text-white" onclick='openEditModal(${JSON.stringify(row)})'>Edit</button>
                                <button class="btn btn-danger" onclick="onDelete(${row.id})">Delete</button>
                            </div>
                        `;
                    }
                }
            ]
        });
    }

    function clearInput() {
        $('#search-custom').val("");
        if (table) table.search("").draw();
    }

    function searchByText(el) {
        if (table) table.search(el.value).draw();
    }
</script>
