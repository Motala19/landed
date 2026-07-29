<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? '';
?>

<!-- Sidebar -->
<div class="col-lg-2 sidebar text-white" 
     style="min-height: 100vh; background-color: #0B1D39; border-right: 3px solid #12355B;">

    <!-- Logo Header -->
    <div class="p-4 border-bottom" style="border-color: #1C3A63;">
        <div class="text-center">
            <img src="assets/images/logo.png" alt="MPS Logo" 
                 style="width: 64px; height: 64px; object-fit: contain; margin-bottom: 0.4rem;">
            <h5 class="mb-1 fw-semibold" style="font-size: 1.1rem; color: #E0E6ED;">Midrand Primary</h5>
            <small style="font-size: 0.8rem; color: #A0AEC0; line-height: 1;">Requisition Management</small>
        </div>
    </div>

    <!-- Navigation -->
    <div class="p-3 pt-3">
        <ul class="nav flex-column">

            <!-- ADMIN DASHBOARD (only for Admin) -->
            <?php if ($role === 'admin'): ?>
            <li class="nav-item">
                <a href="dashboard.php" 
                   class="nav-link d-flex align-items-center px-2 py-2 <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-gauge-high me-2"></i> 
                    <span>Admin Dashboard</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- PRINCIPAL DASHBOARD (only Principal) -->
            <?php if ($role === 'principal'): ?>
            <li class="nav-item">
                <a href="principal-dashboard.php" 
                   class="nav-link d-flex align-items-center px-2 py-2 <?= ($currentPage == 'principal-dashboard.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-user-tie me-2"></i> 
                    <span>Principal Dashboard</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- FINANCE DASHBOARD (only Finance) -->
            <?php if ($role === 'finance'): ?>
            <li class="nav-item">
                <a href="finance-dashboard.php" 
                   class="nav-link d-flex align-items-center px-2 py-2 <?= ($currentPage == 'finance-dashboard.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-sack-dollar me-2"></i> 
                    <span>Finance Dashboard</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- TREASURER DASHBOARD (only Treasurer) -->
            <?php if ($role === 'treasurer'): ?>
            <li class="nav-item">
                <a href="treasurer-dashboard.php" 
                   class="nav-link d-flex align-items-center px-2 py-2 <?= ($currentPage == 'treasurer-dashboard.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-hand-holding-dollar me-2"></i> 
                    <span>Treasurer Dashboard</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- My Requisitions (everyone) -->
            <li class="nav-item">
                <a href="requisitions.php" 
                   class="nav-link d-flex align-items-center px-2 py-2 <?= ($currentPage == 'requisitions.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-file-invoice me-2"></i> 
                    <span>My Requisitions</span>
                </a>
            </li>

            <!-- Manage Users (Finance + Admin) -->
            <?php if (in_array($role, ['finance', 'admin'])): ?>
            <li class="nav-item">
                <a href="manage-users.php" 
                   class="nav-link d-flex align-items-center px-2 py-2 <?= ($currentPage == 'manage-users.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-user-gear me-2"></i> 
                    <span>Manage Users</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Reports (everyone for now, or restrict if you want) -->
            <li class="nav-item">
                <a href="reports.php" 
                   class="nav-link d-flex align-items-center px-2 py-2 <?= ($currentPage == 'reports.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-chart-line me-2"></i> 
                    <span>Reports</span>
                </a>
            </li>

            <hr style="border-color: #1C3A63;" class="my-3">

            <!-- Logout -->
            <li class="nav-item">
                <a href="logout.php" class="logout-btn d-flex align-items-center px-3 py-2">
                    <i class="fa-solid fa-right-from-bracket me-2"></i> 
                    <span>Logout</span>
                </a>
            </li>

        </ul>
    </div>

    <div class="sidebar-footer text-center mt-auto p-3">
        <small class="footer-brand">
            <footer class="text-center copyright-footer">
                <small>&copy; 2026 MMSolutions. All rights reserved.</small>
            </footer>
        </small>
    </div>

</div>

<!-- Custom Styles -->
<style>
    .sidebar .nav-link {
        color: #E0E6ED;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        border-radius: 6px;
    }
    .sidebar .nav-link:hover {
        background-color: rgba(255,255,255,0.08);
        color: #FFFFFF;
        transform: translateX(4px);
    }
    .sidebar .nav-link.active {
        background-color: rgba(255,255,255,0.12);
        font-weight: 600;
    }
    .sidebar .nav-item {
        margin-bottom: 0.4rem;
    }

    .logout-btn {
        background: linear-gradient(90deg, #E53E3E, #C53030);
        color: #fff !important;
        font-weight: 600;
        border-radius: 30px;
        transition: all 0.3s ease;
    }
    .logout-btn:hover {
        background: linear-gradient(90deg, #C53030, #9B2C2C);
        transform: scale(1.05);
        text-decoration: none;
    }
   .sidebar {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.sidebar-footer {
    margin-top: auto;
    padding: 10px;
    border-top: 1px solid #1C3A63;
}

.sidebar-footer .footer-brand {
    font-style: italic;
    font-size: 0.75rem;
    color: #A0AEC0;
    letter-spacing: 0.5px;
}
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">