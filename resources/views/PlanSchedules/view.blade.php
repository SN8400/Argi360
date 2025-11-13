@extends('Layouts.app')

@section('title', 'Plan Schedules')

@section('content')
    <style>
        table {
        border-collapse: collapse;
        width: 100%;
        }

        th, td {
        border: 1px solid #000;
        padding: 8px;
        text-align: center;
        }

        thead th {
        background-color: #f0f0f0;
        }
    </style>

    <h2>ตารางพ่นสารเคมีถั่วแระ พันธุ์: <span id="item_name"></span> | รุ่นปลูก: <span id="crop_name"></span></h2>
    <h2>หัวหน้ากลุ่ม : <span id="broker_name"></span> | เขต: <span id="area_name"></span></h2>
    <h2>หัวหน้า: <span id="head_name">..............................</span> พนักงาน: <span id="user_name">..............................</span></h2>
    <h2>เลขประจำตัวประชาชน[   ][   ][   ][   ][   ][   ][   ][   ][   ][   ][   ][   ][   ]</h2>
    <h2>ชื่อเกษตรกร: <span>..............................</span> เกรด: <span>..............................</span> เบอร์โทร: <span>..............................</span></h2>
    <h2>จำนวนเมล็ดพันธุ์: <span>..............................</span>กก. พื้นที่ปลูก: <span>..............................</span>ไร่ วันที่ปลูก: <span>..............................</span></h2>
    <h2>วันที่หยุดพ่นสารเคมี: <span>..............................</span> วันที่เก็บเกี่ยว: <span>..............................</span></h2>
    <div class="row my-3">
        <div class="col-md-12">
            <form action="#" method="get" class="row g-2 align-items-center" id="buttonForm"></form>
        </div>
    </div>
    <input type="hidden" id="crop_id" name="crop_id" value="{{ $crop_id ?? '' }}">
    <input type="hidden" id="broker_id" name="broker_id" value="{{ $broker_id ?? '' }}">
    <input type="hidden" id="type_id" name="type_id" value="{{ $type_id ?? '' }}">
    <input type="hidden" id="input_item_id" name="input_item_id" value="{{ $input_item_id ?? '' }}">

    <div id="container"></div>


    <div class="col-sm-1 "><b>หมายเหตุ</b></div>
    <div class="row">
        <div class="col-6">1.ให้พ่นสารเคมีตามตารางที่กำหนดอย่างเคร่งครัด<br />
            2.การใช้สารเคมีบางชนิดอาจมีการเปลี่ยนแปลงขึ้นอยู่กับดุลพินิจของหัวหน้าแผนกส่งเสริมขึ้นไป<br />
            3.ห้ามใช้สารเคมีอื่นๆนอกเหนือจากที่บริษัทกำหนดให้โดยเด็ดขาด<br />
            4.การเก็บเกี่ยวผลผลิตต้องเก็บเกี่ยวตามที่พนักงานส่งเสริมกำหนด<br />
            5.การสุ่มตรวจสารเคมีจะสุ่มก่อนเก็บเกี่ยว 5-7 วัน และสุ่มซ้ำอีกครั้งหน้าโรงงาน
        </div>
        <div class="col-6">
            6.สารเคมีกำจัดวัชพืชทุกชนิด พ่นไม่เกิน 24 วัน และห้ามพ่นโดนใบ,ยอดถั่วแระ<br />
            7.ปุ๋ย 13-13-21 ให้แบ่งใส่ 2 ครั้ง ครั้งละ 25 กก./ไร่<br />
            8.ใช้อุปกรณ์ตวงวัดสารเคมีที่บริษัทกำหนด ได้แก่ ช้อนโมแลน หรือ ช้อนโต๊ะ<br />
            ปริมาณ 1 ช้อน = สารเคมีผง 5 กรัม,สารเคมีน้ำ 10 ซีซี<br />
            9. การพ่นสารเคมี คาซูกาไมซิน (คาซู่) แนะนำให้พ่นแยกถังกับสารเคมีอื่นๆ(พ่นเดี่ยว) และพ่นก่อนหรือหลังพ่นสาเคมีอื่นๆ 1-2 วัน
        </div>
        <div class="col-4 txtaligncenter">
            <img src="{{ asset('img/sign/chem1_sp2025.png') }}" class="widchempic2" alt="chem1">
        </div>
        <div class="col-4 txtaligncenter">
            <img src="{{ asset('img/sign/chem2_sp2025.png') }}" class="widchempic2" alt="chem2">
        </div>
        <div class="col-4 txtaligncenter">
            <img src="{{ asset('img/sign/chem3_sp2025.png') }}" class="widchempic2" alt="chem3">
        </div>
    </div>

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    let table;
    let chemicalModal;
    $(document).ready(function() {
        let type_id = $('#type_id').val();
        let crop_id = $('#crop_id').val();
        let broker_id = $('#broker_id').val();
        let input_item_id = $('#input_item_id').val();

        $.ajax({
            url: '/api/planSchedules/' + type_id + '/' + crop_id + '/' + broker_id + '/' + input_item_id,
            type: 'GET',
            success: function(res) {
                console.log(res);
                $('#item_name').text(res.data[0].input_item.name);
                $('#crop_name').text(res.data[0].crop.name);
                $('#broker_name').text(res.data[0].broker.fname + " " + res.data[0].broker.lname + " " + res.data[0].broker.code);
                $('#area_name').text(res.data[0].broker.address2);
                renderData(res.data);
            },
            error: function() {
                alert("เกิดข้อผิดพลาดในการโหลดข้อมูล");
            }
        });
    });

