<!-- <aside> -->
<style>
    /* Sidebar container */
.sidebar {
    width: 230px;
    height: calc(100vh - 70px);
    background-color: #f8fafc;
    border-right: 1px solid #e5e7eb;
    padding-top: 20px;
    position: fixed;
    top: 70px;        /* below header */
    left: 0;
}


/* Menu list */
.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

/* Menu items */
.sidebar-menu li {
    margin-bottom: 6px;
}

/* Menu links */
.sidebar-menu li a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px;
    color: #1f2937;
    text-decoration: none;
    font-weight: 500;
    border-radius: 6px;
    margin: 0 10px;
    transition: background-color 0.2s, color 0.2s;
}

/* Hover effect */
.sidebar-menu li a:hover {
    background-color: #e0e7ff;
    color: #3730a3;
}

/* Active menu */
.sidebar-menu li.active a {
    background-color: #4f46e5;
    color: #ffffff;
}

/* Icons */
.sidebar-menu i {
    font-size: 1.1rem;
}

</style>  

<div class="sidebar">
    <ul class="sidebar-menu">
        <li class="active">
            <a href="dashboard.php">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
        </li>
        <li>
            <a href="project.php">
                <i class="bi bi-person"></i>
                Project
            </a>
        </li>
        <!-- <li>
            <a href="home.php">
                <i class="bi bi-house"></i>
                Home
            </a>
        </li> -->
    </ul>
</div>

<!-- </aside> -->
