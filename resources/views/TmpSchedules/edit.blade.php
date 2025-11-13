@extends('Layouts.app')

@section('title', 'Template Plan Schedules')

@section('content')
    <h2>Templete ตารางปฏิบัติงาน และ ตารางพ่นสารเคมี</h2>
    <h3>ชื่อ "<span id="schedule_name"></span>"</h3>
    <div class="row my-3">
        <div class="col-md-12">
            <form action="#" method="get" class="row g-2 align-items-center" id="buttonForm">
                
            </form>
        </div>
    </div>
    <input type="hidden" id="tmpId" name="tmpId" value="{{ $id ?? '' }}">

    <table class="table" id="head-table">
        <thead>
            <tr>
                <th>#</th>
                <th>InputItem</th>
                <th>Task</th>
                <th>Age</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    let table;
    $(document).ready(function() {
        let id = $('#tmpId').val();
        if(id){
            $.ajax({
                url: '/api/tmpSchedules/' + id,
                type: 'GET',
                success: function(res) {
                    console.log(res);
                    getTable(res.data);
                    $('#schedule_name').text(res.schedule.name);
                    AddButton(res.inputItems);
                },
                error: function() {
                    alert("เกิดข้อผิดพลาดในการโหลดข้อมูล");
                }
            });
        }
    });

    function filterById(id) {
        console.log("Selected ID: " + id);
    }

    function AddButton(data) { 
        const form = document.getElementById('buttonForm');
        const colDiv = document.createElement('div');
        colDiv.className = 'col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-danger w-100';
        btn.innerText = "All";
        btn.onclick = () => searchByText("");
        colDiv.appendChild(btn);
        form.appendChild(colDiv);

        data.forEach(item => {
            const colDiv = document.createElement('div');
            colDiv.className = 'col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-primary w-100';
            btn.innerText = item.name;
            btn.onclick = () => searchByText(item.tradename);

            colDiv.appendChild(btn);
            form.appendChild(colDiv);
        });

        const colDiv1 = document.createElement('div');
        colDiv1.className = 'col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2';
        const btn1 = document.createElement('button');
        btn1.type = 'button';
        btn1.className = 'btn btn-success w-100';
        btn1.innerText = "Back";
        btn1.onclick = () => window.location.href = "/TmpSchedules";
        colDiv1.appendChild(btn1);
        form.appendChild(colDiv1);

    }

    function clearInput() {
        $('#search-custom').val("");
        if (table) {
            table.search("").draw();
        }
    }

    function getTable(data) {
        table = $('#head-table').DataTable({
            data: data,
            dom: 'lrtip',
            destroy: true,
            info: false,
            ordering: false,
            columns: [
                {
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'input_item', title: 'พันธุ์พืช',
                    render: function(data, type, row, meta) {
                        return data && data.tradename ? data.tradename : '-';
                    }
                },
                { data: 'name', title: 'กิจกรรม' },
                { data: 'day', title: 'อายุ' },
                {
                    data: 'id',
                    title: 'Action',
                    render: function (data, type, row, meta) {
                        return `
                            <div class="btn-group btn-group-toggle">
                                <a href="/TmpSchedules/${row.id}/manage" class="btn btn-warning text-white m-2">Manage</a>
                            </div>
                        `;
                    }
                }
            ]
        });
    }

    function searchByText(input) {
        table.column(1).search(input).draw();
    }

    function onDelete(id) {
        if (!confirm('คุณต้องการลบข้อมูล Head นี้หรือไม่?')) return;

        $.ajax({
            url: `/api/TmpSchedules/${id}`,
            type: 'DELETE',
            success: function (res) {
                $.get('/api/TmpSchedules', function(response) {
                    getTable(response.data);
                });
            },
            error: function () {
                alert('เกิดข้อผิดพลาดในการลบข้อมูล');
            }
        });
    }
</script>