function renderData(data) {
    
    let type_id = $('#type_id').val();

    const container = document.getElementById("container");
    container.innerHTML = "";
    let maxCols = Math.max(...Object.values(data).map(v => v.details?.length || 0));
    const table = document.createElement("table");
    const thead = document.createElement("thead");
    const tr = document.createElement("tr");
    tr.innerHTML = `<th>ครั้งที่</th>`;
    tr.innerHTML += `<th>อายุ</th>`;
    tr.innerHTML += `<th>แผน</th>`;
    tr.innerHTML += `<th>จริง1</th>`;
    tr.innerHTML += `<th>จริง2</th>`;
    tr.innerHTML += `<th>จน. ถ้ง</th>`;
    if(type_id == "small"){
        tr.innerHTML += `<th colspan="` + maxCols +`">ตารางพ่นสารเคมีต่อน้ำ 20 ลิตร (<span style="background-color:#ffff66;">&nbsp;&nbsp;&nbsp;</span>=สารเคมีต้องใช้แต่ละรอบ<span style="background-color:orange;">&nbsp;&nbsp;&nbsp;</span>=สารเคมีบังคับต้องใช้100%<span style="background-color:#8FBC8F;">&nbsp;&nbsp;&nbsp;</span>=สารเคมีเสริมใช้ตามคำแนะนำของพนักงาน<span style="background-color:#ee8ae4;">&nbsp;&nbsp;&nbsp;</span>=สารเคมีคุมวัชพืชและสารเคมีกำจัดวัชพืช)</th>`;
    }
    else if(type_id == "big"){
        tr.innerHTML += `<th colspan="` + maxCols +`">ตารางพ่นสารเคมีต่อน้ำ 200 ลิตร (<span style="background-color:#ffff66;">&nbsp;&nbsp;&nbsp;</span>=สารเคมีต้องใช้แต่ละรอบ<span style="background-color:orange;">&nbsp;&nbsp;&nbsp;</span>=สารเคมีบังคับต้องใช้100%<span style="background-color:#8FBC8F;">&nbsp;&nbsp;&nbsp;</span>=สารเคมีเสริมใช้ตามคำแนะนำของพนักงาน<span style="background-color:#ee8ae4;">&nbsp;&nbsp;&nbsp;</span>=สารเคมีคุมวัชพืชและสารเคมีกำจัดวัชพืช)</th>`;
    }
    else {
        tr.innerHTML += `<th colspan="` + maxCols +`">ตารางพ่นสารเคมีต่อไร่ (โดรนพ่นสารเคมี) (<span style="background-color:#ffff66;">&nbsp;&nbsp;&nbsp;</span>=สารเคมีต้องใช้แต่ละรอบ<span style="background-color:orange;">&nbsp;&nbsp;&nbsp;</span>=สารเคมีบังคับต้องใช้100%<span style="background-color:#8FBC8F;">&nbsp;&nbsp;&nbsp;</span>=สารเคมีเสริมใช้ตามคำแนะนำของพนักงาน<span style="background-color:#ee8ae4;">&nbsp;&nbsp;&nbsp;</span>=สารเคมีคุมวัชพืชและสารเคมีกำจัดวัชพืช)</th>`;
    }

    const tbody = document.createElement("tbody");
    let idx = 0;
    Object.entries(data).sort((a, b) => Number(a[0]) - Number(b[0])).forEach(([key, value]) => {  
        if(value.details == 0){return;}
        idx++;
        var dayStr = 0;
        if(value?.day > 0){ 
            dayStr = parseInt(value?.day)  + 1; 
        } else 
        { 
            dayStr = value?.day; 
        }
        const tr = document.createElement("tr");
        tr.innerHTML = `<td>${idx}</td>`;
        tr.innerHTML += `<td>${ dayStr }</td>`;
        tr.innerHTML += `<td></td>`;
        tr.innerHTML += `<td></td>`;
        tr.innerHTML += `<td></td>`;
        tr.innerHTML += `<td></td>`;
        for (let i = 0; i < maxCols; i++) {
            const td = document.createElement("td");
            if(value.details[i]){
                td.className = "ctype : " + value.details[i].ctype + "; length : "  + value.details.length + "; set_group : " + value.details[i].set_group + "; rate : " + value.details[i].rate;
                // if(value.details[i].set_group > 1){
                // if(value.details.length == 1){
                    if(!value.details[i].set_group){
                        if(value.details[i].ctype == 'M'){
                            td.style.backgroundColor = "orange";
                        }
                        if(value.details.length == 1){
                            td.style.backgroundColor = "#ffff66";
                        }
                    // }
                }
                else{
                    if(value.details[i].rate == 1){
                        if(value.details[i].ctype == 'M'){
                            td.style.backgroundColor = "#ffff66";
                        }
                        else if(value.details[i].ctype == 'Y'){
                            td.style.backgroundColor = "#ee8ae4";
                        }
                        else{
                            td.style.backgroundColor = "#8FBC8F";
                        }
                    }
                    else{
                        if(value.details[i].ctype == 'M'){
                            td.style.backgroundColor = "orange";
                        }
                    }
                }

                td.innerHTML = value.details[i].chemical.name;
                td.innerHTML += '<br>';
                td.innerHTML += '(';
                if(type_id == "small"){
                    td.innerHTML += value.details[i].value
                    td.innerHTML += value.details[i].unit.name;
                }
                else if(type_id == "big"){                
                    if(dayStr == 0 && value.ctype != 'S'){
                        td.innerHTML += parseInt(value.details[i].value);
                    }
                    else{
                        if(value.details[i].unit.name == 'Kg'){
                            td.innerHTML += parseInt(value.details[i].value);
                        }
                        else{
                            td.innerHTML += parseInt(value.details[i].value) * 10;
                        }
                    }
                    td.innerHTML += value.details[i].unit.name;
                }
                else {
                    if(dayStr == 0 && value.ctype == 'F'){
                        td.innerHTML += parseInt(value.details[i].value);
                    }
                    else{
                        if(dayStr <= 15){
                            td.innerHTML += parseInt(value.details[i].value) * 2;
                        }
                        else{
                            if(dayStr <= 44){
                                td.innerHTML += parseInt(value.details[i].value) * 3;
                            }
                            else{
                                td.innerHTML += parseInt(value.details[i].value) * 4;
                            }
                        }
                    }
                    td.innerHTML += value.details[i].unit.name;
                }

                td.innerHTML += ')'; 
                td.innerHTML += '<br>';
                td.innerHTML += `<img src="{{ asset('img/chemicals/C` + value.details[i].chemical_id + `.jpg') }}" class="widchempic2" alt="` + value.details[i].chemical.name + `" width="50px">`;
                td.innerHTML += '<br>';

                if(type_id == "small"){
                    if(!value.details[i].p_value){
                        if((value.details[i].value / 10) >= 10){
                            td.innerHTML += Math.round(value.details[i].value / 10);
                        }else{
                            td.innerHTML += (Math.round(value.details[i].value / 10)).toFixed(1);
                        }
                    }else{
                        if(value.details[i].p_value >= 10){
                            td.innerHTML += Math.round(value.details[i].p_value);
                        }else{
                            td.innerHTML += (Math.round(value.details[i].p_value)).toFixed(1);
                        }
                    }
                }
                else if(type_id == "big"){                
                    if(dayStr == 0 && value.ctype != 'S'){
                        td.innerHTML += (Math.round(value.details[i].p_value)).toFixed(1);
                    }
                    else if(value.details[i].unit.name == 'cc'){
                        td.innerHTML += (Math.round(value.details[i].value) * 10 / 50).toFixed(1);
                    }
                    else if(value.details[i].unit.name == 'g'){
                        td.innerHTML += (Math.round(value.details[i].value) * 10 / 25).toFixed(1);
                    }
                    else if(value.details[i].unit.name == 'Kg'){
                        td.innerHTML += (Math.round(value.details[i].p_value)).toFixed(1);
                    }
                    else{
                        td.innerHTML += (Math.round(value.details[i].value) * 10).toFixed(1);
                    }
                    
                }
                else {
                    if(dayStr == 0 && value.ctype == 'F'){
                        if(!value.details[i].p_value){
                            if((value.details[i].value / 10) >= 10){
                                td.innerHTML += Math.round(value.details[i].value / 10);
                            }else{
                                td.innerHTML += (Math.round(value.details[i].value / 10)).toFixed(1);
                            }
                        }else{
                            if(value.details[i].p_value >= 10){
                                td.innerHTML += Math.round(value.details[i].p_value);
                            }else{
                                td.innerHTML += (Math.round(value.details[i].p_value)).toFixed(1);
                            }
                        }
                    }
                    else if(dayStr <= 15){
                        if(!value.details[i].p_value){
                            if((value.details[i].value / 10) >= 10){
                                td.innerHTML += Math.round(value.details[i].value * 2 / 10);
                            }else{
                                td.innerHTML += (Math.round(value.details[i].value * 2 / 10)).toFixed(1);
                            }
                        }else{
                            if(value.details[i].p_value >= 10){
                                td.innerHTML += Math.round(value.details[i].p_value * 2);
                            }else{
                                td.innerHTML += (Math.round(value.details[i].p_value * 2)).toFixed(1);
                            }
                        }
                    }
                    else if(dayStr <= 44){
                        if(!value.details[i].p_value){
                            if((value.details[i].value / 10) >= 10){
                                td.innerHTML += Math.round(value.details[i].value * 3 / 10);
                            }else{
                                td.innerHTML += (Math.round(value.details[i].value * 3 / 10)).toFixed(1);
                            }
                        }else{
                            if(value.details[i].p_value >= 10){
                                td.innerHTML += Math.round(value.details[i].p_value * 3);
                            }else{
                                td.innerHTML += (Math.round(value.details[i].p_value * 3)).toFixed(1);
                            }
                        }
                    }
                    else{
                        if(!value.details[i].p_value){
                            if((value.details[i].value / 10) >= 10){
                                td.innerHTML += Math.round(value.details[i].value * 4 / 10);
                            }else{
                                td.innerHTML += (Math.round(value.details[i].value * 4 / 10)).toFixed(1);
                            }
                        }else{
                            if(value.details[i].p_value >= 10){
                                td.innerHTML += Math.round(value.details[i].p_value * 4);
                            }else{
                                td.innerHTML += (Math.round(value.details[i].p_value * 4)).toFixed(1);
                            }
                        }
                    }
                }

                if(value.details[i].p_unit.name){
                    td.innerHTML += `<img src="{{ asset('img/icons/` + value.details[i].p_unit.name + `.png') }}" class="widchempic2" alt="Spoon" width="18px">`;
                }
                else{
                    td.innerHTML += `<img src="{{ asset('img/icons/spoon.png') }}" class="widchempic2" alt="Spoon" width="18px">`;
                }

                td.innerHTML += `<img src="{{ asset('img/icons/check-box.png') }}" class="widchempic2" alt="check-box" width="18px">`;

            }
            
            tr.appendChild(td);
        }
        tbody.appendChild(tr);

    });
    
    thead.appendChild(tr);
    table.appendChild(thead);
    table.appendChild(tbody);
    container.appendChild(table);
}
</script>
