@extends('layouts.app')

@section('content')
 <input type="hidden" id="schedulesId" name="schedules_id" value="{{ $id ?? '' }}">

<div class="container">
  <!-- Title -->
  <h3 class="mb-1">Template แผนปฏิบัติงาน</h3>
  <h5 class="text-muted mb-4"><span id="seed">seed</span> กิจกรรม <span id="detail">detail</span>(<span id="age">1</span>)</h5>

  <!-- ข้อมูลกิจกรรม -->
  <div class="mb-3">
    <label class="form-label">ชื่อกิจกรรม</label>
    <input type="text" class="form-control" id="activity_name" readonly>
  </div>

  <div class="mb-3">
    <label class="form-label">อายุพืชที่ปฏิบัติงาน</label>
    <input type="text" class="form-control" id="activity_age" readonly>
  </div>

  <div class="mb-3">
    <label class="form-label">ใช้ตรวจสอบโรคและแมลง</label>
    <select class="form-select" id="check_disease">
      <option value="Y">Y</option>
      <option value="N">N</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">ใช้ตรวจสอบภาพถ่าย</label>
    <select class="form-select" id="check_image">
      <option value="Y">Y</option>
      <option value="N">N</option>
    </select>
  </div>

  <div class="row my-3">
      <div class="col-md-12">
          <form action="#" method="get" class="row g-2 align-items-center">
              <div class="col-12 col-sm-10">
                  <input type="text" class="form-control" name="search-custom" id="search-custom" onkeyup="filterButtons()" placeholder="Search..." value="">
              </div>
              <div class="col-12 col-sm-2">
                  <button type="button" class="btn btn-danger w-100" onclick="clearInput();">Clear</button>
              </div>
          </form>
      </div>
  </div>

  <!-- ปุ่ม dynamic จาก chemicals -->
  <div class="mb-3" id="chemical-buttons" style="flex-wrap: wrap;"></div>

  <!-- ตารางข้อมูล -->
  <div class="table-responsive mt-4">
    <table class="table table-bordered" id="chemical-table">
      <thead class="table-light">
        <tr>
          <th>ชื่อ</th>
          <th>ขนาด</th>
          <th>หน่วย</th>
          <th>รูปขนาด</th>
          <th>รูปหน่วย</th>
          <th>กลุ่ม</th>
          <th>ความสำคัญ</th>
          <th>ความจำเป็น</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <!-- ปุ่มล่าง -->
  <div class="d-flex justify-content-between mt-4">
    <button class="btn btn-secondary" onclick="window.history.back()">Back</button>
    <button class="btn btn-success" id="btn-save" onclick="onUpdate();">Save</button>
  </div>
</div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
let selectedChemicals = [];
let unitOptions = '';
let chemicalData = [];

$(document).ready(function () {
    let id = $('#schedulesId').val();

  // โหลด unit สำหรับ dropdown
  $.getJSON('/api/units', function(units) {
    unitOptions = units.data.map(u => `<option value="${u.id}">${u.name}</option>`).join('');
  });

  // โหลด chemicals สร้างปุ่ม
  $.getJSON('/api/chemicals', function(chemicals) {
    chemicalData = chemicals.data;
    chemicals.data.forEach(chem => {
      $('#chemical-buttons').append(`
        <button type="button" class="btn btn-outline-primary m-1" id="chem-${chem.id}" data-id="${chem.id}" data-label="${chem.name}">
          ${chem.name}
        </button>
      `);
    });
  });

  // โหลด data หลัก
  $.getJSON(`/api/tmpSchedules/showmanage/${id}`, function(data) {
    $('#seed').text(data.data.input_item.name);
    $('#detail').text(data.data.name);
    $('#age').text(data.data.day);
    $('#activity_name').val(data.data.name);
    $('#activity_age').val(data.data.day);
    // set ค่าอื่นๆ ถ้ามี
    console.log(data.data);
    data.data.tmp_schedule_plan_details.forEach(detail => {
      const chemId = detail.chemical_id;
      const chem = chemicalData.find(c => c.id == chemId);
      if (chem) {
        toggleChemical(chemId, chem.name, detail);
      }
    });
  });

});

  // toggle ปุ่ม chemical
  $(document).on('click', '#chemical-buttons button', function () {
    const chemId = String($(this).data('id'));
    const label = $(this).data('label');
    toggleChemical(chemId, label);
  });

  // Save
  function onUpdate() {
    
    let schedulesId = $('#schedulesId').val();
    const checklistData = [];

    $('#chemical-table tbody tr').each(function () {
      const row = $(this);
      const rowData = {
        chemical_id: row.find('input[name="chemical_id[]"]').val(),
        plan_detail_id: row.find('input[name="plan_detail_id[]"]').val(),
        name: row.find('td:first').text().trim(), // หรือเก็บ name ไว้ใน hidden ก็ได้
        p_value: row.find('input[name="size[]"]').val(),
        p_unit_id: row.find('select[name="size_unit[]"]').val(),
        value: row.find('input[name="image_size[]"]').val(),
        unit_id: row.find('select[name="image_size_unit[]"]').val(),
        set_group: row.find('input[name="group[]"]').val(),
        rate: row.find('select[name="nor_impo[]"]').val() === "Nor" ? "1" : "0",
        ctype: row.find('select[name="m_opt[]"]').val()
      };
      checklistData.push(rowData);
    });

    const payload = {
      activity_name: $('#activity_name').val(),
      activity_age: $('#activity_age').val(),
      check_disease: $('#check_disease').val(),
      check_image: $('#check_image').val(),
      chemicals: checklistData
    };
    console.log(payload);


    $.post('/api/tmpSchedules/save/' + schedulesId, payload, function (res) {
      console.log(res);
    });
  }

  // ฟังก์ชันเพิ่มแถวเคมีในตาราง และตั้งสถานะปุ่ม
