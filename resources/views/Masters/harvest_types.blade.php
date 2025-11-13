@extends('Layouts.app')

@section('title', 'Harvest Types Management')

@section('content')
<div class="row">
    <div class="col-md-12">
        <a href="javascript:void(0)" class="btn btn-success my-3" onclick="openCreateModal()">Create Harvest Type</a>
        <hr>
    </div>
</div>
<div class="row my-3">
    <div class="col-md-12">
        <input type="text" class="form-control" id="search-custom" onkeyup="searchByText(this)" placeholder="Search...">
    </div>
</div>
<table class="table" id="harvest-type-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Code</th>
            <th>Name</th>
            <th>Note</th>
            <th>Modified</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="harvestTypeModal" tabindex="-1" aria-labelledby="harvestTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Harvest Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="clearModal()"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="harvest_type_id">
                <div class="mb-3">
                    <label for="code" class="form-label">Code</label>
                    <input type="text" class="form-control" id="code">
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name">
                </div>
                <div class="mb-3">
                    <label for="note" class="form-label">Note</label>
                    <textarea class="form-control" id="note"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="clearModal()" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveHarvestType()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts -->
<script>
    let harvestTypeModal;
    let table;

    window.addEventListener('load', function () {
        harvestTypeModal = new bootstrap.Modal(document.getElementById('harvestTypeModal'), {
            backdrop: 'static',
            keyboard: false
        });

        $.get('/api/harvestTypes', function (response) {
            getTable(response.data);
        });
    });

    function openCreateModal() {
        $('#harvest_type_id').val('');
        $('#code').val('');
        $('#name').val('');
        $('#note').val('');
        $('#harvestTypeModal .modal-title').text('Create Harvest Type');
        harvestTypeModal.show();
    }

    function openEditModal(id, code, name, note) {
        $('#harvest_type_id').val(id);
        $('#code').val(code);
        $('#name').val(name);
        $('#note').val(note);
        $('#harvestTypeModal .modal-title').text('Edit Harvest Type');
        harvestTypeModal.show();
    }

    function clearModal() {
        $('#harvest_type_id').val('');
        $('#code').val('');
        $('#name').val('');
        $('#note').val('');
    }

    function saveHarvestType() {
        const id = $('#harvest_type_id').val();
        const data = {
            code: $('#code').val(),
            name: $('#name').val(),
            note: $('#note').val()
        };

        if (!data.code || !data.name) {
            alert('Code and Name are required.');
            return;
        }

        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/harvestTypes/${id}` : '/api/harvestTypes';

        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function () {
                harvestTypeModal.hide();
                clearModal();
                $.get('/api/harvestTypes', function (response) {
                    getTable(response.data);
                });
            },
            error: function () {
                alert('Save failed.');
            }
        });
    }

    function getTable(data) {
        table = $('#harvest-type-table').DataTable({
            data: data,
            destroy: true,
            dom: 'lrtip',
            ordering: false,
            columns: [
                { data: null, render: (d, t, r, m) => m.row + 1 },
                { data: 'code' },
                { data: 'name' },
                { data: 'note' },
                { data: 'modified' },
                {
                    data: null,
                    render: (d, t, r) => `
                        <a href="javascript:void(0)" class="btn btn-warning btn-sm text-white me-1"
                           onclick="openEditModal(${r.id}, '${r.code}', '${r.name}', '${r.note ?? ''}')">Edit</a>
                        <button class="btn btn-danger btn-sm" onclick="onDelete(${r.id})">Delete</button>
                    `
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
        if (!confirm('Are you sure to delete this item?')) return;

        $.ajax({
            url: `/api/harvestTypes/${id}`,
            type: 'DELETE',
            success: function () {
                $.get('/api/harvestTypes', function (response) {
                    getTable(response.data);
                });
            },
            error: function () {
                alert('Delete failed.');
            }
        });
    }
</script>
