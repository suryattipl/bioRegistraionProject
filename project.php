<?php
include "includes/include1.php";
include "includes/toast.php";
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

$shiftResult = mysqli_query($con1, "SELECT id, exam_shifts FROM shifts");
?>

<body>
    <link rel="stylesheet" href="project.css">


    <div class="d-flex align-items-center mb-3">
        <h4 class="mb-0">Projects</h4>
        <button
            class="btn btn-sm btn-primary ms-auto"
            data-bs-toggle="modal"
            data-bs-target="#createProjectModal">
            <i class="bi bi-plus"></i> Create Project
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="projectsTable" class="display table table-bordered table-hover mb-0">
                </table>
            </div>
        </div>
    </div>


    <div class="modal fade" id="createProjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="createProjectForm">
                    <div class="modal-header">
                        <h5>Create Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="created_by" value="<?= htmlspecialchars($user_id) ?>">
                        <div class="mb-2">
                            <label class="form-label">Project Name</label>
                            <input type="text" name="projectname" class="form-control" required>
                            <div class="invalid-feedback">Project name is required</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Project Date</label>
                            <input type="date" name="projectdate" class="form-control" required>
                            <div class="invalid-feedback">Project date is required</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label d-block">Shift</label>

                            <?php while ($row = mysqli_fetch_assoc($shiftResult)) { ?>
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input shift-check"
                                        type="checkbox"
                                        name="shift_ids[]"
                                        value="<?= $row['id'] ?>"
                                        id="shift_<?= $row['id'] ?>">
                                    <label class="form-check-label" for="shift_<?= $row['id'] ?>">
                                        <?= htmlspecialchars($row['exam_shifts']) ?>
                                    </label>
                                </div>
                            <?php } ?>

                            <div class="text-danger small d-none" id="shiftError">
                                Please select at least one shift
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label d-block">Same Candidates for both shifts?</label>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input same-candidates" type="radio"
                                    name="same_candidates" value="1" id="same_yes">
                                <label class="form-check-label" for="same_yes">Yes</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input same-candidates" type="radio"
                                    name="same_candidates" value="2" id="same_no">
                                <label class="form-check-label" for="same_no">No</label>
                            </div>

                            <div class="text-danger small d-none" id="sameCandidatesError">
                                Please select Yes or No
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProjectModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editProjectForm" novalidate>
                    <input type="hidden" name="id" id="edit_id">

                    <div class="modal-header">
                        <h5>Edit Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-2">
                            <label class="form-label">Project Name</label>
                            <input type="text" name="projectname" id="edit_projectname" class="form-control" required>
                            <div class="invalid-feedback">Project name is required</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Project Date</label>
                            <input type="date" name="projectdate" id="edit_projectdate" class="form-control" required>
                            <div class="invalid-feedback">Project date is required</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label d-block">Shift</label>

                            <?php
                            $shiftRes = mysqli_query($con1, "SELECT id, exam_shifts FROM shifts");
                            while ($s = mysqli_fetch_assoc($shiftRes)) {
                            ?>
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input edit-shift-check"
                                        type="checkbox"
                                        name="shift_ids[]"
                                        value="<?= $s['id'] ?>"
                                        id="edit_shift_<?= $s['id'] ?>">
                                    <label class="form-check-label" for="edit_shift_<?= $s['id'] ?>">
                                        <?= htmlspecialchars($s['exam_shifts']) ?>
                                    </label>
                                </div>
                            <?php } ?>

                            <div class="text-danger small d-none" id="editShiftError">
                                Please select at least one shift
                            </div>
                        </div>
                        
                        <div class="mb-2">
    <label class="form-label d-block">Same Candidates for both shifts?</label>

    <div class="form-check form-check-inline">
        <input class="form-check-input edit-same-candidates" type="radio"
               name="same_candidates" value="1" id="edit_same_yes">
        <label class="form-check-label" for="edit_same_yes">Yes</label>
    </div>

    <div class="form-check form-check-inline">
        <input class="form-check-input edit-same-candidates" type="radio"
               name="same_candidates" value="2" id="edit_same_no">
        <label class="form-check-label" for="edit_same_no">No</label>
    </div>

    <div class="text-danger small d-none" id="editSameCandidatesError">
        Please select Yes or No
    </div>
