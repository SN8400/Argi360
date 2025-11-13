@extends('Layouts.app')

@section('title', 'City Management')

@section('content')
<div class="row">
    <div class="col-md-12">
        <a href="javascript:void(0)" class="btn btn-success my-3" onclick="openCityModal()">Create New City</a>
        <hr>
    </div>
</div>

<input type="text" class="form-control mb-3" id="city-search" placeholder="Search..." onkeyup="cityTable.search(this.value).draw()">

<table class="table" id="city-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Province</th>
            <th>TH Name</th>
            <th>EN Name</th>
            <th>Modified</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<!-- City Modal -->
<div class="modal fade" id="cityModal" tabindex="-1" aria-labelledby="cityModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cityModalLabel">City Form</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearCityModal()"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="city_id">
        <div class="mb-3">
          <label class="form-label">Province</label>
          <select class="form-select" id="city_province_id"></select>
        </div>
        <div class="mb-3">
          <label class="form-label">TH Name</label>
          <input type="text" class="form-control" id="city_th_name">
        </div>
        <div class="mb-3">
          <label class="form-label">EN Name</label>
          <input type="text" class="form-control" id="city_en_name">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" onclick="clearCityModal()" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary" onclick="saveCity()">Save</button>
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
let cityModal;
let cityTable;

window.addEventListener('load', function () {
    cityModal = new bootstrap.Modal(document.getElementById('cityModal'));
    loadCityTable();
    loadProvincesDropdown();
});

function loadCityTable() {
    $.get('/api/cities', function (response) {
        cityTable = $('#city-table').DataTable({
            data: response.data,
            destroy: true,
            dom: 'lrtip',
            columns: [
                { data: null, render: (_, __, ___, meta) => meta.row + 1 },
                { data: 'province', render: data => data.th_name },
                { data: 'th_name' },
                { data: 'en_name' },
                { data: 'modified' },
                {
                    data: null,
                    render: row => `
                        <button class="btn btn-warning btn-sm" onclick="editCity(${row.id}, ${row.province_id}, '${row.th_name}', '${row.en_name}')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteCity(${row.id})">Delete</button>
                    `
                }
            ]
        });
    });
}

function loadProvincesDropdown() {
    $.get('/api/provinces', function (response) {
        let html = `<option value="">Select Province</option>`;
        response.data.forEach(p => {
            html += `<option value="${p.id}">${p.th_name}</option>`;
        });
        $('#city_province_id').html(html);
    });
}

function openCityModal() {
    $('#cityModalLabel').text('Create City');
    clearCityModal();
    cityModal.show();
}

function editCity(id, province_id, th_name, en_name) {
    $('#cityModalLabel').text('Edit City');
    $('#city_id').val(id);
    $('#city_province_id').val(province_id);
    $('#city_th_name').val(th_name);
    $('#city_en_name').val(en_name);
    cityModal.show();
}

function saveCity() {
    const id = $('#city_id').val();
    const data = {
        province_id: $('#city_province_id').val(),
        th_name: $('#city_th_name').val(),
        en_name: $('#city_en_name').val()
    };

    const method = id ? 'PUT' : 'POST';
    const url = id ? `/api/cities/${id}` : '/api/cities';

    $.ajax({
        url, method, data,
        success: () => {
            cityModal.hide();
            loadCityTable();
        },
        error: () => alert('Error saving city')
    });
}

function deleteCity(id) {
    if (!confirm('Are you sure?')) return;
    $.ajax({
        url: `/api/cities/${id}`,
        type: 'DELETE',
        success: loadCityTable,
        error: () => alert('Error deleting city')
    });
}

function clearCityModal() {
    $('#city_id').val('');
    $('#city_th_name').val('');
    $('#city_en_name').val('');
    $('#city_province_id').val('');
}
</script>
