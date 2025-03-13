<?php
require_once 'includes/config.php';

try {
    // Get table structure
    $stmt = $pdo->prepare("SHOW CREATE TABLE jobs");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    print_r($result);
    echo "</pre>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 