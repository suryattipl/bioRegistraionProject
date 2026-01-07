<!DOCTYPE html>
<?php
session_start();
error_reporting(0); 
include_once("config.php");

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Registration System</title>


<link rel="stylesheet" href="includes/layout1.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables 2.x -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.min.css">
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>

<!-- Buttons 3.x (MATCHES DataTables 2.x) -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.print.min.js"></script>

<!-- JSZip (required for Excel) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">



</head>

<body>
    
<header class="app-header">
    <div class="header-left">
        <i class="bi bi-shield-check"></i>
        <span>BIO REGISTRATION</span>
    </div>

    <div class="header-right">
        <a href="logout.php" class="btn btn-outline">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</header>
</body>

<script>
            function logout() {
            // Optional: clear browser session data too
            sessionStorage.clear();
            window.location.href = "logout.php";
        }
</script>
