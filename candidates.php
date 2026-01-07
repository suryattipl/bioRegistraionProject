<?php
session_start();

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

<div class="card shadow-sm">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table id="candidatesTable" class="display table table-bordered table-hover mb-0">
            </table>
        </div>
    </div>
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
                            while($row = mysqli_fetch_assoc($result)) {
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {

    $('select[name="project_id"]').on('change', function () {

    const projectId = $(this).val();
    const $shiftSection  = $('#shiftSection');
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
        success: function (res) {

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
                        <input class="form-check-input shift-checkbox"
                               type="checkbox"
                               name="shifts[]"
                               value="morning"
                               id="shiftMorning">
                        <label class="form-check-label" for="shiftMorning">
                            Morning Shift
                        </label>
                    </div>`;
            }

            if (evening_shift == 1) {
                html += `
                    <div class="form-check">
                        <input class="form-check-input shift-checkbox"
                               type="checkbox"
                               name="shifts[]"
                               value="evening"
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

    // Step 1: Upload Excel
    $('#uploadExcelForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('action', 'uploadExcel');

        $.ajax({
            url: './api/candidates_api.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                if(!res.success) return alert(res.message);

                // Show mapping form
                $('#uploadExcelForm').addClass('d-none');
                $('#mappingForm').removeClass('d-none');

                // Build mapping table
                let html = '<table class="table table-bordered"><tr><th>Excel Column</th><th>Map to DB Column</th></tr>';
                res.headers.forEach(header => {
                    html += `<tr>
                                <td>${header}</td>
                                <td>
                                    <select name="mapping[${header}]" class="form-select">
                                        <option value="">-- Skip --</option>`;
                    res.db_columns.forEach(col => {
                        html += `<option value="${col}">${col}</option>`;
                    });
                    html += `</select>
                                </td>
                             </tr>`;
                });
                html += `<input type="hidden" name="project_id" value="${res.project_id}">`;
                html += '</table>';
                $('#mappingContainer').html(html);
            }
        });
    });





    // Step 2: Import mapped data
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
                if(res.success) {
                    $('#importCandidateModal').modal('hide');
                    location.reload();
                }
            }
        });
    });

});
</script>
