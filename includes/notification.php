<?php
// includes/notification.php

function send_notification($user_id, $title, $message, $type = 'info', $requisition_id = null) {
    global $conn;
    
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, title, message, type, requisition_id) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param("isssi", $user_id, $title, $message, $type, $requisition_id);
    $stmt->execute();
}

// Optional: Send email too
function send_email_notification($email, $subject, $message) {
    $headers = "From: no-reply@midrandprimary.co.za\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    mail($email, $subject, $message, $headers);
}
?>