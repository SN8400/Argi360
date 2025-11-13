@extends('Layouts.app')

@section('title', 'Template Plan Schedules')

@section('content')
<style>
    td {
    white-space: normal !important;
    word-break: break-word;
    }
</style>
 <input type="hidden" id="schedulesId" name="schedules_id" value="{{ $id ?? '' }}">

<div class="row my-3">
    <div class="col-md-12">
    <h3>รายละเอียดTemplateสารเคมี "<span id="schedule_name"></span>"</h3>
    </div>
</div>

<div id="container"></div>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
$(document).ready(function () {
    let schedulesId = $('#schedulesId').val();
    if (schedulesId) {
        $.ajax({
            url: '/api/tmpSchedules/viewdetail/' + schedulesId,
            method: 'GET',
            success: function(res) {
                $('#schedule_name').text(res.tmpname);
                renderData(res.data);
            },
            error: function() {
                alert("Error loading data.");
            }
        });
    }
});

    
function renderData(data) {
   const container = document.getElementById("container");
  container.innerHTML = "";

  Object.entries(data).sort((a, b) => a[0].localeCompare(b[0])).forEach(([plantName, ages]) => {
    const header = document.createElement("h3");
    header.textContent = `พันธุ์พืช : ${plantName}`;
    container.appendChild(header);

    // หาความยาวมากที่สุด เพื่อ set จำนวนคอลัมน์
    let maxCols = 0;
    Object.values(ages).forEach(arr => {
        
    let maxKey = Math.max(...Object.keys(arr).map(k => parseInt(k)));
      if (maxKey > maxCols) maxCols = maxKey;
    });

    const table = document.createElement("table");
    table.className = "table table-bordered";

    const thead = document.createElement("thead");
    const tr = document.createElement("tr");
    tr.innerHTML = `<th>อายุ</th>`;
    for (let i = 0; i < maxCols; i++) {
      tr.innerHTML += `<th>สารเคมี ลำดับที่ ${i + 1}</th>`;
    }
    thead.appendChild(tr);
    table.appendChild(thead);

    const tbody = document.createElement("tbody");

    Object.entries(ages).forEach(([age, chemicalArrays]) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `<td>${age > 0 ? parseInt(age) : age}</td>`;

      for (let i = 1; i < maxCols +1; i++) {
        const td = document.createElement("td");

        if (chemicalArrays[i] && chemicalArrays[i][0]) {
          const chem = chemicalArrays[i][0];
          let style = "";
          if (chem.ctype === "O") style += "font-style: italic;";
          if (chem.rate > 1) style += "font-weight: bold;";

          td.innerHTML = `<span style="${style}">
            ${chem.chem_name} (${chem.use_value} ${chem.unit_name})<br>
            (${Math.round(chem.pic_value * 10) / 10} ${chem.pic_unit_name})
          </span>`;
        }

        tr.appendChild(td);
      }

      tbody.appendChild(tr);
    });

    table.appendChild(tbody);
    container.appendChild(table);
  });
}
</script>
