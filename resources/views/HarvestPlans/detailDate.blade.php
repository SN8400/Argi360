@extends('Layouts.app')
@section('title', 'Edit Farmer')

@section('content')
    <input type="hidden" id="cropId" value="{{ $cropId ?? '' }}">
    <input type="hidden" id="date" value="{{ $date ?? '' }}">
    <input type="hidden" id="type" value="{{ $type ?? '' }}">
    <input type="hidden" id="role" value="{{ \App\Helpers\RoleHelper::getGroupByRole(Auth::user()->group_id) ?? '-' }}">
    <h2>แผนเก็บเกี่ยว Crop: <span id="crop_name"></span></h2>
    <h5>วันที่ : <span id="est_date"></span> รวม (ตัน) <span id="est_value"></span> | <span id="bf_value"></span> | <span id="act_value"></span> | <span id="totalsum"></span></h5>
    <div id="con-btn"></div>
    <table class="table" id="inputitem-table">
        <thead>
            <tr>
                <th>#</th>
                <th>วันที่เก็บ / ปลูก</th>
                <th>เขต</th>
                <th>หัวหน้ากลุ่ม</th>
                <th>พันธุ์</th>
                <th>อายุ (ประเภท)</th>
                <th>ประมาณ ตัน (แปลง)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    let areaMap = [];
    let brokerMap = [];
    let inputItemMap = [];

    $(document).ready(function () {
        var cropId = $('#cropId').val();
        var date = $('#date').val();
        var type = $('#type').val();
        var role = document.getElementById('role').value;

        $.ajax({
            url: '/api/harvestPlans/detailDate/' + cropId + '/' + date+ '/' + type,
            method: 'GET',
            success: function(res) {
                const container = document.getElementById("con-btn");
                container.innerHTML = '';
                let est_value = 0;
                let bf_value = 0;
                let act_value = 0;

                areaMap = arrayToObjectById(res.areaList);
                brokerMap = arrayToObjectById(res.brokerList);
                inputItemMap = arrayToObjectById(res.inputItemList);

                res.harvestPlanData.forEach((item, index) => {
                    est_value = Number(est_value) + Number(item.est_value ?? 0);
                    bf_value = Number(bf_value) + Number(item.bf_value ?? 0);
                    act_value = Number(act_value) + Number(item.act_value ?? 0);
                });

                $('#crop_name').text(res.crop.name);
                $('#est_date').text(res.date);
                $('#est_value').text(est_value.toFixed(2));
                $('#bf_value').text(bf_value.toFixed(2));
                $('#act_value').text(act_value.toFixed(2));
                $('#totalsum').text(res.totalsum.toFixed(2));
                if(res.type == 'N'){
                    if(role == "User"){
                        container.innerHTML = `                    
                            <div class="btn-group btn-group-toggle">
                                <a href="/TmpSchedules/${cropId}/edit" class="btn btn-warning text-white m-2">Upload ประมาณการ</a>
                            </div>`;
                    }
                    else{
                        container.innerHTML = `                    
                            <div class="btn-group btn-group-toggle">
                                <a href="/TmpSchedules/${cropId}/edit" class="btn btn-warning text-white m-2">Upload ประมาณการ</a>
                                <a href="/TmpSchedules/${cropId}/clone" class="btn btn-primary text-white m-2">Sync Data</a>
                                <a href="/TmpSchedules/${cropId}/review" class="btn btn-info text-white m-2">Map Data</a>
                            </div>`;
                    }
                    
                }

                getTable(res);
            },
            error: function(xhr) {
                alert('Error fetching farmer data.');
            }
        });
    });

    function getTable(res) {
                        console.log(res);
        table = $('#inputitem-table').DataTable({
            data: res.harvestPlanData,
            dom: 'lrtip',
            destroy: true,
            info: false,
            ordering: false,
            columns: [
                { data: null, render: (data, type, row, meta) => meta.row + 1 },
                { data: null, title: 'วันที่เก็บ / ปลูก',
                    render: function (data, type, row, meta) {
                        return data.date_est + " / " + data.start_date;
                    }
                },
                { data: null, title: 'เขต',
                    render: function (data) {
                        return areaMap[data.area_id].name;
                    }
                },
                { data: null, title: 'หัวหน้ากลุ่ม',
                    render: function (data) {
                        return brokerMap[data.broker_id].code + " " + brokerMap[data.broker_id].fname + " " + brokerMap[data.broker_id].lname;
                    }
                },
                { data: null, title: 'พันธุ์',
                    render: function (data) {
                        return inputItemMap[data.input_item_id].tradename;
                    }
                },
                { data: null, title: 'อายุ (ประเภท)',
                    render: function (data) {
                        return data.age + '(' + data.mat_type +')';
                    }
                },
                { data: null, title: 'ประมาณ ตัน (แปลง)',
                    render: function (data) {
                        return Number(data.est_value ? data.est_value/1000 : 0).toFixed(2) + ' | ' + Number(data.bf_value ? data.bf_value/1000 : 0).toFixed(2) + ' | ' + Number(data.act_value ? data.act_value/1000 : 0).toFixed(2);
                    }
                },
                {
                    data: null,
                    render: (data) => `
                        <a href="/HarvestPlans/detailFarmer/${data.crop_id}/${data.date_est}/${data.area_id}/${data.broker_id}/${data.input_item_id}/${data.age}/${data.mat_type}" class="btn btn-warning btn-sm">รายละเอียด</a>
                    `
                }
            ]
        });
    }

    function arrayToObjectById(arr) {
        return arr.reduce((acc, cur) => {
            acc[cur.id] = cur;
            return acc;
        }, {});
    }
</script>
