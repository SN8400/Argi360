@extends('Layouts.app')
@section('title', 'แผนเก็บเกี่ยว')
@section('content')
<style>
    .table.table-bordered th,
    .table.table-bordered td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
    <input type="hidden" id="crop_id" value="{{ $id ?? '-' }}">
    <h2>แผนเก็บเกี่ยว Crop : <span id="crop_name"></span></h2>
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

    <div id="container"></div>

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    let table;
    let harvestPlansList = [];
    let inputItemsList = [];
    let matTypeList = {
        N: 'ปรกติ (หัวแปลง)',
        C: 'Cluster',
        S: 'รีดเมล็ด'
    };

    window.addEventListener('load', function () {
        getData();
    });

    function getData(){
        let crop_id = $('#crop_id').val();
        $.ajax({
            url: '/api/harvestPlans/' + crop_id,
            method: 'GET',
            success: function(res) {
                console.log(res);
                harvestPlansList = res.data;
                renderData(harvestPlansList);
            },
            error: function(xhr) {
                alert('Error fetching farmer data.');
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

        
    function renderData(harvestPlansList) {
        // console.log(harvestPlansList);
        const limitHarvest = {{ config('parameter.limitrate.harvest') }};
        const container = document.getElementById("container");
        container.innerHTML = "";

        const table = document.createElement("table");
        table.className = "table table-bordered";
        const thead = document.createElement("thead");
        const tr = document.createElement("tr");
        tr.innerHTML = `<th>#</th>`;
        tr.innerHTML += `<th>วันที่</th>`;
        tr.innerHTML += `<th>ประมาณการผลผลิต(ตัน)</th>`;

        Object.values(harvestPlansList.inputItemList).forEach(inputItem => {
            Object.values(matTypeList).forEach(typeItem => {
                tr.innerHTML += `<th>` + inputItem.tradename + `|` + typeItem + `(ตัน)</th>`;
            });
        });
        tr.innerHTML += `<th>รายละเอียด</th>`;
        thead.appendChild(tr);
        table.appendChild(thead);

        const tbody = document.createElement("tbody");
        let cnt = 0;
        Object.values(harvestPlansList.harvestPlanData).forEach(harvestItem => {
            cnt++;
            const tr2 = document.createElement("tr");
            tr2.innerHTML = `<td>${cnt}</td>`;
            tr2.innerHTML += `<td>${harvestItem.date_est}</td>`;
            let est_str = "";
            if(harvestItem.est_value > limitHarvest){
                est_str += `<td><b style='color:red;'>${ Number(harvestItem.est_value).toFixed(2) }</b>`;
            }
            else{
                est_str += `<td>${ Number(harvestItem.est_value).toFixed(2) }`;
            }

            if(harvestItem.bf_value > limitHarvest){
                est_str += ` | <b style='color:red;'>${ Number(harvestItem.bf_value).toFixed(2) }</b>`;
            }
            else{
                est_str += ` | ${ Number(harvestItem.bf_value).toFixed(2) }`;
            }

            if(harvestItem.act_value > limitHarvest){
                est_str += ` | <b style='color:red;'>${ Number(harvestItem.act_value).toFixed(2) }</b>`;
            }
            else{
                est_str += ` | ${ Number(harvestItem.act_value).toFixed(2) }`;
            }
            est_str += ` | (${ harvestItem.count_value })`;

            if(harvestPlansList.sum2Data[harvestItem.date_est]){
                est_str += ` ${ Number(harvestPlansList.sum2Data[harvestItem.date_est]).toFixed(2) }`;
            }
            est_str += `</td>`;
            tr2.innerHTML +=est_str;
            Object.entries(harvestPlansList.inputItemList).forEach(([inputKey, inputItem]) => {
                Object.entries(matTypeList).forEach(([typeKey, typeItem]) => {
                    if(typeKey == 'N'){
                        if(harvestPlansList?.detailData?.[harvestItem.date_est]?.[inputKey]?.['N']){
                            // console.log(harvestPlansList.detailData?.[harvestItem.date_est]?.[inputKey]?.['N']);
                            tr2.innerHTML += `<td>${ Number(harvestPlansList.detailData?.[harvestItem.date_est]?.[inputKey]?.['N']).toFixed(2) }`;
                        }
                        else{
                            tr2.innerHTML += `<td>`;
                        }

                        if(harvestPlansList.detailData?.[harvestItem.date_est]?.[inputKey]?.['T']){
                            tr2.innerHTML += ` (${ Number(harvestPlansList.detailData?.[harvestItem.date_est]?.[inputKey]?.['T']).toFixed(2) })`;
                        }
                        
                        if(harvestPlansList.detail2Data?.[harvestItem.date_est]?.[inputKey]?.['N']){
                            tr2.innerHTML += ` / (${ Number(harvestPlansList.detail2Data?.[harvestItem.date_est]?.[inputKey]?.['N']).toFixed(2) })`;
                        }
                        
                        if(harvestPlansList.detail2Data?.[harvestItem.date_est]?.[inputKey]?.['T']){
                            tr2.innerHTML += ` / (${ Number(harvestPlansList.detail2Data?.[harvestItem.date_est]?.[inputKey]?.['T']).toFixed(2) }) </td>`;
                        }
                        else{
                            tr2.innerHTML += ` </td>`;
                        }
                        
                    }else{
                        if(harvestPlansList.detailData?.[harvestItem.date_est]?.[inputKey]?.[typeKey]){    
                            tr2.innerHTML += `<td>${ Number(harvestPlansList.detailData?.[harvestItem.date_est]?.[inputKey]?.[typeKey]).toFixed(2) }`;
                        }
                        else{
                            tr2.innerHTML += `<td>`;
                        }
                        
                        if(harvestPlansList.detail2Data?.[harvestItem.date_est]?.[inputKey]?.[typeKey]){    
                            tr2.innerHTML += ` / ${ Number(harvestPlansList.detail2Data?.[harvestItem.date_est]?.[inputKey]?.[typeKey]).toFixed(2) } </td>`;
                        }
                        else{
                            tr2.innerHTML += `</td>`;
                        }
                    }
                    // tr.innerHTML += `<td>` + inputItem.tradename + `|` + typeItem + `</td>`;
                });
            });
            tr2.innerHTML += `<td><a href="/HarvestPlans/detailDate/${harvestItem.crop_id}/${harvestItem.date_est}/N" class="grow_state itemhover">จัดเก็บเกี่ยว</a> | <a href="{{ route('HarvestPlans', ['id' => $id]) }}" class="grow_state itemhover">ขนส่ง</a></td>`;
            tbody.appendChild(tr2);
        });
        table.appendChild(tbody);
        container.appendChild(table);
    }

</script>
