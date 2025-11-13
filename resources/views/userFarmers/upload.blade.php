@extends('Layouts.app')

@if ($type == "card")
    @section('title', 'Upload รูปภาพบัตรประชาชน')
@else
    @section('title', 'Upload ลูกสวน ใหม่')
@endif

@section('content')

<input type="hidden" id="cropId" value="{{ $cropId ?? '' }}">
<input type="hidden" id="type" value="{{ $type ?? '' }}">
<input type="hidden" id="id" value="{{ $id ?? '' }}">
<div class="container mt-4">
    @if ($type == "card")
        <h3>Upload รูปภาพบัตรประชาชน | Farmer :
    @elseif ($type == "image")
        <h3>Upload รูปภาพลูกสวน | Farmer :
    @endif
        <span id="farmer_name">(farmer_name)</span>
    </h3>
    <div id="dropzone" class="border p-4 mb-3 text-center" style="border: 2px dashed #ccc; cursor: pointer;">
        <label for="excelFile" style="cursor: pointer;">
            <p id="dropzoneText">คลิกหรือลากรูปภาพมาวางที่นี่</p>
        </label>
        <input type="file" id="excelFile" style="display: none;" accept=".jpg,.jpeg,.png,.gif" />
    </div>

    <div id="imagePreview" class="mb-3 text-center"></div>

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
    let farmerId = 0;
    const dropzone = $('#dropzone');
    const fileInput = $('#excelFile');
    const uploadStatus = $('#uploadStatus');
    const uploadBtn = $('#uploadBtn');
    let selectedFile = null;

    $(document).ready(function () {
        var cropId = $('#cropId').val();
        var id = $('#id').val();
        var type = $('#type').val();
        $.ajax({
            url: '/api/userFarmers/' + cropId +'/' + id,
            method: 'GET',
            success: function(res) { 
                console.log(res);       
                var farmer = res.data.farmer;
                if(farmer){
                    farmerId = farmer.id;
                    var init = farmer.init ?? '';
                    var fname = farmer.fname ?? '';
                    var fname = farmer.lname ?? '';
                    $('#farmer_name').text(init + ' ' + fname + ' ' + fname);
                    if(type == "card"){
                        if(farmer.farmer_card){
                            if(farmer.farmer_card.attach){
                                let  attachDir = farmer.farmer_card.attach_dir.replace(/\\/g, '/');
                                const imageUrl = `http://127.0.0.1:8000/${attachDir}/${farmer.farmer_card.attach}`;
                    
                                $('#imagePreview').html(`<img src="${imageUrl}" alt="Preview Image" style="max-width: 100%; max-height: 300px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">`);

                            }
                        }
                    }
                    else{
                        if(farmer.farmer_image){
                            if(farmer.farmer_image.attach){
                                let  attachDir = farmer.farmer_image.attach_dir.replace(/\\/g, '/');
                                const imageUrl = `http://127.0.0.1:8000/${attachDir}/${farmer.farmer_image.attach}`;
                    
                                $('#imagePreview').html(`<img src="${imageUrl}" alt="Preview Image" style="max-width: 100%; max-height: 300px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">`);

                            }
                        }
                    }
                }
                //  var init = res.data?.init ?? '';
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
            if (!file || !file.name.match(/\.(jpg|jpeg|png|gif)$/i)) {
                alert('กรุณาเลือกไฟล์ Excel เท่านั้น');
                return;
            }
            selectedFile = file;
            $('#dropzoneText').text(`เลือกไฟล์แล้ว: ${file.name}`);

            // แสดง preview image
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview').html(`<img src="${e.target.result}" alt="Preview Image" style="max-width: 100%; max-height: 300px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">`);
            };
            reader.readAsDataURL(file);

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
        formData.append('farmerId', farmerId);
        $.ajax({
            url: '/api/userFarmers/images/' + cropId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                console.log(res);
                console.log(cropId);
                alert('Upload success');
                uploadStatus.removeClass().addClass('badge bg-success').text('อัปโหลดเสร็จแล้ว');
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
