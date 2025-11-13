@extends('Layouts.app')

@section('title', 'Province Management')

@section('content')
<div class="row">
    <div class="col-md-12">
        <a href="javascript:void(0)" class="btn btn-success my-3" onclick="openProvinceModal()">Create New Province</a>
        <hr>
    </div>
</div>

<input type="text" class="form-control mb-3" id="province-search" placeholder="Search..." onkeyup="provinceTable.search(this.value).draw()">

<table class="table" id="province-table">
    <thead>
        <tr>
            <th>#</th>
            <th>TH Name</th>
            <th>EN Name</th>
            <th>Modified</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- Province Modal -->
<div class="modal fade" id="provinceModal" tabindex="-1" aria-labelledby="provinceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="provinceModalLabel">Province Form</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearProvinceModal()"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="province_id">
        <div class="mb-3">
          <label class="form-label">TH Name</label>
          <input type="text" class="form-control" id="province_th_name">
        </div>
        <div class="mb-3">
          <label class="form-label">EN Name</label>
          <input type="text" class="form-control" id="province_en_name">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="clearProvinceModal()" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary" onclick="saveProvince()">Save</button>
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
let provinceModal;
let provinceTable;

window.addEventListener('load', function () {
    provinceModal = new bootstrap.Modal(document.getElementById('provinceModal'));
    loadProvinceTable();
});

function loadProvinceTable() {
    $.get('/api/provinces', function (response) {
        provinceTable = $('#province-table').DataTable({
            data: response.data,
            destroy: true,
            dom: 'lrtip',
            columns: [
                { data: null, render: (_, __, ___, meta) => meta.row + 1 },
                { data: 'th_name' },
                { data: 'en_name' },
                { data: 'modified' },
                {
                    data: null,
                    render: row => `
                        <button class="btn btn-warning btn-sm" onclick="editProvince(${row.id}, '${row.th_name}', '${row.en_name}')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteProvince(${row.id})">Delete</button>
                    `
                }
            ]
        });
    });
}

function openProvinceModal() {
    $('#provinceModalLabel').text('Create Province');
    clearProvinceModal();
    provinceModal.show();
}

function editProvince(id, th_name, en_name) {
    $('#provinceModalLabel').text('Edit Province');
    $('#province_id').val(id);
    $('#province_th_name').val(th_name);
    $('#province_en_name').val(en_name);
    provinceModal.show();
}

function saveProvince() {
    const id = $('#province_id').val();
    const data = {
        th_name: $('#province_th_name').val(),
        en_name: $('#province_en_name').val()
    };

    const method = id ? 'PUT' : 'POST';
    const url = id ? `/api/provinces/${id}` : '/api/provinces';

    $.ajax({
        url, method, data,
        success: () => {
            provinceModal.hide();
            loadProvinceTable();
        },
        error: () => alert('Error saving province')
    });
}

function deleteProvince(id) {
    if (!confirm('Are you sure?')) return;
    $.ajax({
        url: `/api/provinces/${id}`,
        type: 'DELETE',
        success: loadProvinceTable,
        error: () => alert('Error deleting province')
    });
}

function clearProvinceModal() {
    $('#province_id').val('');
    $('#province_th_name').val('');
    $('#province_en_name').val('');
}
</script>
