@extends('layouts.app')

@section('content')    
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Grow States</h2>
            <button class="btn btn-success mb-3" id="btnCreate" onclick="openCreateModal()">Create New</button>
            <button class="btn btn-primary mb-3" id="btnClone" onclick="openCloneModal()">Clone</button>

           {{-- <a href="javascript:void(0)" class="btn btn-success my-3" onclick="openCreateModal()">Create New Unit</a> --}}
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


    <table class="table" id="growStatesTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Crop</th>
                <th>Input Item</th>
                <th>Code</th>
                <th>Name</th>
                <th>age</th>
                <th>created</th>
                <th>modified</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="growStateModal" tabindex="-1" role="dialog" aria-labelledby="growStateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="growStateForm">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="growStateModalLabel">Create / Clone Grow State</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="$('#growStateModal').modal('hide')">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <input type="hidden" id="actionType" name="actionType" value="create">
                    <div class="form-group">
                        <label for="crop_id">Crop</label>
                        <select class="form-control" id="crop_id" name="crop_id" required></select>
                    </div>
                    <div class="form-group">
                        <label for="input_item_id">Input Item</label>
                        <select class="form-control" id="input_item_id" name="input_item_id" required></select>
                    </div>
                    <div class="form-group">
                        <label for="input_id">Code</label>
                        <input type="text" class="form-control" id="state_code" name="state_code" required>
                
                    </div>
                    <div class="form-group">
                        <label for="day_after_plant">Name</label>
                        <input type="text" class="form-control" id="state_name" name="state_name" required>
                    </div>
                    <div class="form-group">
                        <label for="rate">Age</label>
                        <input type="text" class="form-control" id="age" name="age" required>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="onCreateState()">Save</button>
                <button type="button" class="btn btn-secondary" onclick="$('#growStateModal').modal('hide')">Close</button>
            </div>
            <input type="hidden"  id="edit_id" name="edit_id" >
            </div>
        </form>
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
            <label for="from_item_id">From Item</label>
            <select class="form-control" id="from_item_id" name="from_item_id" required></select>
          </div>

          <hr>

          <!-- To Crop (fixed label) -->
          <div class="form-group mb-2">
            <label for="to_crop_id">To Crop:</label>
            <select class="form-control" id="to_crop_id" name="to_crop_id" required></select>
          </div>

          <!-- To Item -->
          <div class="form-group">
            <label for="to_item_id">To Item</label>
            <select class="form-control" id="to_item_id" name="to_item_id" required></select>
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

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    let table;
    $(document).ready(function() {
        getDataList();

        loadDropdowns();
    });

    function getDataList(){
            $.ajax({
                url: '/api/growstates/',
                method: 'GET',
                success: function(res) {
                    if(res.status === 'success') {
                        loadTable(res.data);
                    } else {
                        alert("Data not found.");
                    }
                },
                error: function() {
                    alert("Error loading data.");
                }
            });
    }


    // Load dropdowns
    function loadDropdowns() {
        $.ajax({
            url: '/api/InputItems',
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                $('#input_item_id').empty().append(`<option value="">Select Input</option>`);
                $.each(data.data, function(i, item) {
                    $('#input_item_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });

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
    }

    function openCreateModal() {
        console.log('btnCreate');
        $('#actionType').val('create');
        $('#growStateModalLabel').text('Create Grow State');
        $('#growStateForm')[0].reset();
        loadDropdowns();
        $('#growStateModal').modal('show');
    }

    function loadClonedowns() {
        $.ajax({
            url: '/api/InputItems',
            type: 'GET',
            dataType: 'json',
            async: false, 
            success: function(data) {
                $('#from_item_id, #to_item_id').empty().append(`<option value="">Select Input</option>`);
                $.each(data.data, function(i, item) {
                    $('#from_item_id, #to_item_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });
        
        $.ajax({
            url: '/api/crops',
            type: 'GET',
            dataType: 'json',
            async: false, 
            success: function(data) {
                $('#from_crop_id, #to_crop_id').empty().append(`<option value="">Select Input</option>`);
                $.each(data.data, function(i, item) {
                    $('#from_crop_id, #to_crop_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });
    }

    function openCloneModal() {
        console.log('btnClone');
        loadClonedowns();
        $('#clone-modal').modal('show');
    }

    // Submit Form
    function onCreateState() {
        console.log("onCreateState");
        let formData = {
            id:$('#edit_id').val(),
            crop_id:$('#crop_id').val(),
            input_item_id: $('#input_item_id').val(),
            code: $('#state_code').val(),
            name: $('#state_name').val(),
            age: $('#age').val()
        };

        let action = $('#actionType').val();
        let urlMethod = (action === 'update') ? 'POST' : 'POST';
        let url = (action === 'update') ? `/api/growstates/update` : `/api/growstates`;
        
        $.ajax({
            type: urlMethod,
            url: url,
            data: formData,
            success: function(res) {
                console.log(res);
                $('#growStateModal').modal('hide');
                
                getDataList();
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function onCloneState() {  
        let formData = {
            from_crop_id: $('#from_crop_id').val(),
            from_item_id: $('#from_item_id').val(),
            to_item_id: $('#to_item_id').val(),
            to_crop_id:$('#to_crop_id').val()
        };
        console.log(formData);
        $.ajax({
            url: `/api/growstates/clone`,
            type: 'POST',
            data: formData,
            success: function (res) {
                alert(res.message);
            $('#clone-modal').modal('hide');
                // console.log(res);   
                getDataList();
            },
            error: function (xhr) {
                console.error(xhr);   
                alert('เกิดข้อผิดพลาดในการคัดลอก กรุณาลองใหม่');
            }
        });   
    }

    // OPTIONAL: Load Table Data
    function loadTable(data) {
        console.log(data);
        clearInput();
        table = $('#growStatesTable').DataTable({
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
                { data: 'input_item', title: 'input item',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    }
                },
                { data: 'code', title: 'Code' },
                { data: 'name', title: 'Name' },
                { data: 'age', title: 'age' },
                
                { data: 'crop', title: 'created',
                    render: function(data, type, row, meta) {
                        return data && data.created ? data.created : '-';
                    }
                },
                { data: 'crop', title: 'modified',
                    render: function(data, type, row, meta) {
                        return data && data.modified ? data.modified : '-';
                    }
                },
                {
                    data: 'id',
                    title: 'Action',
                    render: function (data, type, row, meta) {
                        return `
                        <form action="/units/${row.id}" method="POST" class="align-middle mt-1" onsubmit="return myFunction();">
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <a href="javascript:void(0)" class="text-decoration-none me-2" onclick="openEditModal(${row.id}, ${row.crop.id}, ${row.input_item.id}, \`${row.name}\`, \`${row.code}\`, \`${row.age}\`)">
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

    function clearInput() {
        $('#search-custom').val("");
        if (table) {
            table.search("").draw();
        }
    }

    function openEditModal(id, crop_id, input_item, name, code, age) {
        $('#actionType').val('update');
        $('#growStateModalLabel').text('Edit Grow State');
        $('#growStateForm')[0].reset();
        loadDropdowns();
        $('#state_code').val(code);
        $('#state_name').val(name);
        $('#age').val(age);
        $('#edit_id').val(id);
        $('#crop_id').val(crop_id);
        $('#input_item_id').val(input_item);

        $('#growStateModal').modal('show');
    }

    function onDelete(id) {
        if (!confirm('คุณต้องการลบข้อมูลใช่หรือไม่?')) {
            return;
        }

        $.ajax({
            url: `/api/growstates/${id}`,
            type: 'DELETE',
            success: function (res) {
                getDataList();
            },
            error: function (xhr) {
                alert('เกิดข้อผิดพลาดในการลบ กรุณาลองใหม่');
            }
        });
    }
</script>
