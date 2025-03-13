<?php
session_start();
require_once 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit('Unauthorized');
}

// Get JSON data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($data['message_id'])) {
    http_response_code(400);
    exit('Message ID required');
}

try {
    // Update message read timestamp
    $stmt = $pdo->prepare("
        UPDATE messages 
        SET read_at = NOW() 
        WHERE id = ? 
        AND recipient_id = ? 
        AND recipient_type = ? 
        AND read_at IS NULL
    ");
    
    $stmt->execute([
        $data['message_id'],
        $_SESSION['user_id'],
        $_SESSION['user_type']
    ]);
    
    http_response_code(200);
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    error_log("Error marking message as read: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to mark message as read']);
} 