@extends('Layouts.app')

@section('title', 'Map Matcodes')

@section('content')
    <input type="hidden" id="cropId" value="{{ $id ?? '' }}">
<div class="row">
    <div class="col-md-12">
        <button class="btn btn-success mb-3" id="btnCreate" onclick="openCreateModal()">Create Mapping</button>
        <button class="btn btn-primary mb-3" id="btnClone" onclick="openCloneModal()">Clone</button>
        <hr>
    </div>
</div>

<div class="row my-3">
    <div class="col-md-12">
        <form action="#" method="get" class="row g-2 align-items-center">
            <div class="col-12 col-sm-10">
                <input type="text" class="form-control" name="search-custom" id="search-custom" onkeyup="searchByText(this)" placeholder="Search...">
            </div>
            <div class="col-12 col-sm-2">
                <button type="button" class="btn btn-danger w-100" onclick="clearInput();">Clear</button>
            </div>
        </form>
    </div>
</div>

<table class="table" id="map-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Crop</th>
            <th>Input Item</th>
            <th>Broker</th>
            <th>Harvest By</th>
            <th>Harvest To</th>
            <th>Matcode</th>
            <th>Desc</th>
            <th>Created</th>
            <th>Modified</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Mapping Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="clearModal()"></button>
            </div>
            <div class="modal-body row g-3">
                <input type="hidden" id="map_id">
                <div class="col-md-6">
                    <label class="form-label">Crop</label>
                    <select id="crop" class="form-select"></select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Input Item</label>
                    <select id="input_item" class="form-select"></select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Broker</label>
                    <select id="broker" class="form-select"></select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Harvest By</label>
                    <select id="harvest_by" class="form-select">
                        <option value="L">แรงงาน</option>
                        <option value="H">รถเกี่ยว</option>
                        <option value="M">เครื่องรูด</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Harvest To</label>
                    <select id="harvest_to" class="form-select">
                        <option value="N">ปรกติ</option>
                        <option value="C">cluster</option>
                        <option value="G">รีดเมล็ด</option>
                        <option value="S">รีดเมล็ด2</option>
                        <option value="T">ปรกติหัวแปลง</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Matcode</label>
                    <input type="text" class="form-control" id="matcode">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="desc" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" onclick="clearModal()">Back</button>
                <button class="btn btn-primary" onclick="saveMapping()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Clone Modal -->
<div class="modal fade" id="clone-modal" tabindex="-1" role="dialog" aria-labelledby="cloneModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="cloneForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="cloneModalLabel">Clone Grow State</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#clone-modal').modal('hide')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <!-- From Crop -->
          <div class="form-group">
            <label for="from_crop_id">From Crop</label>
            <select class="form-control" id="from_crop_id" name="from_crop_id" required></select>
          </div>

          <!-- From Item -->
          <div class="form-group">
            <label for="from_broker_id">From Broker</label>
            <select class="form-control" id="from_broker_id" name="from_broker_id" required></select>
          </div>

          <hr>

          <!-- To Crop (fixed label) -->
          <div class="form-group mb-2">
            <label for="to_crop_id">To Crop:</label>
            <select class="form-control" id="to_crop_id" name="to_crop_id" required></select>
          </div>

          <!-- To Item -->
          <div class="form-group">
            <label for="to_broker_id">To Broker</label>
            <select class="form-control" id="to_broker_id" name="to_broker_id" required></select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="onCloneState()">Clone</button>
          <button type="button" class="btn btn-secondary" onclick="$('#clone-modal').modal('hide')">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

