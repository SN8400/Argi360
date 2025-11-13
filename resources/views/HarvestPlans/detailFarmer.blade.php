@extends('Layouts.app')
@section('title', 'Edit Farmer')

@section('content')
    <input type="hidden" id="cropId" value="{{ $cropId ?? '' }}">
    <input type="hidden" id="date" value="{{ $date ?? '' }}">
    <input type="hidden" id="areaId" value="{{ $areaId ?? '' }}">
    <input type="hidden" id="brokerId" value="{{ $brokerId ?? '' }}">
    <input type="hidden" id="itemInputId" value="{{ $itemInputId ?? '' }}">
    <input type="hidden" id="age" value="{{ $age ?? '' }}">
    <input type="hidden" id="matType" value="{{ $type ?? '' }}">
    <input type="hidden" id="role" value="{{ \App\Helpers\RoleHelper::getGroupByRole(Auth::user()->group_id) ?? '-' }}">
    <h2>แผนเก็บเกี่ยว Crop: <span id="crop_name"></span></h2>
    <h5>วันที่ : <span id="est_date"></span> | หัวหน้ากลุ่ม: <span id="area_name"></span>/<span id="broker_name"></h5>
    <h5>รวม (ตัน) <span id="est_value"></span> | <span id="bf_value"></span> | <span id="act_value"></span> | <span id="totalsum"></span></h5>
    <div id="con-btn"></div>
    <table class="table" id="inputitem-table">
        <thead>
            <tr>
                <th>วันที่</th>
                <th>แปลง</th>
                <th>ลูกสวน (ส่งเสริม/หัวหน้า)</th>
                <th>พันธุ์</th>
                <th>อายุ (ประเภท) | QA | จุดคัด</th>
                <th>ประมาณ (ตัน)</th>
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
        var areaId = $('#areaId').val();
        var brokerId = $('#brokerId').val();
        var itemInputId = $('#itemInputId').val();
        var age = $('#age').val();
        var matType = $('#matType').val();
        var role = document.getElementById('role').value;

        $.ajax({
            url: '/api/harvestPlans/detailFarmer/' + cropId + '/' + date+ '/' + areaId+ '/' + brokerId+ '/' + itemInputId+ '/' + age+ '/' + matType,
            method: 'GET',
            success: function(res) {
                console.log(res);
                let value_est = 0;
                let value_bf_harvest = 0;
                let value_act = 0;


                res.harvestPlanData.forEach((item, index) => {
                    value_est = Number(value_est) + Number(item.value_est / 1000 ?? 0);
                    value_bf_harvest = Number(value_bf_harvest) + Number(item.value_bf_harvest / 1000 ?? 0);
                    value_act = Number(value_act) + Number(item.act_value / 1000 ?? 0);
                });

                $('#crop_name').text(res.crop.name);
                $('#est_date').text(res.date);
                $('#area_name').text(res.areaList[res.harvestPlanData[0].area_id]);
                const broker = findByField(res.brokerList, 'id', res.harvestPlanData[0].broker_id);

                $('#broker_name').text(broker.fname + " " + broker.lname);
                $('#est_value').text(value_est.toFixed(2));
                $('#bf_value').text(value_bf_harvest.toFixed(2));
                $('#act_value').text(value_act.toFixed(2));

                getTable(res);
            },
            error: function(xhr) {
                alert('Error fetching farmer data.');
            }
        });
    });

    function getTable(res) {
        console.log(res.harvestPlanData);
        table = $('#inputitem-table').DataTable({
            data: res.harvestPlanData,
            dom: 'lrtip',
            destroy: true,
            info: false,
            ordering: false,
            columns: [
                { data: null, title: 'วันที่เก็บ / ปลูก',
                    render: function (data, type, row, meta) {
                        return data.date_est;
                    }
                },
                { data: null, title: 'แปลง',
                    render: function (data) {
                        if(res.listMove[data.id]){
                            if(res.canEdit == true){
                                return data.sowing?.name + ' ปลูก ' + data.sowing?.start_date + ' (' + data.sowing?.land_status + '/' + data.sowing?.harvest_status + ')' +
                                '<br/>[อยู่ระหว่างรอย้ายหรือแยก][ยกเลิก]';
                            }
                            else{
                                return data.sowing?.name + ' ปลูก ' + data.sowing?.start_date + ' (' + data.sowing?.land_status + '/' + data.sowing?.harvest_status + ')' + '<br/>[อยู่ระหว่างรอย้ายหรือแยก]';
                            }
                        }
                        else{
                            return data.sowing?.name + ' ปลูก ' + data.sowing?.start_date + ' (' + data.sowing?.land_status + '/' + data.sowing?.harvest_status + ')';
                        }
                     
                    }
                },
                { data: null, title: 'ลูกสวน (ส่งเสริม/หัวหน้า)',
                    render: function (data) {                        
                        if(res.mapMatWithHarvestPlan[data.id]){           
                            return data.farmer.fname + " " + data.farmer.lname + 
                            " (" + data.farmer.fname + " " + data.farmer.lname + 
                            " / " + data.farmer.fname + " " + data.farmer.lname + 
                            ")<br/>GSB-" + res.crop.sap_code + "-" + data.run_no + 
                            "<br/>[" + res.mapMatWithHarvestPlan[data.id] + "]";
                        }
                        else{                      
                            return data.farmer.fname + " " + data.farmer.lname + 
                            " (" + data.farmer.fname + " " + data.farmer.lname + 
                            " / " + data.farmer.fname + " " + data.farmer.lname + 
                            ")<br/>GSB-" + res.crop.sap_code + "-" + data.run_no;
                        }                    
                    }
                },
                { data: null, title: 'พันธุ์',
                    render: function (data) {
                        let item = findByField(res.inputItemList, 'id', data.input_item_id);
                        return item.tradename;
                    }
                },
                { data: null, title: 'อายุ (ประเภท) | QA | จุดคัด',
                    render: function (data) {
                        return data.age + 
                        '(' + data.mat_type +') - (' + 
                        res.harvestTypeList[String(data.havest_by).trim().toUpperCase()] +')';
                    }
                },
                { data: null, title: 'ประมาณ ตัน (แปลง)',
                    render: function (data) {
                        let data_str = Number(data.value_est ? data.value_est/1000 : 0).toFixed(2) + ' | ' + 
                        Number(data.value_bf_harvest ? data.value_bf_harvest/1000 : 0).toFixed(2) + ' | ' + 
                        Number(data.value_act ? data.value_act/1000 : 0).toFixed(2);

                        if(res.dataWeight[data.id]){  
                            data_str += "น้ำหนักเข้าจริง " + Number(res.dataWeight[data.id] ? Number(res.dataWeight[data.id])/1000 : 0).toFixed(2) + " ตัน<br/>";
                        }
                        if(res.dataQc[data.id]){  
                            data_str += "ของเสียรวม(%) " + Number(res.dataQc[data.id] ? Number(res.dataQc[data.id])/1000 : 0).toFixed(2);
                        }

                        return data_str;
                    }
                },
                {
                    data: null, title: 'Action',
                    render: function (data) {
                        let data_str = "";
                        // if(res.viewtype == 'N'){  
                        //     if(data.delivery_status.trim().toUpperCase() == 'D'){
                        //         data_str += '<a href="#" class="btn btn-warning m-1 btn-sm">ขนส่งหมดแล้ว</a>';// update status /
                        //     }
                        //     else{
                        //         if(res.canEdit){
                                    data_str += '<a href="/HarvestPlans/edit/' + data.crop_id + '/' + data.id + '" class="btn btn-warning m-1 btn-sm">แก้ไข</a>'; // edit
                            //     }
                            //     else{

                            //     }
                            // }
                            
                            data_str += '<a href="/HarvestPlans/separate/' + data.crop_id + '/' + data.id + '/S" class="btn btn-warning m-1 btn-sm">แยก</a>'; // request move
                            data_str += '<a href="/HarvestPlans/request/' + data.crop_id + '/' + data.id + '/M" class="btn btn-warning m-1 btn-sm">ย้ายวัน</a>'; // request date
                            data_str += '<a href="javascript:void(0)" onclick="sendUpdateStatus(' + data.id + ',\'delivery\')" class="btn btn-warning m-1 btn-sm">รอขนส่ง</a>'; // update status /
                            data_str += '<a href="javascript:void(0)" onclick="sendUpdateStatus(' + data.id + ',\'reject\')" class="btn btn-warning m-1 btn-sm">ขอยกเลิกเก็บ</a>'; // update status / 
                            data_str += '<a href="javascript:void(0)" onclick="sendUpdateStatus(' + data.id + ',\'manualMat\')" class="btn btn-warning m-1 btn-sm">Custom Material</a>'; // update status
                        // }

                        return data_str;
                    }
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

    function findByField(list, field, value) {
        return list.find(item => item[field] == value);
    }

    function sendUpdateStatus(id, mode) {
        console.log(id);
        console.log(mode);
    }
</script>
