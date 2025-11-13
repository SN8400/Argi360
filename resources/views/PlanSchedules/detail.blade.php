@extends('Layouts.app')

@section('title', 'Plan Schedules')

@section('content')
    <h2>แผนปฏิบัติงานแยกตามเขต ประจำ Crop : <span id="crop_name"></span></h2>
    <div class="row my-3">
        <div class="col-md-12">
            <form action="#" method="get" class="row g-2 align-items-center" id="buttonForm"></form>
        </div>
    </div>
    <input type="hidden" id="crop_id" name="crop_id" value="{{ $id ?? '' }}">
    <input type="hidden" id="schedule_id" name="schedule_id" value="{{ $schedule_id ?? '' }}">

    <div class="d-flex justify-content-between mb-3">
        <a href="{{ route('Schedules', ['id' => $id ?? '']) }}" class="btn btn-secondary">ย้อนกลับ</a>
        <button type="button" class="btn btn-primary" onclick="openCreateModal()" >เพิ่มสารเคมีใหม่</button>
    </div>
    <table class="table" id="plan-schedule-table">
        <thead>
            <tr>
                <th>#</th>
                <th>สารเคมี</th>
                <th>ปริมาณ</th>
                <th>Default</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    
<!-- Broker Head Modal -->
<div class="modal fade" id="chemicalModal" tabindex="-1" aria-labelledby="chemicalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chemicalModalLabel">เพิ่มสารเคมีใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="clearModal()"></button>
            </div>
            <div class="modal-body">

                <!-- Chemical Dropdown -->
                <div class="mb-3">
                    <label for="chemical_id" class="form-label">สารเคมี หรือ ปุ๋ย</label>
                    <select class="form-select" id="chemical_id">
                        <option value="">-- เลือก --</option>
                    </select>
                </div>

                <!-- Value Dropdown -->
                <div class="mb-3">
                    <label for="chemical_value" class="form-label">อัตราต่อถัง</label>
                    <input type="text" class="form-control" id="chemical_value" name="chemical_value">
                </div>

                <!-- Unit Dropdown -->
                <div class="mb-3">
                    <label for="unit" class="form-label">หน่วย</label>
                    <select class="form-select" id="unit">
                        <option value="">-- เลือก --</option>
                    </select>
                </div>

                <!-- Rate Dropdown -->
                <div class="mb-3">
                    <label for="rate" class="form-label">Rate</label>
                    <select class="form-select" id="rate">
                        <option value="">-- เลือก --</option>
                        <option value="1">ปรกติ</option>
                        <option value="2">บังคับ</option>
                    </select>
                </div>

                <!-- Type Dropdown -->
                <div class="mb-3">
                    <label for="typeC" class="form-label">แบบ</label>
                    <select class="form-select" id="typeC">
                        <option value="">-- เลือก --</option>
                        <option value="M">สารเคมีหลัก</option>
                        <option value="O">สารเคมีเสริม</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="clearModal()" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveUnit()">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    let table;
    let chemicalModal;
    $(document).ready(function() {
        chemicalModal = new bootstrap.Modal(document.getElementById('chemicalModal'), {
            backdrop: 'static',
            keyboard: false
        });
        let id = $('#crop_id').val();
        let schedule_id = $('#schedule_id').val();
        if(id){
            $.ajax({
                url: '/api/planSchedules/' + id + '/' + schedule_id,
                type: 'GET',
                success: function(res) {
                    console.log(res);
                    getTable(res.data);
                },
                error: function() {
                    alert("เกิดข้อผิดพลาดในการโหลดข้อมูล");
                }
            });
        }

        $.ajax({
            url: '/api/chemicals',
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                $('#chemical_id').empty().append(`<option value="">-- เลือก --</option>`);
                $.each(data.data, function(i, item) {
                    $('#chemical_id').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });

        $.ajax({
            url: '/api/units',
            type: 'GET',
            dataType: 'json',
            async: false, // บังคับ synchronous
            success: function(data) {
                $('#unit').empty().append(`<option value="">-- เลือก --</option>`);
                $.each(data.data, function(i, item) {
                    $('#unit').append(`<option value="${item.id}">${item.name}</option>`);
                });
            }
        });
    });

    function openCreateModal() {
        $('#unit').val('');
        $('#rate').val('');
        $('#typeC').val('');
        $('#chemical_value').val('');
        $('#chemical_id').val('');
        chemicalModal.show();
    }

    function clearModal() {
        $('#unit').val('');
        $('#rate').val('');
        $('#typeC').val('');
        $('#chemical_value').val('');
        $('#chemical_id').val('');
    }

    function saveUnit() {
        const crop_id = $('#crop_id').val();
        const plan_schedule_id = $('#schedule_id').val();
        const chemical_id = $('#chemical_id').val();
        const value = $('#chemical_value').val();
        const unit_id = $('#unit').val();
        const rate = $('#rate').val();
        const ctype = $('#typeC').val();

        if (chemical_id.trim() === '') {
            alert('Please enter a unit name.');
            return;
        }

        $.ajax({
            url: '/api/planSchedules',
            method: 'POST',
            data: { 
                crop_id, 
                plan_schedule_id, 
                chemical_id, 
                value, 
                unit_id, 
                rate, 
                ctype 
            },
            success: function(res) {
                chemicalModal.hide();
                clearModal();
                    $.ajax({
                        url: '/api/planSchedules/' + crop_id + '/' + plan_schedule_id,
                        type: 'GET',
                        success: function(res) {
                            getTable(res.data);
                        },
                        error: function() {
                            alert("เกิดข้อผิดพลาดในการโหลดข้อมูล");
                        }
                    });
            },
            error: function(xhr) {
                alert('Error saving data.');
            }
        });
    }

    function getTable(data) {
        table = $('#plan-schedule-table').DataTable({
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
                { data: 'chemical', title: 'สารเคมี',
                    render: function(data, type, row, meta) {
                        return data && data.name ? data.name : '-';
                    }
                },
                { data: null, title: 'ปริมาณ',
                    render: function (data, type, row, meta) {
                        return row.value + row.unit?.name;
                    }
                },
                { data: 'ctype', title: 'Default',
                    render: function (data, type, row, meta) {
                        return data == "M" ? 'Y' : 'N';
                    }
                },
                {
                    data: 'id',
                    title: 'Action',
                    render: function (data, type, row, meta) {
                        return `
                            <div class="btn-group btn-group-toggle">
                                <button type="button" class="btn btn-danger text-white m-2" title="Delete" onclick="onDelete(${row.id});">
                                    <i class='bx bx-trash'>Remove</i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });
    }


    function onDelete(id) {
        if (!confirm('คุณต้องการลบข้อมูล Head นี้หรือไม่?')) return;

        let crop_id = $('#crop_id').val();
        let schedule_id = $('#schedule_id').val();
        $.ajax({
            url: `/api/planSchedules/${id}`,
            type: 'DELETE',
            success: function (res) {
                $.ajax({
                    url: '/api/planSchedules/' + crop_id + '/' + schedule_id,
                    type: 'GET',
                    success: function(res) {
                        getTable(res.data);
                    },
                    error: function() {
                        alert("เกิดข้อผิดพลาดในการโหลดข้อมูล");
                    }
                });
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการลบข้อมูล');
            }
        });
    }
</script>
