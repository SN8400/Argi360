@extends(\App\Helpers\RoleHelper::getLayoutByRole(Auth::user()->group_id))


@section('content')
<style>
    .big-button {
        font-size: 1.5rem;     
        padding: 1.5rem 2rem; 
    }
    .top-right-label {
        position: absolute;
        top: .5rem;
        right: 1.5rem;
        text-align: right;
        font-size: 1rem;
        z-index: 1000;
    }
</style>

<div class="top-right-label">
    <strong>ชื่อ: {{ session('fullName') }}</strong><br>
    <strong>ตำแหน่ง: {{ session('groupName') }}</strong>
</div>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="row text-center">
        <div class="col-12 mb-4">
            <h2>หน้าจอ {{ session('groupName') }}</h2>
            <h5>ตำแหน่ง {{ \App\Helpers\RoleHelper::getGroupByRole(Auth::user()->group_id) ?? '-'  }}</h5>
            <h5>แผนก {{ \App\Helpers\RoleHelper::getDepartmentByRole(Auth::user()->group_id) ?? '-'  }}</h5>
        </div>

        @switch(\App\Helpers\RoleHelper::getGroupByRole(Auth::user()->group_id))
            @case('Admin')
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="{{ route('admin_core') }}" class="btn btn-primary w-100 big-button">Admin Core</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="{{ route('crop_list') }}" class="btn btn-success w-100 big-button">Admin Crop</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="{{ route('crop_list') }}" class="btn btn-secondary w-100 big-button">User</a>
                </div>
                <div class="col-12 col-md-6 col-lg-6 mb-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-warning w-100 big-button">Dashboard</a>
                </div>
                <div class="col-12 col-md-6 col-lg-6 mb-3">
                    <a href="{{ route('report') }}" class="btn btn-info w-100 big-button">Report</a>
                </div>
            @break
            @case('Manager')
                <div class="col-12 col-md-6 col-lg-6 mb-3">
                    <a href="{{ route('crop_list') }}" class="btn btn-success w-100 big-button">Admin Crop</a>
                </div>
                <div class="col-12 col-md-6 col-lg-6 mb-3">
                    <a href="{{ route('crop_list') }}" class="btn btn-secondary w-100 big-button">User</a>
                </div>
                <div class="col-12 col-md-6 col-lg-6 mb-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-warning w-100 big-button">Dashboard</a>
                </div>
                <div class="col-12 col-md-6 col-lg-6 mb-3">
                    <a href="{{ route('report') }}" class="btn btn-info w-100 big-button">Report</a>
            @break
            @default
                <script>
                    window.location.href = "{{ route('admin_core') }}";
                </script>
            @break
        @endswitch
{{-- 
        @switch(Auth::user()->group_id)
            @case(3)
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/Admin/cropMaster" class="btn btn-primary w-100 big-button">Crop Management</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/User" class="btn btn-primary w-100 big-button">User Management</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/InputItems" class="btn btn-primary w-100 big-button">Input Item Management</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/brokers" class="btn btn-primary w-100 big-button">Broker Management</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/standardMaster" class="btn btn-primary w-100 big-button">Standard Management</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/Chemicals" class="btn btn-primary w-100 big-button">Chemical Management</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/Admin/growstate" class="btn btn-primary w-100 big-button">GrowState Management</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/heads" class="btn btn-primary w-100 big-button">Heads Management</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/roleMaster" class="btn btn-primary w-100 big-button">Group Management</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/checkListMaster" class="btn btn-primary w-100 big-button">Check List Management</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/units" class="btn btn-primary w-100 big-button">Unit Management</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/plant_code" class="btn btn-primary w-100 big-button">Plant Code</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/TmpSchedules" class="btn btn-primary w-100 big-button">Template Plan Management</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/seedCode" class="btn btn-primary w-100 big-button">Seed Code</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/crop_list" class="btn btn-primary w-100 big-button">Crop List</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/brokerHead" class="btn btn-primary w-100 big-button">Broker Head</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/brokerArea" class="btn btn-primary w-100 big-button">Broker Area</a>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/areaMaster" class="btn btn-primary w-100 big-button">Area Management</a>
                </div>
                @break

            @default
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <button class="btn btn-primary w-100 big-button">Dashboard</button>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <button class="btn btn-primary w-100 big-button">Crop</button>
                </div>
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <button class="btn btn-primary w-100 big-button">Report</button>
                </div>
        @endswitch
        
                <div class="col-12 col-md-6 col-lg-4 mb-3">
                    <a href="/logout" class="btn btn-success w-100 big-button">Log out</a>
                </div>
    </div>
</div> --}}
@endsection
