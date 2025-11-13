@extends('Layouts.app')

@section('title', 'Unit Management')

@section('content')
    <input type="hidden" id="cropId" value="{{ $id ?? '' }}">
    <div class="row">
        <div class="col-md-12">
           <a href="javascript:void(0)" class="btn btn-success my-3" onclick="openCreateModal()">Create New Seed Pack</a>
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
                <th>Seed Code</th>
                <th>Name</th>
                <th>Pack Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

<!-- Seed Pack Modal -->
<div class="modal fade" id="seedPackModal" tabindex="-1" aria-labelledby="seedPackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="seedPackModalLabel">Seed Pack Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="seed_pack_id">

                <!-- Seed Code -->
                <div class="mb-3">
                    <label for="seed_code" class="form-label">Seed Code</label>
                    <select class="form-select" id="seed_code">
                        <option value="">-- Select Seed Code --</option>
                    </select>
                </div>

                <!-- Name -->
                <div class="mb-3">
                    <label for="seed_name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="seed_name" placeholder="Enter name">
                </div>

                <!-- Detail -->
                <div class="mb-3">
                    <label for="seed_detail" class="form-label">Detail</label>
                    <textarea class="form-control" id="seed_detail" placeholder="Enter detail"></textarea>
                </div>

                <!-- Pack Date -->
                <div class="mb-3">
                    <label for="pack_date" class="form-label">Pack Date</label>
                    <input type="date" class="form-control" id="pack_date">
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <label for="seed_status" class="form-label">Status</label>
                    <select class="form-select" id="seed_status">
                        <option value="">-- Select Status --</option>
                        <option value="1">Available</option>
                        <option value="0">Unavailable</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="clearModal()" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveSeedPack()">Save</button>
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
    let seedPackModal;
    let table;

    window.addEventListener('load', function () {
        let cropId = $('#cropId').val();


        seedPackModal = new bootstrap.Modal(document.getElementById('seedPackModal'), {
            backdrop: 'static',
            keyboard: false
        });

        // โหลด seed code มาเติมใน dropdown (optional)
        $.get('/api/seedPack/' + cropId, function(response) {
            const seedCodeSelect = $('#seed_code');
            Object.entries(response.data).forEach(([id, name]) => {
                $('#seed_code').append(`<option value="${id}">${name}</option>`);
            });
        });

        getData();

    });

    function getData() {
        let cropId = $('#cropId').val();

        $.get('/api/seedPack/index/' + cropId, function(response) {
            getTable(response.data);
            clearInput();
        });
    }

    function openCreateModal() {
        $('#seedPackModalLabel').text('Create Seed Pack');
        $('#seed_pack_id').val('');
        $('#seed_code').val('');
        $('#seed_name').val('');
        $('#seed_detail').val('');
        $('#pack_date').val('');
        $('#seed_status').val('');
        seedPackModal.show();
    }

    function openEditModal(id, seed_code_id, name, detail, pack_date, status) {
        $('#seedPackModalLabel').text('Edit Seed Pack');
        $('#seed_pack_id').val(id);
        $('#seed_code').val(seed_code_id);
        $('#seed_name').val(name);
        $('#seed_detail').val(detail);
        $('#pack_date').val(pack_date);
        $('#seed_status').val(status);
        seedPackModal.show();
    }

    function clearModal() {
        $('#seed_pack_id').val('');
        $('#seed_code').val('');
        $('#seed_name').val('');
        $('#seed_detail').val('');
        $('#pack_date').val('');
        $('#seed_status').val('');
    }

    function saveSeedPack() {
        const id = $('#seed_pack_id').val();
        const seed_code_id = $('#seed_code').val();
        const name = $('#seed_name').val();
        const details = $('#seed_detail').val();
        const pack_date = $('#pack_date').val();
        const status = $('#seed_status').val();

        if (!seed_code_id || !name || !pack_date || !status) {
            alert('Please fill in all required fields.');
            return;
        }

        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/seedPack/${id}` : `/api/seedPack`;
        $.ajax({
            url: url,
            method: method,
            data: {
                seed_code_id,
                name,
                details,
                pack_date,
                status
            },
            success: function(res) {
                seedPackModal.hide();
                clearModal();
                getData();
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
                { data: 'seedcode', title: 'Seed Code',
                    render: function(data, type, row, meta) {
                        return data && data.code ? data.code : '-';
                    } 
                },
                { data: 'name', title: 'Name' },
                { data: 'pack_date', title: 'Pack Date' },
                { data: 'status', title: 'Status' },
                {
                    data: 'id',
                    title: 'Action',
                    render: function (data, type, row, meta) {
                        return `
                        <form action="/seedPack/${row.id}" method="POST" class="align-middle mt-1" onsubmit="return myFunction();">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <a href="javascript:void(0)" class="text-decoration-none me-2" onclick="openEditModal(${row.id}, \`${row.seed_code_id}\`, \`${row.name}\`, \`${row.details}\`, \`${row.pack_date}\`, \`${row.status}\`)">
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
            url: `/api/seedPack/${id}`,
            type: 'DELETE',
            success: function (res) {
              getData();
            },
            error: function (xhr) {
                alert('เกิดข้อผิดพลาดในการลบ กรุณาลองใหม่');
            }
        });
    }
</script>
