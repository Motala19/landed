<?php
// includes/audit_logger.php

function log_audit($user_id, $user_name, $role, $action, $requisition_id = null, $details = null) {
    global $conn;
    
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

    $stmt = $conn->prepare("
        INSERT INTO audit_logs 
        (user_id, user_name, role, action, requisition_id, details, ip_address) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("isssiss", 
        $user_id, 
        $user_name, 
        $role, 
        $action, 
        $requisition_id, 
        $details, 
        $ip_address
    );

    $stmt->execute();
}
?>