@extends('layouts.navTop')
@push('styles')
    <style>
        a.btn-crop {
            background-color: #B5F8FB;
            border-radius: 10px;
            color: black;
            padding: 14px 25px;
            font-size: 20px;
            text-align: center;
            text-decoration: none;
            display: block;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        a.btn-crop:hover {
            background-color: #dddddd;
            color: black;
        }
    </style>
@endpush

@section('topic')
    Crops :
@endsection

@section('content')
    <div class='row' id="crop-container" style="margin-top:80px; margin-left:20px; margin-right:20px; "></div>
    <input type="hidden" id="dep" value="{{ \App\Helpers\RoleHelper::getDepartmentByRole(Auth::user()->group_id) ?? '-' }}">
    <input type="hidden" id="role" value="{{ \App\Helpers\RoleHelper::getGroupByRole(Auth::user()->group_id) ?? '-' }}">
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('crop-container');
        if (!container) {
            console.error('Element #crop-container not found');
            return;
        }

        $.ajax({
            url: '/api/crops',
            method: 'GET',
            success: function (response) {
                var role = document.getElementById('role').value;
                var dep = document.getElementById('dep').value;
                response.data.forEach(crop => {
                    const div = document.createElement('div');
                    div.className = 'col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 p-2 d-flex justify-content-center';

                    const a = document.createElement('a');
                    if(role == "User"){
                        a.href = '/' + dep + '/operation/' + crop.id;
                    }
                    else{
                        a.href = '/' + dep + '/admin_crop/' + crop.id;
                    }
                    a.className = 'btn-crop btn btn-success';
                    a.style.width = '150px'
                    a.style.backgroundColor = '#C8E4F4'
                    a.style.borderColor = '#C8E4F4'
                    a.style.color = '#000000'

                    const icon = document.createElement('i');
                    icon.className = 'bx bxs-leaf me-2';

                    a.appendChild(icon);
                    a.appendChild(document.createTextNode(crop.name));

                    div.appendChild(a);
                    container.appendChild(div);
                });
            },
            error: function (xhr) {
                console.error('Error:', xhr.responseText);
            }
        });
    });
</script>
