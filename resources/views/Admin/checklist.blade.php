@extends('Layouts.app')

@section('title', 'Checklist Crops Management')

@section('content')
<div class="row">
    <div class="col-md-12">
            <button class="btn btn-success mb-3" id="btnCreate" onclick="openCreateModal()">Create New</button>
            <button class="btn btn-primary mb-3" id="btnClone" onclick="openCloneModal()">Clone</button>
        <hr>
    </div>
</div>

<div class="row my-3">
    <div class="col-md-12">
        <form class="row g-2 align-items-center">
            <div class="col-12 col-sm-10 order-sm-1">
                <input type="text" class="form-control" id="search-custom" onkeyup="searchByText(this)" placeholder="Search...">
            </div>
            <div class="col-12 col-sm-2 order-sm-2">
                <button type="button" class="btn btn-danger w-100" onclick="clearInput();">Clear</button>
            </div>
        </form>
    </div>
</div>

<table class="table" id="checklistcrops-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Crop</th>
            <th>Checklist</th>
            <th>Counds</th>
            <th>Unit</th>
            <th>Field Map Result</th>
            <th>Field Map Val</th>
            <th>Description</th>
            <th>Created</th>
            <th>Modified</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="checklistCropModal" tabindex="-1" aria-labelledby="checklistCropModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checklistCropModalLabel">Checklist Crop Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="clearModal()"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="checklist_crop_id">

                <div class="mb-2">
                    <label class="form-label">Crop</label>
                    <select class="form-select" id="crop_id"></select>
                </div>

                <div class="mb-2">
                    <label class="form-label">Checklist</label>
                    <select class="form-select" id="checklist_id"></select>
                </div>

                <div class="mb-2">
                    <label class="form-label">Counds</label>
                    <input type="number" class="form-control" id="counds">
                </div>

                <div class="mb-2">
                    <label class="form-label">Unit</label>
                    <select class="form-select" id="unit_id"></select>
                </div>

                <div class="mb-2">
                    <label class="form-label">Field Map Result</label>
                    <input type="text" class="form-control" id="field_map_result">
                </div>

                <div class="mb-2">
                    <label class="form-label">Field Map Val</label>
                    <input type="text" class="form-control" id="field_map_val">
                </div>

                <div class="mb-2">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" id="desc"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="clearModal()" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveChecklistCrop()">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="cloneCropModal" tabindex="-1" aria-labelledby="cloneCropModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cloneCropModalLabel">Clone Checklist Crop Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="clearModal()"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="clone_crop_id">

                <div class="mb-2">
                    <label class="form-label">From Crop</label>
                    <select class="form-select" id="from_crop_id"></select>
                </div>

                <div class="mb-2">
                    <label class="form-label">To Crop</label>
                    <select class="form-select" id="to_crop_id"></select>
                </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="clearModal()" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveClonelistCrop()">Save</button>
            </div>
        </div>
    </div>
</div>



@endsection

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- Include JS & DataTables -->
<script>
    let checklistCropModal;
    let cloneCropModal;
    let table;

    const crops = {};
    const checklists = {};
    const units = {};

    window.addEventListener('load', async () => {
        cloneCropModal = new bootstrap.Modal(document.getElementById('cloneCropModal'), {
            backdrop: 'static',
            keyboard: false
        });

        checklistCropModal = new bootstrap.Modal(document.getElementById('checklistCropModal'), {
            backdrop: 'static',
            keyboard: false
        });

        await loadDropdowns();
        loadTable();
    });

    async function loadDropdowns() {
        const [cropRes, checklistRes, unitRes] = await Promise.all([
            $.get('/api/crops'),
            $.get('/api/checklist'),
            $.get('/api/units')
        ]);

        fillSelect('#crop_id, #from_crop_id, #to_crop_id', cropRes.data, crops);
        fillSelect('#checklist_id', checklistRes.data, checklists);
        fillSelect('#unit_id', unitRes.data, units);
    }

    function fillSelect(selector, data, map) {
        const select = $(selector);
        select.empty();
        select.append(`<option value="">-- Select --</option>`);
        data.forEach(item => {
            map[item.id] = item.name;
            select.append(`<option value="${item.id}">${item.name}</option>`);
        });
    }

    function openCreateModal() {
        $('#checklistCropModalLabel').text('Create Checklist Crop');
        clearModal();
        checklistCropModal.show();
    }
    function openCloneModal() {
        $('#checklistCropModalLabel').text('Create Checklist Crop');
        clearModal();
        cloneCropModal.show();
    }

    function openEditModal(item) {
        $('#checklistCropModalLabel').text('Edit Checklist Crop');
        $('#checklist_crop_id').val(item.id);
        $('#crop_id').val(item.crop_id);
        $('#checklist_id').val(item.checklist_id);
        $('#counds').val(item.conds);
        $('#unit_id').val(item.unit);
        $('#field_map_result').val(item.field_map_result);
        $('#field_map_val').val(item.field_map_val);
        $('#desc').val(item.desc);
        checklistCropModal.show();
    }

    function clearModal() {
        $('#checklist_crop_id, #crop_id, #checklist_id, #counds, #unit_id, #field_map_result, #field_map_val, #desc').val('');
        clearInput();
    }

    function saveChecklistCrop() {
        const id = $('#checklist_crop_id').val();
        const data = {
            crop_id: $('#crop_id').val(),
            checklist_id: $('#checklist_id').val(),
            conds: $('#counds').val(),
            unit: $('#unit_id').val(),
            field_map_result: $('#field_map_result').val(),
            field_map_val: $('#field_map_val').val(),
            desc: $('#desc').val()
        };

        const method = id ? 'POST' : 'POST';
        const url = id ? `/api/checklistcrop/edit/${id}` : `/api/checklistcrop`;

        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function (res) {
                checklistCropModal.hide();
                clearModal();
                loadTable();
                clearInput();
            },
            error: function () {
                alert("Error saving data");
            }
        });
    }

    function onDelete(id) {
        if (!confirm('คุณต้องการลบข้อมูลใช่หรือไม่?')) return;

        $.ajax({
            url: `/api/checklistcrop/${id}`,
            type: 'DELETE',
            success: function () {
                clearInput();
                clearModal();
                loadTable();
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการลบ กรุณาลองใหม่');
            }
        });
    }

    function loadTable() {
        $.get('/api/checklistcrop', function(response) {
            table = $('#checklistcrops-table').DataTable({
                data: response.data,
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
                    { data: 'checklist', title: 'Checklist',
                        render: function(data, type, row, meta) {
                            return data && data.name ? data.name : '-';
                        }
                    },
                    { data: 'conds' },
                    { data: 'unit' },
                    { data: 'field_map_result' },
                    { data: 'field_map_val' },
                    { data: 'desc' },
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
        });
    }

    function clearInput() {
        $('#search-custom').val("");
        if (table) table.search("").draw();
    }

    function searchByText(el) {
        if (table) table.search(el.value).draw();
    }

    function saveClonelistCrop() {  
        let formData = {
            from_crop_id: $('#from_crop_id').val(),
            to_crop_id: $('#to_crop_id').val(),
        };
        $.ajax({
            url: `/api/checklistcrop/clone`,
            type: 'POST',
            data: formData,
            success: function (res) {
                alert(res.message);
                $('#cloneCropModal').modal('hide');
                loadTable();
                clearInput();
                clearModal();
            },
            error: function (xhr) {
                console.error(xhr);   
                alert('เกิดข้อผิดพลาดในการคัดลอก กรุณาลองใหม่');
            }
        });   
    }
</script>
