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

function send_email_notification($email, $subject, $message) {
    $from = "no-reply@midrandprimary.co.za";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Midrand Primary <$from>\r\n";
    $headers .= "Reply-To: $from\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // 5th parameter helps on many hosts
    return mail($email, $subject, $message, $headers, "-f$from");
}
?>