@extends('Layouts.app')

@section('title', 'Broker Area Management')

@section('content')
    <div class="row">
        <div class="col-md-12">
           <a href="javascript:void(0)" class="btn btn-success my-3" onclick="openCreateModal()">Create New Broker Area</a>
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
    <table class="table" id="brokerArea-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Crop</th>
                <th>Broker</th>
                <th>Area</th>
                <th>Modified</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

<!-- Broker Area Modal -->
<div class="modal fade" id="brokerAreaModal" tabindex="-1" aria-labelledby="brokerAreaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="brokerAreaModalLabel">Broker Area Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="broker_area_id">

                <!-- Crop Dropdown -->
                <div class="mb-3">
                    <label for="crop_id" class="form-label">Crop</label>
                    <select class="form-select" id="crop_id">
                        <option value="">-- Select Crop --</option>
                        <!-- Populate options dynamically -->
                        <option value="1">Rice</option>
                        <option value="2">Corn</option>
                    </select>
                </div>

                <!-- Broker Dropdown -->
                <div class="mb-3">
                    <label for="broker_id" class="form-label">Broker</label>
                    <select class="form-select" id="broker_id">
                        <option value="">-- Select Broker --</option>
                        <!-- Populate options dynamically -->
                        <option value="1">Broker A</option>
                        <option value="2">Broker B</option>
                    </select>
                </div>

                <!-- Area Dropdown -->
                <div class="mb-3">
                    <label for="area_id" class="form-label">Area</label>
                    <select class="form-select" id="area_id">
                        <option value="">-- Select Area --</option>
                        <!-- Populate options dynamically -->
                        <option value="1">North</option>
                        <option value="2">South</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="clearModal()" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveBrokerArea()">Save</button>
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
    let brokerAreaModal;
    let table;

    window.addEventListener('load', function () {
        brokerAreaModal = new bootstrap.Modal(document.getElementById('brokerAreaModal'), {
            backdrop: 'static',
            keyboard: false
        });

        $.get('/api/brokerArea', function(response) {
            getTable(response.data);
            clearInput();
        });

        loadDropdowns();
    });

    function openCreateModal() {
        $('#brokerAreaModalLabel').text('Create Broker Area');
        $('#broker_area_id').val('');
        $('#crop_id').val('');
        $('#broker_id').val('');
        $('#area_id').val('');
        brokerAreaModal.show();
    }

    function openEditModal(id, crop_id, broker_id, area_id) {
        $('#brokerAreaModalLabel').text('Edit Broker Area');
        $('#broker_area_id').val(id);
        $('#crop_id').val(crop_id);
        $('#broker_id').val(broker_id);
        $('#area_id').val(area_id);
        brokerAreaModal.show();
    }

    function clearModal() {
        $('#broker_area_id').val('');
        $('#crop_id').val('');
        $('#broker_id').val('');
        $('#area_id').val('');
    }

    function saveBrokerArea() {
        const id = $('#broker_area_id').val();
        const crop_id = $('#crop_id').val();
        const broker_id = $('#broker_id').val();
        const area_id = $('#area_id').val();

        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/brokerArea/${id}` : `/api/brokerArea`;

        $.ajax({
            url: url,
            method: method,
            data: { crop_id, broker_id, area_id },
            success: function(res) {
                brokerAreaModal.hide();
                clearModal();
                $.get('/api/brokerArea', function(response) {
                    getTable(response.data);
                    clearInput();
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
        table = $('#brokerArea-table').DataTable({
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
                { data: 'crop', title: 'Crop',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    } 
                },
                { data: 'broker', title: 'Broker',
                    render: function(data, type, row, meta) {
                        return data && data.fname ? data.init + " " + data.fname + " " + data.lname : '-';
                    } 
                },
                { data: 'area', title: 'Area',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    } 
                },
                { data: 'modified', title: 'Modified' },
                {
                    data: 'id',
                    title: 'Action',
                    render: function (data, type, row, meta) {
                        return `
                        <form action="/brokerArea/${row.id}" method="POST" class="align-middle mt-1" onsubmit="return myFunction();">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <a href="javascript:void(0)" class="text-decoration-none me-2" onclick="openEditModal(${row.id}, \`${row.crop.id}\`, \`${row.broker.id}\`, \`${row.area.id}\`)">
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
            url: `/api/brokerArea/${id}`,
            type: 'DELETE',
            success: function (res) {
                $.get('/api/brokerArea', function(response) {
                    getTable(response.data);
                    clearInput();
                });
            },
            error: function (xhr) {
                alert('เกิดข้อผิดพลาดในการลบ กรุณาลองใหม่');
            }
        });
    }

    
        // Load dropdowns
    function loadDropdowns(){
        $.ajax({
            url: '/api/crops',
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                
                console.log(data);
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
                console.log(data);
                $('#broker_id').empty().append(`<option value="">Select Broker</option>`);
                $.each(data.data, function(i, item) {
                    $('#broker_id').append(`<option value="${item.id}">${item.init + " " + item.fname + " " + item.lname}</option>`);
                });
            }
        });

        $.ajax({
            url: '/api/areas',
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                console.log(data);
                $('#area_id').empty().append(`<option value="">Select Area</option>`);
                $.each(data.data, function(i, item) {
                    $('#area_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });


    }
</script>