function addChemicalRow(chemId, label, detailData = null) {
  const rowId = `row-${chemId}`;
  if ($(`#${rowId}`).length) return;

  selectedChemicals.push(chemId);

  const btn = $(`#chemical-buttons button[data-id='${chemId}']`);
  btn.removeClass('btn-outline-primary').addClass('btn-danger');

  // เตรียมค่าจาก detailData (หรือ fallback เป็นค่าว่าง)
  const sizeVal = detailData?.p_value || '';
  const sizeUnitId = detailData?.p_unit_id || '';
  const imageSizeVal = detailData?.value || '';
  const imageSizeUnitId = detailData?.unit_id || '';
  const groupVal = detailData?.set_group || '';
  const norImpoVal = detailData?.rate === "1" ? "Nor" : "Impo";
  const mOptVal = detailData?.ctype || "M";

  // ฟังก์ชันช่วยใส่ selected ให้ option ที่ตรงกับค่าที่ต้องการ
  const renderUnitOptions = (selectedId) => {
    return unitOptions.replace(
      new RegExp(`value="${selectedId}"`),
      `value="${selectedId}" selected`
    );
  };

  // เพิ่ม row ลงในตาราง
  $('#chemical-table tbody').append(`
    <tr id="${rowId}">
      <td>
        ${label}
        <input type="hidden" name="chemical_id[]" value="${chemId}">
        <input type="hidden" name="plan_detail_id[]" value="${detailData?.id || ''}">
      </td>
      <td><input type="text" class="form-control" name="size[]" value="${sizeVal}"></td>
      <td>
        <select class="form-select" name="size_unit[]">
          ${renderUnitOptions(sizeUnitId)}
        </select>
      </td>
      <td><input type="text" class="form-control" name="image_size[]" value="${imageSizeVal}"></td>
      <td>
        <select class="form-select" name="image_size_unit[]">
          ${renderUnitOptions(imageSizeUnitId)}
        </select>
      </td>
      <td><input type="text" class="form-control" name="group[]" value="${groupVal}"></td>
      <td>
        <select class="form-select" name="nor_impo[]">
          <option value="Nor" ${norImpoVal === 'Nor' ? 'selected' : ''}>Nor</option>
          <option value="Impo" ${norImpoVal === 'Impo' ? 'selected' : ''}>Impo</option>
        </select>
      </td>
      <td>
        <select class="form-select" name="m_opt[]">
          <option value="M" ${mOptVal === 'M' ? 'selected' : ''}>Man</option>
          <option value="O" ${mOptVal === 'O' ? 'selected' : ''}>Opt</option>
        </select>
      </td>
    </tr>
  `);
}

function toggleChemical(chemId, label, detailData = null) {
  const rowId = `row-${chemId}`;
  const btn = $(`#chemical-buttons button[data-id='${chemId}']`);

  console.log(btn);
  console.log(selectedChemicals);
  console.log(chemId);
  console.log(selectedChemicals.includes(chemId));
  if (selectedChemicals.includes(chemId)) {
    selectedChemicals = selectedChemicals.filter(id => id !== chemId);
    $(`#${rowId}`).remove();
    btn.removeClass('btn-danger').addClass('btn-outline-primary');
  } else {
    addChemicalRow(chemId, label, detailData);
  }
}

function filterButtons() {
  const input = document.getElementById("search-custom");
  const filter = input.value.toLowerCase();
  const buttons = document.querySelectorAll("#chemical-buttons button");

  buttons.forEach(button => {
    const label = button.getAttribute("data-label") || button.innerText;
    if (label.toLowerCase().includes(filter)) {
      button.style.display = "inline-block";
    } else {
      button.style.display = "none";
    }
  });
}

    function clearInput() {
        $('#search-custom').val("");
        filterButtons();
    }
</script>
