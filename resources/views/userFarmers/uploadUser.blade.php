@extends('Layouts.app')

@if ($type == "old")
    @section('title', 'Upload ลูกสวน เก่า')
@else
    @section('title', 'Upload ลูกสวน ใหม่')
@endif

@section('content')

<input type="hidden" id="cropId" value="{{ $cropId ?? '' }}">
<input type="hidden" id="type" value="{{ $type ?? '' }}">
<div class="container mt-4">
    @if ($type == "old")
        <h3>Upload ลูกสวน เก่า | Crop :
    @else
        <h3>Upload ลูกสวน ใหม่ | Crop :
    @endif
        <span id="crop_name">(crop_name)</span>
    </h3>
    <div id="dropzone" class="border p-4 mb-3 text-center" style="border: 2px dashed #ccc; cursor: pointer;">
        <label for="excelFile" style="cursor: pointer;">
            <p id="dropzoneText">คลิกหรือลากไฟล์ Excel มาวางที่นี่</p>
        </label>
        <input type="file" id="excelFile" style="display: none;" accept=".xlsx,.xls" />
    </div>

    <div class="mb-3">
        <span id="uploadStatus" class="badge bg-secondary">ยังไม่ได้อัปโหลด</span>
    </div>

    <div class="row align-items-center">
        <div class="col-md-2">
            <a href="{{ route('userFarmers', ['cropId' => $cropId]) }}" class="btn btn-danger w-100">Back</a>
        </div>
        <div class="col-md-2 ms-auto">
            <button type="button" class="btn btn-success w-100" onclick="onCreateUserFarmer()">Save</button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    let initCount = 0;
    let selectedFile = null;
    const dropzone = $('#dropzone');
    const fileInput = $('#excelFile');
    const uploadStatus = $('#uploadStatus');
    const uploadBtn = $('#uploadBtn');

    $(document).ready(function () {
        var cropId = $('#cropId').val();
        $.ajax({
            url: '/api/crops/' + cropId ,
            method: 'GET',
            success: function(res) {          
                $('#crop_name').text(res.data.name);
            },
            error: function(xhr) {
                alert('Error fetching user farmer data.');
            }
        });

        dropzone.off('dragover').on('dragover', function (e) {
            e.preventDefault();
            dropzone.css('background-color', '#f0f8ff');
        });

        dropzone.off('dragleave').on('dragleave', function (e) {
            e.preventDefault();
            dropzone.css('background-color', '');
        });

        dropzone.off('drop').on('drop', function (e) {
            e.preventDefault();
            dropzone.css('background-color', '');
            const files = e.originalEvent.dataTransfer.files;
            handleFile(files[0]);
        });

        fileInput.off('change').on('change', function (e) {
            handleFile(e.target.files[0]);
        });

        function handleFile(file) {
            if (!file || !file.name.match(/\.(xlsx|xls)$/)) {
                alert('กรุณาเลือกไฟล์ Excel เท่านั้น');
                return;
            }
            selectedFile = file;
            $('#dropzoneText').text(`เลือกไฟล์แล้ว: ${file.name}`);
            uploadStatus.removeClass().addClass('badge bg-warning').text('พร้อมอัปโหลด');
            uploadBtn.prop('disabled', false);
        }
    });

    function onCreateUserFarmer() {
        if (!selectedFile) return;

        uploadStatus.removeClass().addClass('badge bg-info').text('กำลังอัปโหลด...');
        uploadBtn.prop('disabled', true);
        const cropId = $('#cropId').val();
        const type = $('#type').val();

        const formData = new FormData();
        formData.append('file', selectedFile);
        formData.append('type', type);
        $.ajax({
            url: '/api/userFarmers/import/' + cropId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                // console.log(res);
                alert('Upload success');
                // uploadStatus.removeClass().addClass('badge bg-success').text('อัปโหลดเสร็จแล้ว');
                window.location.href = "/userFarmers/" + cropId;
            },
            error: function (xhr) {
                console.error(xhr);
                const msg = xhr.responseJSON?.message || 'เกิดข้อผิดพลาดในการอัปโหลด';
                alert(msg);
                uploadStatus.removeClass().addClass('badge bg-danger').text('อัปโหลดล้มเหลว');
                uploadBtn.prop('disabled', false);
            }
        });
    }
</script>
@endpush
