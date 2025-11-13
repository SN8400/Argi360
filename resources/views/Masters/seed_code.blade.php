@extends('Layouts.app')

@section('title', 'Seed Code Management')

@section('content')
<input type="hidden" id="crop_id" value="{{ $id ?? '' }}">
<div class="row">
    <div class="col-md-12">
        <a href="javascript:void(0)" class="btn btn-success my-3" onclick="openCreateModal()">Create New Seed Code</a>
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
<table class="table" id="seed-code-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Crop</th>
            <th>Input Item</th>
            <th>Code</th>
            <th>Details</th>
            <th>Val per Area</th>
            <th>Seed per Kg</th>
            <th>Pack Date</th>
            <th>Created</th>
            <th>Modifiled</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="seedCodeModal" tabindex="-1" aria-labelledby="seedCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seedCodeModalLabel">Seed Code Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="seed_id">

                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="crop_name" class="form-label">Crop</label>
                        <input type="text" class="form-control" id="crop_name" name="crop_name" readonly>
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="input_item_id" class="form-label">Input Item</label>
                        <select class="form-select" id="input_item_id">
                            <option value="">-- Select Input Item --</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="seed_code" class="form-label">Code</label>
                        <input type="text" class="form-control" id="seed_code" placeholder="Enter code">
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="val_per_area" class="form-label">Val per Area</label>
                        <input type="text" class="form-control" id="val_per_area" placeholder="Enter value per area">
                    </div>
                </div>

                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="seed_per_kg" class="form-label">Seed per Kg</label>
                        <input type="text" class="form-control" id="seed_per_kg" placeholder="Enter seed per kg">
                    </div>

                    <div class="mb-3 col-md-6">
                        <label for="pack_date" class="form-label">Pack Date</label>
                        <input type="date" class="form-control" id="pack_date">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="seed_detail" class="form-label">Details</label>
                    <textarea class="form-control" id="seed_detail" placeholder="Enter detail"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="clearModal()" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveSeedCode()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection


<script>
    let seedModal;
    let table;

    window.addEventListener('load', function () {
        seedModal = new bootstrap.Modal(document.getElementById('seedCodeModal'), {
            backdrop: 'static',
            keyboard: false
        });

        fetchData();
        loadDropdowns();
    });

    function loadDropdowns() {
        var crop_id = $('#crop_id').val();
        $.ajax({
            url: '/api/InputItems',
            type: 'GET',
            dataType: 'json',
            async: false, 
            success: function(data) {
                $('#input_item_id').empty().append(`<option value="">Select Input</option>`);
                $.each(data.data, function(i, item) {
                    $('#input_item_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });

        $.ajax({
            url: '/api/crops/' + crop_id,
            type: 'GET',
            dataType: 'json',
            async: false, 
            success: function(response) {
                $('#crop_name').val(response.data.name);
            }
        });
    }

    function fetchData() {
        var crop_id = $('#crop_id').val();
        
        $.ajax({
            url: '/api/seedcodes/' + crop_id,
            type: 'GET',
            dataType: 'json',
            async: false, 
            success: function(response) {
                loadTable(response.data);
                clearInput();
            }
        });
    }

    function loadTable(response) {
        table = $('#seed-code-table').DataTable({
            data: response,
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
                { data: 'crop', title: 'Crop',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    }
                },
                { data: 'input_item', title: 'Checklist',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    }
                },
                { data: 'code' },
                { data: 'details' },
                { data: 'val_per_area' },
                { data: 'seed_per_kg' },
                { data: 'val_per_area' },
                { data: 'modified' },
                { data: 'created' },
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

function openCreateModal() {
    $('#seedCodeModalLabel').text('Create Seed Code');
    $('#seed_id').val('');
    $('#input_item_id').val('');
    $('#seed_code').val('');
    $('#val_per_area').val('');
    $('#seed_per_kg').val('');
    $('#pack_date').val('');
    $('#seed_detail').val('');
    seedModal.show();
}

function openEditModal(data) {
    $('#seed_id').val(data.id);
    $('#crop_id').val(data.crop_id);
    $('#input_item_id').val(data.input_item_id);
    $('#seed_code').val(data.code);
    $('#val_per_area').val(data.val_per_area ?? '');
    $('#seed_per_kg').val(data.seed_per_kg ?? '');
    $('#pack_date').val(data.pack_date ?? '');
    $('#seed_detail').val(data.details ?? '');
    seedModal.show();
}

function clearModal() {
    $('#seed_id').val('');
    $('#input_item_id').val('');
    $('#seed_code').val('');
    $('#val_per_area').val('');
    $('#seed_per_kg').val('');
    $('#pack_date').val('');
    $('#seed_detail').val('');
}

function saveSeedCode() {
    const id = $('#seed_id').val();
    const data = {
        crop_id: $('#crop_id').val(),
        input_item_id: $('#input_item_id').val(),
        code: $('#seed_code').val(),
        val_per_area: $('#val_per_area').val(),
        seed_per_kg: $('#seed_per_kg').val(),
        pack_date: $('#pack_date').val(),
        details: $('#seed_detail').val()
    };

    const url = id ? `/api/seedcodes/${id}` : '/api/seedcodes';
    const method = id ? 'PUT' : 'POST';

    $.ajax({
      url: url,
        type: method,
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(response) {            
            $('#seedCodeModal').modal('hide');
            clearModal();
            // ใส่ callback หรือ refresh data table ได้ที่นี่ เช่น:
            seedModal.hide();
            fetchData();
        },
        error: function(xhr) {
            alert('Error saving data.');
        }
    });
}

function onDelete(id) {
    if (!confirm('คุณต้องการลบข้อมูลใช่หรือไม่?')) return;

    $.ajax({
        url: `/api/seedcodes/${id}`,
        type: 'DELETE',
        success: function (res) {
            fetchData();
        },
        error: function (xhr) {
            alert('เกิดข้อผิดพลาดในการลบ กรุณาลองใหม่');
        }
    });
}

    function searchByText(params) {
        if (table) {
            table.search(params.value).draw();
        }
    }

    function clearInput() {
        $('#search-custom').val("");
        if (table) {
            table.search("").draw();
        }
    }
</script>