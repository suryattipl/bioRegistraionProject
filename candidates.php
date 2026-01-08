<?php
include "includes/include1.php";
include "includes/toast.php";
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;
?>

<body>

    <div class="d-flex align-items-center mb-3">
        <h4 class="mb-0">Candidates</h4>
        <button
            class="btn btn-sm btn-primary ms-auto"
            data-bs-toggle="modal"
            data-bs-target="#importCandidateModal">
            <i class="bi bi-plus"></i> Import Candidates
        </button>
    </div>

    <!-- Import Candidate Modal -->
    <div class="modal fade" id="importCandidateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Step 1: Upload Excel -->
                <form id="uploadExcelForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5>Upload Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label">Select Project</label>
                            <!-- <select name="project_id" class="form-select" required>
                            <option value="">-- Select Project --</option>
                            <?php
                            $query = "SELECT id, projectname FROM projects ORDER BY projectname ASC";
                            $result = mysqli_query($con1, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['projectname']) . '</option>';
                            }
                            ?>
                        </select> -->
                            <select name="project_id" id="projectSelect" class="form-select" required>
                                <option value="">-- Select Project --</option>
                                <?php
                                $query = "SELECT id, projectname FROM projects ORDER BY projectname ASC";
                                $result = mysqli_query($con1, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['projectname']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="mb-2 d-none" id="shiftSection">
                            <label class="form-label d-block">Shift</label>

                            <div id="shiftContainer"></div>

                            <div class="text-danger small d-none" id="shiftError">
                                Please select at least one shift
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Select Excel File</label>
                            <input type="file" name="excel_file" accept=".xlsx,.xls" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Next</button>
                    </div>
                </form>

                <!-- Step 2: Mapping -->
                <form id="mappingForm" class="d-none">
                    <div class="modal-header">
                        <h5>Map Excel Columns</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="mappingContainer"></div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Import Data</button>
                    </div>
                </form>



            </div>

        </div>


    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4">
                    <label class="form-label">Project</label>
                    <select id="filterProject" class="form-select">
                        <option value="">-- Select Project --</option>
                        <?php
                        $res = mysqli_query($con1, "SELECT id, projectname FROM projects ORDER BY projectname");
                        while ($r = mysqli_fetch_assoc($res)) {
                            echo "<option value='{$r['id']}'>{$r['projectname']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4 d-none" id="shiftFilterBox">
                    <label class="form-label">Shift</label>
                    <select id="filterShift" class="form-select">
                        <option value="">-- All Shifts --</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
            <table id="candidatesTable" class="table table-bordered table-hover w-100">
                <thead>
                    <tr>
                        <th>RegID</th>
                        <th>Name</th>
                        <th>DOB</th>
                        <th>Gender</th>
                        <th>Community</th>
                        <th>Center Code</th>
                        <th>Center</th>
                        <th>District</th>
                        <th>District Code</th>
                        <th>Post</th>
                        <th>Exam Date</th>
                        <th>Project Shift</th>
                    </tr>
                </thead>
            </table>
        </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <script>
        $(document).ready(function() {

            $('select[name="project_id"]').on('change', function() {

                const projectId = $(this).val();
                const $shiftSection = $('#shiftSection');
                const $shiftContainer = $('#shiftContainer');

                // Reset everything
                $shiftSection.addClass('d-none');
                $shiftContainer.html('');

                if (!projectId) return;

                $.ajax({
                    url: './api/candidates_api.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'getProjectDetails',
                        project_id: projectId
                    },
                    success: function(res) {

                        if (!res.success) return;

                        const {
                            morning_shift,
                            evening_shift,
                            same_candidates_in_shifts
                        } = res.data;

                        if (
                            same_candidates_in_shifts != 2 ||
                            (morning_shift != 1 && evening_shift != 1)
                        ) {
                            return;
                        }

                        $shiftSection.removeClass('d-none');

                        let html = '';

                        if (morning_shift == 1) {
                            html += `
                            <div class="form-check">
                                <input class="form-check-input"
                                    type="radio"
                                    name="shift"
                                    value="1"
                                    id="shiftMorning">
                                <label class="form-check-label" for="shiftMorning">
                                    Morning Shift
                                </label>
                            </div>`;
                        }

                        if (evening_shift == 1) {
                            html += `
                            <div class="form-check">
                                <input class="form-check-input"
                                    type="radio"
                                    name="shift"
                                    value="2"
                                    id="shiftEvening">
                                <label class="form-check-label" for="shiftEvening">
                                    Evening Shift
                                </label>
                            </div>`;
                        }

                        $shiftContainer.html(html);
                    }
                });
            });


            $('#uploadExcelForm').on('submit', function(e) {
                e.preventDefault();

                const projectId = $('#projectSelect').val();
                const shiftVisible = !$('#shiftSection').hasClass('d-none');
                const shiftSelected = $('input[name="shift"]:checked').length;
                const fileSelected = $('input[name="excel_file"]').val();

                $('#shiftError').addClass('d-none');

                if (!projectId) {
                    alert('Please select a project');
                    return;
                }

                if (shiftVisible && shiftSelected === 0) {
                    $('#shiftError').removeClass('d-none');
                    return;
                }

                if (!fileSelected) {
                    alert('Please select an Excel file');
                    return;
                }

                let formData = new FormData(this);
                formData.append('action', 'uploadExcel');

                if (shiftSelected) {
                    formData.append('shift', $('input[name="shift"]:checked').val());
                }

                $.ajax({
                    url: './api/candidates_api.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(res) {
                        if (!res.success) {
                            alert(res.message);
                            return;
                        }

                        $('#uploadExcelForm').addClass('d-none');
                        $('#mappingForm').removeClass('d-none');

                        let html = '<table class="table table-bordered">';
                        html += '<tr><th>Excel Column</th><th>Map to DB Column</th></tr>';

                        res.headers.forEach(header => {
                            html += `
                    <tr>
                        <td>${header}</td>
                        <td>
                            <select name="mapping[${header}]" class="form-select">
                                <option value="">-- Skip --</option>`;
                            res.db_columns.forEach(col => {
                                html += `<option value="${col}">${col}</option>`;
                            });
                            html += `
                            </select>
                        </td>
                    </tr>`;
                        });

                        html += `<input type="hidden" name="project_id" value="${res.project_id}">`;
                        if (res.shift) {
                            html += `<input type="hidden" name="shift" value="${res.shift}">`;
                        }
                        html += '</table>';

                        $('#mappingContainer').html(html);
                    }
                });
            });



            // Import mapped data
            $('#mappingForm').on('submit', function(e) {
                e.preventDefault();
                let data = $(this).serialize() + '&action=importData';

                $.ajax({
                    url: './api/candidates_api.php',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(res) {
                        alert(res.message);
                        if (res.success) {
                            $('#importCandidateModal').modal('hide');
                            location.reload();
                        }
                    }
                });
            });



            function reloadTable() {
                if ($.fn.DataTable.isDataTable('#candidatesTable')) {
                    table.destroy();
                }

                table = $('#candidatesTable').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    scrollX: true,
                     autoWidth: false,
                     dom: 'Bfrtip',       // ✅ REQUIRED for buttons
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Export Excel',
                            title: 'Candidates'
                        }
                    ],
                     
                    ajax: {
                        url: './api/candidates_api.php',
                        type: 'POST',
                        data: function(d) {
                            d.action = 'getCandidates';
                            d.project_id = $('#filterProject').val();
                            d.shift = $('#filterShift').val();
                        }
                    },
                    columns: [{
                            data: 'Regid'
                        },
                        {
                            data: 'name'
                        },
                        {
                            data: 'BirthDate'
                        },
                        {
                            data: 'gender'
                        },
                        {
                            data: 'Community'
                        },
                        {
                            data: 'center_code'
                        },
                        {
                            data: 'center_name'
                        },
                        {
                            data: 'districname'
                        },
                        {
                            data: 'districcode'
                        },
                        {
                            data: 'post'
                        },
                        {
                            data: 'exam_date'
                        },
                        {
                            data: 'project_shift',
                            render: function(d) {
                                if (d == 1) return 'Morning';
                                if (d == 2) return 'Evening';
                                return '-';
                            }
                        }
                    ]
                });
            }

            $('#filterProject, #filterShift').on('change', reloadTable);


            $('#filterProject').on('change', function() {

                const projectId = $(this).val();

                // reset shift UI + value
                $('#filterShift')
                    .html('<option value="">-- All Shifts --</option>')
                    .val('');

                $('#shiftFilterBox').addClass('d-none');

                table.clear().draw();

                if (!projectId) {
                    table.ajax.reload(null, true); // reset paging
                    return;
                }

                $.post('./api/candidates_api.php', {
                    action: 'getProjectDetails',
                    project_id: projectId
                }, function(res) {

                    if (!res.success) {
                        table.ajax.reload(null, true);
                        return;
                    }

                    const data = res.data;
                    let hasAnyShift = false;
                    let html = '<option value="">-- All Shifts --</option>';

                    if (data.morning_shift == 1) {
                        html += '<option value="1">Morning</option>';
                        hasAnyShift = true;
                    }

                    if (data.evening_shift == 1) {
                        html += '<option value="2">Evening</option>';
                        hasAnyShift = true;
                    }

                    // show shift filter ONLY if allowed
                    if (data.same_candidates_in_shifts == 2 && hasAnyShift) {
                        $('#shiftFilterBox').removeClass('d-none');
                        $('#filterShift').html(html);
                    }

                    // ✅ reload fresh project data
                    table.ajax.reload(null, true);

                }, 'json');
            });



            // reload table when shift changes
            $('#filterShift').on('change', function() {
                table.ajax.reload();
            });


        });
    </script>