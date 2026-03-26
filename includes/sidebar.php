<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="col-lg-2 sidebar p-3">

    <h5 class="mb-4 text-white">Midrand Primary</h5>

    <ul class="nav flex-column">

        <!-- REQUISITIONS -->
        <li class="nav-item mb-2">
            <a href="requisitions.php" 
               class="nav-link <?php echo ($currentPage == 'requisitions.php') ? 'active' : ''; ?>">
                My Requisitions
            </a>
        </li>

        <!-- QUOTES -->
        <li class="nav-item mb-2">
            <a href="quotes.php" 
               class="nav-link <?php echo ($currentPage == 'quotes.php') ? 'active' : ''; ?>">
               My Quotes
            </a>
        </li>

        <hr class="text-white">

        <!-- LOGOUT -->
        <li class="nav-item">
            <a href="logout.php" class="nav-link text-danger">
                Logout
            </a>
        </li>

    </ul>

</div>