@extends('layouts.app')

@section('topic')
    Farmer Card
@endsection

@section('content')
    <div class="row align-items-center mb-4">
        <div class="col-md-2">
            <a href="{{ route('userFarmers', ['cropId' => $cropId ?? 0]) }}" class="btn btn-danger w-100">Back</a>
        </div>
        <div class="col-md-2 ms-auto">
            
            {{-- <a href="javascript:window.print()" class="btn btn-primary w-100">Print</a> --}}
            <button type="button" class="btn btn-primary w-100" onclick="onPrintFarmerCard()">Print</button>
        </div>
    </div>

    <div id="farmerCardList" class="row">
        <!-- บัตรเกษตรกรแต่ละรายการจะแสดงที่นี่ -->
    </div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
<style>
    .card-thumbnail {
        max-width: 200px;
        max-height: 120px;
        object-fit: contain;
        border-radius: 8px;
    }
</style>
@endpush

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
    $(document).ready(function () {
        fetchFarmerCards();
    });

    function onPrintFarmerCard(){
        const content = document.getElementById("farmerCardList").innerHTML;
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>พิมพ์</title>');
        // printWindow.document.write('<link rel="stylesheet" href="your-style.css">'); // ถ้ามี CSS
        printWindow.document.write('</head><body>');
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }

    function fetchFarmerCards() {
        $.get(`/api/farmers`, function (res) {
            console.log(res);
            if (res.status === 'success') {
                renderFarmerCards(res.data);
            } else {
                $('#farmerCardList').html(`<div class="text-danger p-3">No card images found.</div>`);
            }
        }).fail(function () {
            $('#farmerCardList').html(`<div class="text-danger p-3">Failed to load card images.</div>`);
        });
    }

    function renderFarmerCards(cards) {
        if (!cards.length) {
            $('#farmerCardList').html(`<div class="text-muted p-3">No farmer cards available.</div>`);
            return;
        }

        let html = '';
        cards.forEach(card => {
            if(card?.farmer_card?.attach_dir){
                let fName = card?.init + " " + card?.fname + " " + card?.lname
                let  attachDir = card.farmer_card.attach_dir.replace(/\\/g, '/');
                const imageUrl = `http://110.164.162.67:8080/spring2025/attachs/farmercard/${attachDir}/${card.attach}`;
                console.log(imageUrl);
                html += `
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                        <div class="card h-100 text-center shadow-sm border-0">
                            <div style="width: 100%; height: 320px; overflow: hidden;">
                                <img src="${imageUrl}" alt="Farmer Card"
                                    class="img-fluid object-fit-cover rounded-top"
                                    onclick="previewImage('${imageUrl}')" style="cursor: pointer;">
                            </div>
                            <div class="card-body p-2">
                                <h6 class="card-title mb-0 text-truncate">${fName}</h6>
                            </div>
                        </div>
                    </div>
                `;
            }
        });

        $('#farmerCardList').html(html);
    }

    function previewImage(imageUrl) {
        const win = window.open("", "_blank");
        win.document.write(`<title>Preview</title><img src="${imageUrl}" style="width:100%;height:auto;">`);
    }

    function onUpdateFarmerCard() {
        alert("Update functionality to be implemented.");
    }
</script>
@endpush
