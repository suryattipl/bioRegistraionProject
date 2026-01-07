<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <ul class="sidebar-menu">
        <li class="<?= $currentPage == 'dashboard.php' ? 'active' : '' ?>">
            <a href="dashboard.php">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
        </li>

        <li class="<?= $currentPage == 'project.php' ? 'active' : '' ?>">
            <a href="project.php">
                <i class="bi bi-folder"></i>
                Project
            </a>
        </li>

        <li class="<?= $currentPage == 'profile.php' ? 'active' : '' ?>">
            <a href="profile.php">
                <i class="bi bi-person"></i>
                Profile
            </a>
        </li>
    </ul>
</aside>
