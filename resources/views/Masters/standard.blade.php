@extends('Layouts.app')

@section('title', 'Standard Code Management')

@section('content')
<div class="row">
    <div class="col-md-12">
        <a href="javascript:void(0)" class="btn btn-success my-3" onclick="openCreateModal()">Create New Standard</a>
        <hr>
    </div>
</div>

<div class="row my-3">
    <div class="col-md-12">
        <form action="#" method="get" class="row g-2 align-items-center">
            <div class="col-12 col-sm-10 order-sm-1">
                <input type="text" class="form-control" name="search-custom" id="search-custom" onkeyup="searchByText(this)" placeholder="Search...">
            </div>
            <div class="col-12 col-sm-2 order-sm-2">
                <button type="button" class="btn btn-danger w-100" onclick="clearInput();">Clear</button>
            </div>
        </form>
    </div>
</div>

<table class="table" id="standard-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Standard Name</th>
            <th>Detail</th>
            <th>Chemical Type</th>
            <th>MRLs</th>
            <th>Major Type</th>
            <th>Type Code</th>
            <th>Rate</th>
            <th>Modified</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="standardModal" tabindex="-1" aria-labelledby="standardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Standard Code Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="standard_id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="standard_name" class="form-label">Standard Name</label>
                        <input type="text" class="form-control" id="standard_name">
                    </div>
                    <div class="col-md-6">
                        <label for="chemical_type" class="form-label">Chemical Type</label>
                        <input type="text" class="form-control" id="chemical_type">
                    </div>
                    <div class="col-md-6">
                        <label for="MRLs" class="form-label">MRLs</label>
                        <input type="text" class="form-control" id="MRLs">
                    </div>
                    <div class="col-md-6">
                        <label for="major_type" class="form-label">Major Type</label>
                        <input type="text" class="form-control" id="major_type">
                    </div>
                    <div class="col-md-6">
                        <label for="type_code" class="form-label">Type Code</label>
                        <input type="text" class="form-control" id="type_code">
                    </div>
                    <div class="col-md-6">
                        <label for="rate" class="form-label">Rate</label>
                        <input type="text" class="form-control" id="rate">
                    </div>
                    <div class="col-12">
                        <label for="details" class="form-label">Detail</label>
                        <textarea class="form-control" id="details"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="clearModal()" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveStandard()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- JavaScript -->
<script>
let standardModal;
let table;

window.addEventListener('load', function () {
    standardModal = new bootstrap.Modal(document.getElementById('standardModal'), {
        backdrop: 'static',
        keyboard: false
    });

    $.get('/api/standards', function(response) {
        console.log(response);
        getTable(response.data);
    });
});

function openCreateModal() {
    $('#standardModalLabel').text('Create Standard');
    clearModal();
    standardModal.show();
}

function openEditModal(data) {
    $('#standard_id').val(data.id);
    $('#standard_name').val(data.standard_name);
    $('#chemical_type').val(data.chemical_type);
    $('#MRLs').val(data.MRLs);
    $('#major_type').val(data.major_type);
    $('#type_code').val(data.type_code);
    $('#rate').val(data.rate);
    $('#details').val(data.details);
    $('#standardModalLabel').text('Edit Standard');
    standardModal.show();
}

function clearModal() {
    $('#standard_id').val('');
    $('#standard_name, #chemical_type, #MRLs, #major_type, #type_code, #rate, #details').val('');
}

function saveStandard() {
    const id = $('#standard_id').val();
    const payload = {
        standard_name: $('#standard_name').val(),
        chemical_type: $('#chemical_type').val(),
        MRLs: $('#MRLs').val(),
        major_type: $('#major_type').val(),
        type_code: $('#type_code').val(),
        rate: $('#rate').val(),
        details: $('#details').val()
    };

    const method = id ? 'PUT' : 'POST';
    const url = id ? `/api/standards/${id}` : `/api/standards`;

    $.ajax({
        url: url,
        method: method,
        data: payload,
        success: function(res) {
            standardModal.hide();
            clearModal();
            $.get('/api/standards', function(response) {
                getTable(response.data);
            });
        },
        error: function() {
            alert('Error saving data.');
        }
    });
}

function onDelete(id) {
    if (!confirm('คุณต้องการลบข้อมูลใช่หรือไม่?')) return;

    $.ajax({
        url: `/api/standards/${id}`,
        type: 'DELETE',
        success: function () {
            $.get('/api/standards', function(response) {
                getTable(response.data);
            });
        },
        error: function () {
            alert('เกิดข้อผิดพลาดในการลบ กรุณาลองใหม่');
        }
    });
}

function searchByText(el) {
    if (table) table.search(el.value).draw();
}

function clearInput() {
    $('#search-custom').val("");
    if (table) table.search("").draw();
}

function getTable(data) {
    table = $('#standard-table').DataTable({
        data: data,
        dom: 'lrtip',
        destroy: true,
        info: false,
        ordering: false,
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1 },
            { data: 'standard_name' },
            { data: 'details' },
            { data: 'chemical_type' },
            { data: 'MRLs' },
            { data: 'major_type' },
            { data: 'type_code' },
            { data: 'rate' },
            { data: 'modified' },
            {
                data: null,
                render: function (data, type, row) {
                    const dataJSON = JSON.stringify(row).replace(/"/g, '&quot;');
                    return `
                        <div class="btn-group">
                            <button class="btn btn-warning text-white" onclick="openEditModal(${dataJSON})">Edit</button>
                            <button class="btn btn-danger" onclick="onDelete(${row.id})">Delete</button>
                        </div>
                    `;
                }
            }
        ],
        order: []
    });
}
</script>
