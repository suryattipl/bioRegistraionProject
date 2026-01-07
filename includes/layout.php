<?php
// $pageContent must be set before including this file
require_once __DIR__ . "/header.php";
?>

<div class="container-fluid">
    <div class="row">
        <?php require_once __DIR__ . "/sidebar.php"; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-3">
            <?php require_once $pageContent; ?>
        </main>
    </div>
</div>

<?php require_once __DIR__ . "/footer.php"; ?>
