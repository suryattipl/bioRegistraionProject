<?php
// include_once("header.php");
include_once("config.php");
?>


<body>
    <!-- jQuery (already included) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables CSS & JS (already included) -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.min.css">
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>

<!-- DataTables Buttons extension (for Excel export) -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<!-- JSZip required for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- SheetJS (optional if you want another Excel method) -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>

<!-- Bootstrap CSS & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">

    <!-- Login Page -->
    <div id="loginPage" class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h1 style="color: #1f2937; margin-bottom: 0.5rem;">Bio Registration</h1>
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="text" id="emailInput" class="form-input" placeholder="admin@verification.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" id="passwordInput" class="form-input" placeholder="Enter password" required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.25rem; height: 1.25rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Sign In
                </button>
            </form>
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

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('emailInput').value;
            const password = document.getElementById('passwordInput').value;
                $.ajax({
                url: './api/api.php',
                type: 'POST',
                data: {
                    action: 'login',
                    username: email,
                    password: password
                },
                dataType: 'json',
                success: function(result) {
                    console.log(result);
                    if (result.success) {
                        sessionStorage.setItem('isLoggedIn', 'true');
                        // showDashboard();
                        showToast('success', 'Login Successful', 'Welcome to the Verification System');
                        window.location.href = 'dashboard.php';
                    } else {
                        showToast('error', 'Login Failed', 'Invalid email or password');
                    }
                },
                });                   
        });

        function showToast(type, title, message) {
            const toast = document.getElementById('toast');
            toast.className = `toast ${type} show`;
            document.getElementById('toastTitle').textContent = title;
            document.getElementById('toastMessage').textContent = message;

            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        }
    </script>
</body>

</html>