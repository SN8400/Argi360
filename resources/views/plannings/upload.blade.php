@extends('Layouts.app')

@section('title', 'Input Items Page')

@section('content')

<input type="hidden" id="cropId" value="{{ $id ?? '' }}">
<div class="container mt-4">
    <h3>Upload Excel แผนการปลูก Crop :<span id="crop_name">(Excel)</span></h3>

    <div id="dropzone" class="border p-4 mb-3 text-center" style="border: 2px dashed #ccc; cursor: pointer;">
        <label for="excelFile" style="cursor: pointer;">
            <p id="dropzoneText">คลิกหรือลากไฟล์ Excel มาวางที่นี่</p>
        </label>
        <input type="file" id="excelFile" style="display: none;" accept=".xlsx,.xls" />
    </div>

    <div class="mb-3">
        <span id="uploadStatus" class="badge bg-secondary">ยังไม่ได้อัปโหลด</span>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('plannings', ['id' => $id ?? '']) }}" class="btn btn-secondary">Back</a>
        <button id="uploadBtn" class="btn btn-primary" disabled>Upload</button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    let initCount = 0;
$(function () {
    const dropzone = $('#dropzone');
    const fileInput = $('#excelFile');
    const uploadStatus = $('#uploadStatus');
    const uploadBtn = $('#uploadBtn');
    let selectedFile = null;


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

    uploadBtn.off('click').on('click', function () {
        if (!selectedFile) return;

        uploadStatus.removeClass().addClass('badge bg-info').text('กำลังอัปโหลด...');
        uploadBtn.prop('disabled', true);
        const cropId = $('#cropId').val();

        const formData = new FormData();
        formData.append('file', selectedFile);
        console.log(selectedFile);
        $.ajax({
            url: '/api/uploadplan/' + cropId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                alert('Upload success');
                uploadStatus.removeClass().addClass('badge bg-success').text('อัปโหลดเสร็จแล้ว');
                window.location.href = "/plannings/" + cropId;
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || 'เกิดข้อผิดพลาดในการอัปโหลด';
                alert(msg);
                uploadStatus.removeClass().addClass('badge bg-danger').text('อัปโหลดล้มเหลว');
                uploadBtn.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
