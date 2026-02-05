<?php
require_once '../model/db.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    $sql = file_get_contents('../database/database.sql');
    if ($sql) {
        $conn->exec($sql);
        echo "Table created successfully.";
    } else {
        echo "Error: Could not read SQL file.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