</div>


                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteProjectForm">
                    <input type="hidden" name="id" id="delete_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Delete Project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <p>Are you sure you want to delete this project?</p>
                        <p><strong id="delete_projectname"></strong></p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
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

            const table = $('#projectsTable').DataTable({
                ajax: "./api/api.php?action=getProjects",
                columns: [{
                        data: null, // Use null because it's not from data
                        render: function(data, type, row, meta) {
                            return meta.row + 1; // Serial number: row index + 1
                        },
                        title: "S.No" // Optional header title
                    },
                    {
                        data: "projectname",
                        title: "Project Name"
                    },
                    {
                        data: "projectdates",
                        title: "Project Date"
                    },
                    {
                        data: "morning_shift",
                        title: "Morning Shift",
                        render: function(data) {
                            return data == 1 ? 'Yes' : 'No';
                        }
                    },
                    {
                        data: "evening_shift",
                        title: "Evening Shift",
                        render: function(data) {
                            return data == 1 ? 'Yes' : 'No';
                        }
                    },
                    {
                        data: "created_by_name",
                        title: "Created By"
                    },
                    {
                        data: "create_date_time",
                        title: "Created At"
                    },

                    {
                        data: null,
                        title: "Actions",
                        render: function(row) {
                            return `
                                <button class="btn btn-sm btn-warning editBtn"
                                    data-id="${row.ID}"
                                    data-name="${row.projectname}"
                                    data-date="${row.projectdates}"><i class="bi bi-pencil-square"></i></button>
                                <button class="btn btn-sm btn-danger deleteBtn"
                                    data-id="${row.ID}"
                                    data-name="${row.projectname}"><i class="bi bi-trash"></i></button>
                            `;
                        }
                    }


                ]
            });


            $('#createProjectForm').on('submit', function(e) {
                e.preventDefault();

                let valid = true;
                $('#shiftError').addClass('d-none');
                $('#sameCandidatesError').addClass('d-none');

                this.classList.add('was-validated');
                if (!$('.shift-check:checked').length) {
                    $('#shiftError').removeClass('d-none');
                    valid = false;
                }

                if (!$('input[name="same_candidates"]:checked').length) {
                    $('#sameCandidatesError').removeClass('d-none');
                    valid = false;
                }

                if (!this.checkValidity() || !valid) return;

                $.ajax({
                    url: './api/api.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=createProject',

                    success: function(res) {

                        /*  const modalEl = document.getElementById('createProjectModal');
                         bootstrap.Modal.getInstance(modalEl)?.hide(); */
                        const modalEl = document.getElementById('createProjectModal');
                        const modal = bootstrap.Modal.getInstance(modalEl) ||
                            new bootstrap.Modal(modalEl);
                        modal.hide();

                        $('#createProjectForm')[0].reset();
                        $('.shift-check').prop('checked', false);
                        $('#createProjectForm').removeClass('was-validated');
                        $('#shiftError').addClass('d-none');

                        table.ajax.reload(null, false);
                        showToast("Project Created successfully", "success");
                    },

                    error: function(xhr) {
                        showToast("Failed to create project: " + xhr.responseText, "error");
                    }
                });
            });



            $(document).on('click', '.editBtn', function() {
                const rowData = table.row($(this).parents('tr')).data();

                $('#edit_id').val(rowData.ID);
                $('#edit_projectname').val(rowData.projectname);
                $('#edit_projectdate').val(rowData.projectdates);

                $('.edit-shift-check').prop('checked', false);

                if (rowData.morning_shift == 1) {
                    $('#edit_shift_1').prop('checked', true); 
                }

                if (rowData.evening_shift == 1) {
                    $('#edit_shift_2').prop('checked', true); 
                }

                $('.edit-same-candidates').prop('checked', false);

    if (rowData.same_candidates == 1) {
        $('#edit_same_yes').prop('checked', true);
    } else if (rowData.same_candidates == 2) {
        $('#edit_same_no').prop('checked', true);
    }

                const modal = new bootstrap.Modal(
                    document.getElementById('editProjectModal')
                );
                modal.show();
            });


            $('#editProjectForm').on('submit', function(e) {
                e.preventDefault();
                let valid = true;
                $('#editShiftError').addClass('d-none');

                // Validate that a shift is selected
                if (!$('.edit-shift-check:checked').length) {
                    $('#editShiftError').removeClass('d-none');
                    valid = false;
                }

                if (!$('.'))

                    if (!this.checkValidity() || !valid) return;

                $.ajax({
                    url: './api/api.php',
                    type: 'POST',
                    data: $(this).serialize() + '&action=updateProject',
                    success: function() {
                        const modalEl = document.getElementById('editProjectModal');
                        bootstrap.Modal.getInstance(modalEl).hide();

                        $('#editProjectForm')[0].reset();
                        $('.edit-shift-check').prop('checked', false);

                        table.ajax.reload(null, false); // reload DataTable
                        showToast("Project Updated successfully", "success");
                    },
                    error: function(xhr) {
                        showToast("Something went wrong: " + xhr.responseText, "error");
                    }
                });
            });


            // Open delete modal and set values
            $(document).on('click', '.deleteBtn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                $('#delete_id').val(id);
                $('#delete_projectname').text(name);

                new bootstrap.Modal(document.getElementById('deleteProjectModal')).show();
            });

            // Submit delete form via AJAX
            $('#deleteProjectForm').on('submit', function(e) {
                e.preventDefault();

                const id = $('#delete_id').val();

                $.ajax({
                    url: './api/api.php',
                    type: 'POST',
                    data: {
                        id: id,
                        action: 'deleteProject'
                    },
                    success: function(res) {
                        const modalEl = document.getElementById('deleteProjectModal');
                        bootstrap.Modal.getInstance(modalEl).hide();

                        table.ajax.reload(null, false); // Reload DataTable
                        showToast("Project deleted successfully", "success");
                    },
                    error: function(xhr) {
                        showToast("Failed to delete project: " + xhr.responseText, "error");
                    }
                });
            });



        });
    </script>


</body>