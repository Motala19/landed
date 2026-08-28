<?php
// Session timeout - 30 minutes
$timeout_duration = 30 * 60; // 30 minutes in seconds

if (isset($_SESSION['user_id'])) {

    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
        // Session expired
        session_unset();
        session_destroy();
        header("Location: login.php?error=" . urlencode("Your session has expired. Please login again."));
        exit;
    }

    // Update last activity time
    $_SESSION['last_activity'] = time();
}
?>