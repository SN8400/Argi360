@extends('Layouts.app')

@section('title', 'Yield Management')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <a href="javascript:void(0)" class="btn btn-success my-3" onclick="openCreateModal()">Create New Yield</a>
            <hr>
        </div>
    </div>
    <div class="row my-3">
        <div class="col-md-12">
            <form action="#" method="get" class="row g-2 align-items-center">
                <div class="col-12 col-sm-10 order-sm-1">
                    <input type="text" class="form-control" id="search-custom" onkeyup="searchByText(this)" placeholder="Search...">
                </div>
                <div class="col-12 col-sm-2 order-sm-2">
                    <button type="button" class="btn btn-danger w-100" onclick="clearInput();">Clear</button>
                </div>
            </form>
        </div>
    </div>

    <table class="table" id="yield-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Crop</th>
                <th>Area</th>
                <th>Broker</th>
                <th>Input Item</th>
                <th>Harvest Type</th>
                <th>Start</th>
                <th>End</th>
                <th>Rate</th>
                <th>Status</th>
                <th>kg/area</th>
                <th>Modified</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="yieldModal" tabindex="-1" aria-labelledby="yieldModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="yieldModalLabel">Yield Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="yield_id">
                    <!-- Select Inputs (dummy options, replace with AJAX later) -->
                    <select class="form-control mb-2" id="crop_id"></select>
                    <select class="form-control mb-2" id="area_id"></select>
                    <select class="form-control mb-2" id="broker_id"></select>
                    <select class="form-control mb-2" id="input_item_id"></select>
                    <select class="form-control mb-2" id="harvest_type_id"></select>
                    <input type="date" class="form-control mb-2" id="start_date">
                    <input type="date" class="form-control mb-2" id="end_date">
                    <input type="number" step="0.01" class="form-control mb-2" id="rate" placeholder="Rate">
                    <input type="text" class="form-control mb-2" id="status" placeholder="Status">
                    <input type="number" step="0.01" class="form-control mb-2" id="kg_per_area" placeholder="Kg per Area">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="clearModal()">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveYield()">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    let yieldModal, table;

    window.addEventListener('load', function () {
        yieldModal = new bootstrap.Modal(document.getElementById('yieldModal'), { backdrop: 'static', keyboard: false });

        $.get('/api/yields', function(response) {
            console.log(response);
            getTable(response.data);
        });
loadDropdowns();
        // Optionally load dropdowns here
    });

    function openCreateModal() {
        $('#yieldModalLabel').text('Create Yield');
        $('#yield_id').val('');
        clearModal();
        yieldModal.show();
    }

    function openEditModal(row) {
        $('#yieldModalLabel').text('Edit Yield');
        $('#yield_id').val(row.id);
        $('#crop_id').val(row.crop_id);
        $('#area_id').val(row.area_id);
        $('#broker_id').val(row.broker_id);
        $('#input_item_id').val(row.input_item_id);
        $('#harvest_type_id').val(row.harvest_type_id);
        $('#start_date').val(row.start_date);
        $('#end_date').val(row.end_date);
        $('#rate').val(row.rate);
        $('#status').val(row.status);
        $('#kg_per_area').val(row.kg_per_area);
        yieldModal.show();
    }

    function clearModal() {
        $('#yieldModal input, #yieldModal select').val('');
    }

    function saveYield() {
        const data = {
            crop_id: $('#crop_id').val(),
            area_id: $('#area_id').val(),
            broker_id: $('#broker_id').val(),
            input_item_id: $('#input_item_id').val(),
            harvest_type_id: $('#harvest_type_id').val(),
            start_date: $('#start_date').val(),
            end_date: $('#end_date').val(),
            rate: $('#rate').val(),
            status: $('#status').val(),
            kg_per_area: $('#kg_per_area').val(),
        };

        const id = $('#yield_id').val();
        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/yields/${id}` : `/api/yields`;

        $.ajax({ url, method, data,
            success: function(res) {
                yieldModal.hide();
                $.get('/api/yields', res => getTable(res.data));
            },
            error: () => alert('Error saving data')
        });
    }

    function getTable(data) {
        table = $('#yield-table').DataTable({
            data: data,
            destroy: true,
            dom: 'lrtip',
            columns: [
                { data: null, render: (_, __, ___, meta) => meta.row + 1 },
                { data: 'crop', title: 'Crop Name',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    }
                },
                { data: 'area', title: 'Area Name',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    }
                },
                { data: 'broker', title: 'Broker Name',
                    render: function(data, type, row, meta) {
                        return data && data.code ? data.code + " " + data.fname + " " + data.lname : '-';
                    } 
                },
                { data: 'inputitem', title: 'พันธุ์พืช',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    }
                },
                { data: 'harvesttype', title: 'วิธีการเก็บ',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    }
                },
                { data: 'start_date' },
                { data: 'end_date' },
                { data: 'rate' },
                { data: 'status' },
                { data: 'kg_per_area' },
                { data: 'modified' },
                {
                    data: null,
                    render: (data, type, row) => `
                        <div class="btn-group">
                            <button class="btn btn-warning btn-sm" onclick='openEditModal(${JSON.stringify(row)})'>Edit</button>
                            <button class="btn btn-danger btn-sm" onclick='onDelete(${row.id})'>Delete</button>
                        </div>`
                }
            ]
        });
    }

    function onDelete(id) {
        if (!confirm('Are you sure you want to delete this item?')) return;

        $.ajax({
            url: `/api/yields/${id}`,
            type: 'DELETE',
            success: () => $.get('/api/yields', res => getTable(res.data)),
            error: () => alert('Delete failed.')
        });
    }

    function searchByText(input) {
        if (table) table.search(input.value).draw();
    }

    function clearInput() {
        $('#search-custom').val('');
        if (table) table.search('').draw();
    }

    function loadDropdowns() {

        $.ajax({
            url: '/api/crops',
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                $('#crop_id').empty().append(`<option value="">Select Crop</option>`);
                $.each(data.data, function(i, item) {
                    $('#crop_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });


        $.ajax({
            url: '/api/brokers',
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                $('#broker_id').empty().append(`<option value="">Select Broker</option>`);
                $.each(data.data, function(i, item) {
                    $('#broker_id').append(`<option value="${item.id}">${item.code + " " + item.fname + " " + item.lname}</option>`);
                });
            }
        });

        $.ajax({
            url: '/api/InputItems',
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                $('#input_item_id').empty().append(`<option value="">Select Input Items</option>`);
                $.each(data.data, function(i, item) {
                    $('#input_item_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });

        $.ajax({
            url: '/api/harvestTypes',
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                $('#harvest_type_id').empty().append(`<option value="">Select Harvest Type</option>`);
                $.each(data.data, function(i, item) {
                    $('#harvest_type_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });

        $.ajax({
            url: '/api/areas',
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                $('#area_id').empty().append(`<option value="">Select Area</option>`);
                $.each(data.data, function(i, item) {
                    $('#area_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });
    }
</script>