<!-- Include CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- JavaScript -->
<script>
    let mapModal;
    let table;

    window.addEventListener('load', function () {
        mapModal = new bootstrap.Modal(document.getElementById('mapModal'), { backdrop: 'static', keyboard: false });

        loadDropdowns();
        getData();
    });

    function getData(){
        let crop_id = $('#cropId').val();
        $.ajax({
            url: '/api/mapMatcodes',
            method: 'GET',
            data: { crop_id },
            success: function(res) {
                getTable(res.data)
            },
            error: function(xhr) {
                alert('Error saving data.');
            }
        });
    }

    function loadDropdowns() {
        let crop_id = $('#cropId').val();
        $.ajax({
            url: '/api/crops/' + crop_id,
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                $('#crop').empty().append(`<option value="">Select Crop</option>`);
                $('#crop').append(`<option value="${data.data.id}">${data.data.name}</option>`);
                // $.each(data.data, function(i, item) {
                // console.log(item);
                //     $('#crop').append(`<option value="${item.id}">${item.name}</option>`);
                // });
            }
        });

        $.ajax({
            url: '/api/crops',
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                $('#from_crop_id, #to_crop_id').empty().append(`<option value="">Select Crop</option>`);
                $.each(data.data, function(i, item) {
                    $('#from_crop_id, #to_crop_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });


        $.ajax({
            url: '/api/brokers',
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                $('#broker, #from_broker_id, #to_broker_id').empty().append(`<option value="">Select Broker</option>`);
                $.each(data.data, function(i, item) {
                    $('#broker, #from_broker_id, #to_broker_id').append(`<option value="${item.id}">${item.code + " " + item.fname + " " + item.lname}</option>`);
                });
            }
        });

        $.ajax({
            url: '/api/InputItems',
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                $('#input_item').empty().append(`<option value="">Select Area</option>`);
                $.each(data.data, function(i, item) {
                    $('#input_item').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });
    }

    function openCreateModal() {
        $('#mapModalLabel').text('Create Mapping');
        clearModal();
        mapModal.show();
    }

    function openEditModal(row) {
        $('#mapModalLabel').text('Edit Mapping');
        $('#map_id').val(row.id);
        $('#crop').val(row.crop_id);
        $('#input_item').val(row.input_item_id);
        $('#broker').val(row.broker_id);
        $('#harvest_by').val(row.harvest_by);
        $('#harvest_to').val(row.harvest_to);
        $('#matcode').val(row.matcode);
        $('#desc').val(row.desc);
        mapModal.show();
    }

    function clearModal() {
        $('#map_id, #matcode, #desc').val('');
        $('#crop, #input_item, #broker, #harvest_by, #harvest_to').val('');
    }

    function saveMapping() {
        const id = $('#map_id').val();
        const payload = {
            crop_id: $('#crop').val(),
            input_item_id: $('#input_item').val(),
            broker_id: $('#broker').val(),
            harvest_by: $('#harvest_by').val(),
            harvest_to: $('#harvest_to').val(),
            matcode: $('#matcode').val(),
            desc: $('#desc').val()
        };

        const method = id ? 'PUT' : 'POST';
        const url = id ? `/api/mapMatcodes/${id}` : '/api/mapMatcodes';

        $.ajax({ url, method, data: payload })
            .done(res => {
                mapModal.hide();
                clearModal();
                getData();
            })
            .fail(() => alert('เกิดข้อผิดพลาดในการบันทึก'));
    }

    function onDelete(id) {
        if (!confirm('คุณแน่ใจว่าต้องการลบ?')) return;
        $.ajax({
            url: `/api/mapMatcodes/${id}`,
            type: 'DELETE',
            success: () => getData(),
            error: () => alert('เกิดข้อผิดพลาดในการลบ')
        });
    }

    function clearInput() {
        $('#search-custom').val('');
        if (table) table.search('').draw();
    }

    function searchByText(e) {
        if (table) table.search(e.value).draw();
    }

    function getTable(data) {
        table = $('#map-table').DataTable({
            data: data,
            destroy: true,
            dom: 'lrtip',
            ordering: false,
            info: false,
            columns: [
                { data: null, render: (d, t, r, m) => m.row + 1 },
                { data: 'crop', title: 'crop_name',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    } 
                },
                { data: 'inputitem', title: 'input_item_name',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    } 
                },
                { data: 'broker', title: 'broker_name',
                    render: function(data, type, row, meta) {
                        return data && data.code ? data.code + " " + data.fname + " " + data.lname : '-';
                    } 
                },
                { data: 'harvest_by' },
                { data: 'harvest_to' },
                { data: 'matcode' },
                { data: 'desc' },
                { data: 'created' },
                { data: 'modified' },
                {
                    data: null,
                    render: row => `
                        <div class="btn-group">
                            <a href="javascript:void(0)" class="btn btn-warning text-white me-1" onclick='openEditModal(${JSON.stringify(row)})'>Edit</a>
                            <button class="btn btn-danger" onclick="onDelete(${row.id})">Delete</button>
                        </div>
                    `
                }
            ]
        });
    }

    function onCloneState() {  
        let crop_id = $('#cropId').val();
        let formData = {
            from_crop_id: $('#from_crop_id').val(),
            from_broker_id: $('#from_broker_id').val(),
            to_crop_id: $('#to_crop_id').val(),
            to_broker_id:$('#to_broker_id').val()
        };
        console.log(formData);
        $.ajax({
            url: '/api/mapMatcodes/clone',
            type: 'POST',
            data: formData,
            success: function (res) {
                $('#clone-modal').modal('hide');
                console.log(res);   
                getData();
            },
            error: function (xhr) {
                console.error(xhr);   
                alert('เกิดข้อผิดพลาดในการคัดลอก กรุณาลองใหม่');
            }
        });   
    }

    function openCloneModal() {
        $('#clone-modal').modal('show');
    }
</script>
