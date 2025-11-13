@extends('Layouts.app')
@section('title', 'User Farmers Page')
@section('content')
    <input type="hidden" id="cropId" value="{{ $cropId ?? '' }}">
    <div class="row">
        <div class="col-md-12">
            <a href="{{ route('userFarmers.create', ['cropId' => $cropId]) }}" class="btn btn-success my-3">เพิ่มลูกสวนเก่า</a>
            <a href="{{ route('userFarmers.new', ['cropId' => $cropId]) }}" class="btn btn-primary my-3">เพิ่มลูกสวนใหม่</a>
            <a href="{{ route('userFarmers.uploadUser', ['cropId' => $cropId, 'type' => 'old']) }}" class="btn btn-info my-3">Upload ลูกสวนเก่า</a>
            <a href="{{ route('userFarmers.uploadUser', ['cropId' => $cropId, 'type' => 'new']) }}" class="btn btn-secondary my-3">Upload ลูกสวนใหม่</a>
            <a href="{{ route('downloadFarmerReport', ['crop_id' => $cropId]) }}" class="btn btn-warning my-3">Download Farmer</a>
            <a href="{{ route('userFarmers.print', ['cropId' => $cropId]) }}" class="btn btn-dark my-3">Print รูป</a>
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
    <table class="table" id="farmer-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Broker</th>
                <th>หัวจุด</th>
                <th>ลูกสวน</th>
                <th>พนักงาน</th>
                <th>หัวหน้า</th>
                <th>ตรวจพ่นยา</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    let table;
    window.addEventListener('load', function () {
        getData();
    });

    function getData(){
        var cropId = $('#cropId').val();
        console.log(cropId);

        $.ajax({
            url: '/api/userFarmers/' + cropId,
            method: 'GET',
            success: function(res) {
                console.log(res);
                getTable(res.data);
            },
            error: function(xhr) {
                alert('Error fetching user farmer data.');
            }
        });
    }

    function clearInput() {
        $('#search-custom').val("");
        if (table) table.search("").draw();
    }

    function searchByText(params) {
        if (table) table.search(params.value).draw();
    }

    function getTable(data) {
        console.log(data);
        var cropId = $('#cropId').val();
        table = $('#farmer-table').DataTable({
            data: data,
            dom: 'lrtip',
            destroy: true,
            info: false,
            ordering: false,
            pageLength: 50,
            columns: [
                { data: null, render: (data, type, row, meta) => meta.row + 1 },
                { 
                    data: null,
                    title: 'Broker',
                    render: function (data, type, full) {
                        return `${data?.broker?.init ?? ''} ${data?.broker?.fname ?? ''} ${data?.broker?.lname ?? ''} (${data.area?.name ?? ''})`;
                    }
                },
                { 
                    data: 'head',
                    title: 'หัวจุด',
                    render: function (data, type, full) {
                        var dataId = `${full.id}`;
                        return `${data?.init ?? ''} ${data?.fname ?? ''} ${data?.lname ?? ''}` +
                        `<a href="/userFarmers/${cropId}/head/${full.id}/change" class="btn btn-warning btn-sm m-2">Edit</a>`;
                    }
                },
                { 
                    data: 'farmer',
                    title: 'ลูกสวน',
                    render: function (data, type, full) {
                        return `${data?.init ?? ''} ${data?.fname ?? ''} ${data?.lname ?? ''}` +
                        `<a href="/userFarmers/${cropId}/farmer/${full.id}/change" class="btn btn-warning btn-sm m-2">แก้ไข</a>` +
                        `<a href="/userFarmers/${cropId}/sowing/${full.id}/change" class="btn btn-info btn-sm m-2">${full.sowing_city}</a>` +
                        `<a href="/userFarmers/${cropId}/card/${full.id}/upload" class="btn btn-info btn-sm m-2">บัตร</a>` +
                        `<a href="/userFarmers/${cropId}/image/${full.id}/upload" class="btn btn-info btn-sm m-2">รูปภาพ</a>` +
                        `<a href="javascript:void(0);" onclick="updateStatus('${full.status}', ${full.id}, ${cropId})" class="btn btn-secondary btn-sm m-2">${full.status}</a>`;
                    }
                },
                { 
                    data: 'user',
                    title: 'พนักงาน',
                    render: function (data, type, full) {
                        return `${data?.init ?? ''} ${data?.fname ?? ''} ${data?.lname ?? ''}` +
                        `<a href="/userFarmers/${cropId}/user/${full.id}/change" class="btn btn-warning btn-sm m-2">Edit</a>`;
                    }
                },
                { 
                    data: 'manager',
                    title: 'หัวหน้า',
                    render: function (data, type, full) {
                        return `${data?.init ?? ''} ${data?.fname ?? ''} ${data?.lname ?? ''}` +
                        `<a href="/userFarmers/${cropId}/manager/${full.id}/change" class="btn btn-warning btn-sm m-2">Edit</a>`;
                    }
                },
                { 
                    data: 'review',
                    title: 'ตรวจพ่นยา',
                    render: function (data, type, full) {
                        return `${data?.init ?? ''} ${data?.fname ?? ''} ${data?.lname ?? ''}` +
                        `<a href="/userFarmers/${cropId}/review/${full.id}/change" class="btn btn-warning btn-sm m-2">Edit</a>`;
                    }
                },
                {
                    data: 'id',
                    render: (data) => `
                        <a href="/userFarmers/${cropId}/${data}/edit" class="btn btn-warning btn-sm m-2">Edit</a>
                        <button class="btn btn-danger btn-sm m-2" onclick="onDelete(${data})">Delete</button>
                    `
                }
            ]
        });
    }

    function onDelete(id) {
        if (!confirm('คุณต้องการลบข้อมูลใช่หรือไม่?')) return;

        $.ajax({
            url: `/api/userFarmers/${id}`,
            type: 'DELETE',
            success: function () {
                getData(); // reload table
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการลบ');
            }
        });
    }

    function updateStatus(statusName, id, cropId){
        var sName = 'ใหม่';
        if(statusName == 'ใหม่'){
            sName = 'เก่า';
        }
        
        const formData = {
            statusName: sName,
            type: 'statusName',
        }

        $.ajax({
            url: '/api/userFarmers/updateByType/' + cropId + '/' + id,
            method: 'PUT', 
            data: formData,
            success: function (res) {
                getData();
            },
            error: function (res) {
                // console.error(res);
                if (res.status === 422) {
                    const errors = res.responseJSON.errors;
                    for (let field in errors) {
                        $('[name="' + field + '"]').addClass('is-invalid');
                        $('#error-' + field).html(errors[field][0]);
                    }
                } else {
                    alert('❌ เกิดข้อผิดพลาดในการอัปเดตข้อมูล');
                }
            }
        });
    }
</script>
