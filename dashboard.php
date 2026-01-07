
<?php 
include "includes/header.php"; 
include "includes/sidebar.php"; 
?>

<body>
    <!-- <script src="dashboard.js"></script> -->
    <link rel="stylesheet" href="dashboard.css">

    <!--     <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script> -->


    <style>
        .card {
            border-radius: 15px;
        }

        .card h2 {
            font-size: 2rem;
        }


        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            /* margin-top: 1rem; */
        }

        .full-table {
            width: 98%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        .full-table th,
        .full-table td {
            padding: 0.75rem 1rem;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .full-table thead {
            background-color: #f9fafb;
            font-weight: 600;
            color: #374151;
        }

        .full-table tbody tr:hover {
            background-color: #f3f4f6;
        }
    </style>


    <!-- Dashboard -->
    <div id="dashboard" class="dashboard">
        <!-- Header -->
        


        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Controls -->
            <div class="controls-section container-fluid">
                <div class="row g-3 align-items-end">
                     <!-- Shift -->
                    <div class="col-12 col-md-3">
                        <div class="control-group">
                            <label class="form-label">Select Shift</label>
                            <select id="shiftSelect" class="form-select" onchange="updateDashboard()" required>
                                <option value="">Select Shift</option>
                            </select>
                        </div>
                    </div>

                    <!-- District -->
                    <div class="col-12 col-md-3">
                        <div class="control-group">
                            <label class="form-label">Select District</label>
                            <select id="districtselect" class="form-select" onchange="loadLocations()" required>
                                <option value="">Select District</option>
                                <?php
                                $districts = mysqli_query($con1, "SELECT DISTINCT districtname FROM candidate_details ORDER BY districtname");
                                while ($row = mysqli_fetch_assoc($districts)) {
                                    echo "<option value='{$row['districtname']}'>{$row['districtname']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="col-12 col-md-3">
                        <div class="control-group">
                            <label class="form-label">Select Venue</label>
                            <select id="locationselect" class="form-select" onchange="loadDates()" required>
                                <option value="">Select Venue</option>
                            </select>
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="col-12 col-md-3">
                        <div class="control-group">
                            <label class="form-label">Select Date</label>
                            <select id="areadate" class="form-select" onchange="loadShifts()" required>
                                <option value="">Select Date</option>
                            </select>
                        </div>
                    </div>

                   

                </div>
            </div>

            <!-- Stats Cards -->
            <div id="statsContainer" class="stats-grid"></div>

            <!-- Table Section -->
            <div id="tableSection" class="table-section" style="display: none; width: 100%;">
                <!-- <div class="table-header">
                    <div class="table-header-top">
                        

                    </div>
                </div> -->
                <div class="px-2 py-2">
                    <h2 style="color: #1f2937; display: flex; align-items: center; gap: 0.5rem;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem; color: #4f46e5;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Venue Details
                    </h2>
                    <!-- <p id="venueCount" style="color: #6b7280; margin-top: 0.25rem;"></p> -->
                </div>

                <div class="table-wrapper  py-3">
                    <table id="venueTable" class="full-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Venue Name</th>
                                <th>Called</th>
                                <th>Verified</th>
                                <th>Not Verified</th>
                                <th>Absent</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <div class="toast-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div class="toast-content">
            <h4 id="toastTitle"></h4>
            <p id="toastMessage"></p>
        </div>
    </div>

    <script>


        // Initialize
        window.onload = function() {
            document.getElementById('dashboard').classList.add('active');
            // Check if logged in
            /* if (sessionStorage.getItem('isLoggedIn') === 'true') {
                showDashboard();
            } */

            // Populate area dropdown
            /* const areaSelect = document.getElementById('districtselect');
            areas.forEach(area => {
                const option = document.createElement('option');
                option.value = area.id;
                option.textContent = area.name;
                areaSelect.appendChild(option);
            }); */
        };




        
        $(document).ready(function () {
                    
            const shiftOptions = `
                <option value="">Select Shift</option>
                <option value="Morning">Morning</option>
                <option value="Afternoon">Afternoon</option>
            `;
            $('#shiftSelect').html(shiftOptions).prop('disabled', false);
        });

        $('#districtselect').on('change', function() {
            const district = $(this).val();

            $('#locationselect').prop('disabled', true).html('<option value="">Select Location</option>');
            $('#areadate').prop('disabled', true).html('<option value="">Select Date</option>');
            // $('#shiftSelect').prop('disabled', true).html('<option value="">Select Shift</option>');

            if (!district) return;

            // Load locations for selected district
            $.getJSON('api.php', {
                action: 'getLocations',
                district
            }, function(data) {
                let options = '<option value="">Select Location</option>';
                data.forEach(row => {
                    options += `<option value="${row.wecentrecode}">${row.wecentre} (${row.wecentrecode})</option>`;
                });
                $('#locationselect').html(options).prop('disabled', false);
            });

            // Fetch venues for selected district
            updateDashboard();
        });


        // 2️⃣ When location changes → enable & load dates
        $('#locationselect').on('change', function() {
            const district = $('#districtselect').val();
            const location = $(this).val();

            $('#areadate').prop('disabled', true).html('<option value="">Select Date</option>');
            // $('#shiftSelect').prop('disabled', true).html('<option value="">Select Shift</option>');

            if (!location) return;

            // Load dates for this district & location
            $.getJSON('api.php', {
                action: 'getDates',
                district,
                location
            }, function(data) {
                let options = '<option value="">Select Date</option>';
                data.forEach(d => {
                    options += `<option value="${d}">${d}</option>`;
                });
                $('#areadate').html(options).prop('disabled', false);
            });

            // Fetch venues for district + location
            updateDashboard();
        });


        // 3️⃣ When date changes → enable & load shifts
        $('#areadate').on('change', function() {
            const district = $('#districtselect').val();
            const location = $('#locationselect').val();
            const dvdate = $(this).val();

            // $('#shiftSelect').prop('disabled', true).html('<option value="">Select Shift</option>');

            if (!dvdate) return;

            /* $.getJSON('api.php', { action: 'getShifts', district, location, dvdate }, function(data) {
                let options = '<option value="">Select Shift</option>';
                data.forEach(s => {
                    options += `<option value="${s}">${s}</option>`;
                });
                $('#shiftSelect').html(options).prop('disabled', false);
            }); */
            

            // Fetch venues for district + location + date
            updateDashboard();
        });


        // 4️⃣ When shift changes → final call to load full venue data
        $('#shiftSelect').on('change', function() {
            updateDashboard();
        });


        /* let venueTable = $('#venueTable').DataTable({
            processing: true,
            serverSide: false, // we are loading pre-aggregated data via AJAX (no paging on DB)
            searching: false,
            paging: false,
            info: false,
            columns: [
                { data: 'wecentrecode', title: 'Code' },
                { data: 'wecentre', title: 'Venue Name' },
                { data: 'totalCandidates', title: 'Called' },
                { data: 'verifiedCount', title: 'Verified', className: 'text-success fw-bold' },
                { data: 'notVerifiedCount', title: 'Not Verified', className: 'text-warning fw-bold' },
                { data: 'absentCount', title: 'Absent', className: 'text-danger fw-bold' }
            ]
        }); */

        let venueTable = $('#venueTable').DataTable({
            processing: true,
            serverSide: false,
            searching: true,
            paging: false,
            info: false,
            autoWidth: false,
            responsive: true,
            columns: [{
                    data: 'wecentrecode',
                    title: 'Code',
                    width: '10%'
                },
                {
                    data: 'wecentre',
                    title: 'Venue Name',
                    width: '40%'
                },
                {
                    data: 'totalCandidates',
                    title: 'Called',
                    width: '10%',
                    className: 'text-center'
                },
                {
                    data: 'verifiedCount',
                    title: 'Verified',
                    width: '10%',
                    className: 'text-success fw-bold text-center'
                },
                {
                    data: 'notVerifiedCount',
                    title: 'Not Verified',
                    width: '10%',
                    className: 'text-warning fw-bold text-center'
                },
                {
                    data: 'absentCount',
                    title: 'Absent',
                    width: '10%',
                    className: 'text-danger fw-bold text-center'
                },
                {
                    data: null,
                    title: 'Operators',
                    width: '10%',
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `<button class="btn btn-primary btn-sm view-officers-btn">View Operators</button>`;
                    }
                }
            ]
        });


        $('#venueTable').on('click', 'td.text-danger', function() {
            const rowData = venueTable.row($(this).closest('tr')).data();
            const venueCode = rowData.wecentrecode;
            const shiftTimings = $('#shiftSelect').val();


            // Confirm export
            if (!confirm(`Export absent candidates for ${rowData.wecentre}?`)) return;

            // Call backend to export
            window.location.href = `api.php?action=exportAbsent&wecentrecode=${venueCode}&shift=${shiftTimings}`;
        });

        $('#venueTable').on('click', '.view-officers-btn', function() {
            const rowData = venueTable.row($(this).closest('tr')).data();

            // You can customize these parameters based on your data structure
            // const district = rowData.districtname || '';  // if you have district in your data
            const district = $('#districtselect').val();
            const location = rowData.wecentrecode;
            const shiftTimings = $('#shiftSelect').val();


            // Redirect to another page with query parameters
            const url = `location_officers_list.php?district=${encodeURIComponent(district)}&location=${encodeURIComponent(location)}&shift=${encodeURIComponent(shiftTimings)}`;
            window.location.href = url;
        });


        function updateDashboard() {
            const selectedArea = $('#districtselect').val();
            const selectedShift = $('#shiftSelect').val();
            const locationselected = $('#locationselect').val();
            const areadate = $('#areadate').val();
            // const searchQuery = $('#searchInput').val().toLowerCase();

            // if (!selectedArea || !selectedShift || !locationselected || !areadate) return;
            if (!selectedArea) return;

            $.ajax({
                url: 'api.php',
                type: 'GET',
                data: {
                    action: 'getVenues',
                    area_id: selectedArea,
                    locationselected,
                    areadate,
                    shift_time: selectedShift
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Response:', response);

                    const stats = response.stats || {};
                    const venues = response.venues || [];

                    $('#statsContainer').html(`
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-2">Total Called</h5>
                            <h2 class="card-text fw-bold">${stats.totalCalled || 0}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-2">Total Verified</h5>
                            <h2 class="card-text fw-bold">${stats.totalVerified || 0}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-2">Total Not Verified</h5>
                            <h2 class="card-text fw-bold">${stats.totalNotVerified || 0}</h2>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-2">Total Absent</h5>
                            <h2 class="card-text fw-bold">${stats.totalAbsent || 0}</h2>
                        </div>
                    </div>
                </div>
            </div>

        `);


                    if (response.data) {
                        venueTable.clear().rows.add(response.data).draw();
                        $('#tableSection').show();
                    } else {
                        venueTable.clear().draw();
                        $('#tableSection').hide();
                    }

                    // $('#tableSection').show();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        }




        // Export Absent Candidates
        function exportAbsent(venueName, shift, venueId) {
            const venue = venues.find(v => v.id === venueId);
            if (!venue) return;

            const absentCandidates = shift === 'morning' ? venue.morningShift.absent : venue.afternoonShift.absent;

            if (absentCandidates.length === 0) {
                showToast('error', 'No Data', 'No absent candidates to export');
                return;
            }

            const data = absentCandidates.map((candidate, index) => ({
                'S. No.': index + 1,
                'Name': candidate.name,
                'Application Number': candidate.applicationNumber,
                'Contact Number': candidate.contactNumber,
                'Email': candidate.email,
            }));

            const ws = XLSX.utils.json_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Absent Candidates');

            const fileName = `${venueName}_${shift}_absent_candidates.xlsx`;
            XLSX.writeFile(wb, fileName);

            showToast('success', `Downloading ${shift} shift absent candidates`,
                `${absentCandidates.length} candidate(s) exported from ${venueName}`);
        }

        // Export All Absent
        function exportAllAbsent() {
            const selectedArea = document.getElementById('districtselect').value;
            const allVenues = venues.filter(v => v.areaId === selectedArea);

            const allAbsent = [];
            allVenues.forEach(venue => {
                venue.morningShift.absent.forEach(candidate => {
                    allAbsent.push({
                        'Venue': venue.name,
                        'Shift': 'Morning',
                        'Name': candidate.name,
                        'Application Number': candidate.applicationNumber,
                        'Contact Number': candidate.contactNumber,
                        'Email': candidate.email,
                    });
                });
                venue.afternoonShift.absent.forEach(candidate => {
                    allAbsent.push({
                        'Venue': venue.name,
                        'Shift': 'Afternoon',
                        'Name': candidate.name,
                        'Application Number': candidate.applicationNumber,
                        'Contact Number': candidate.contactNumber,
                        'Email': candidate.email,
                    });
                });
            });

            if (allAbsent.length === 0) {
                showToast('error', 'No Data', 'No absent candidates to export');
                return;
            }

            const ws = XLSX.utils.json_to_sheet(allAbsent);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'All Absent Candidates');

            XLSX.writeFile(wb, 'all_absent_candidates.xlsx');

            showToast('success', 'Downloading all absent candidates',
                `${allAbsent.length} candidate(s) from ${allVenues.length} venue(s) exported`);
        }

        // Show Toast
        function showToast(type, title, message) {
            const toast = document.getElementById('toast');
            toast.className = `toast ${type} show`;
            document.getElementById('toastTitle').textContent = title;
            document.getElementById('toastMessage').textContent = message;

            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }

        function logout() {
            // Optional: clear browser session data too
            sessionStorage.clear();
            window.location.href = "logout.php";
        }
    </script>
</body>

</html>