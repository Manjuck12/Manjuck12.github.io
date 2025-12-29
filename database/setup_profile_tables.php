<?php
include_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Create user_profile table
    $query1 = "CREATE TABLE IF NOT EXISTS user_profile (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        designation VARCHAR(255) DEFAULT NULL,
        salary DECIMAL(10,2) DEFAULT 0.00,
        extra_amount DECIMAL(10,2) DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_user_profile (user_id)
    )";
    
    $stmt1 = $db->prepare($query1);
    $stmt1->execute();
    echo "user_profile table created successfully\n";
    
    // Create daily_extra_amounts table
    $query2 = "CREATE TABLE IF NOT EXISTS daily_extra_amounts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        description TEXT DEFAULT NULL,
        date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_user_date (user_id, date)
    )";
    
    $stmt2 = $db->prepare($query2);
    $stmt2->execute();
    echo "daily_extra_amounts table created successfully\n";
    
    echo "All profile tables created successfully!\n";
    
} catch(PDOException $exception) {
    echo "Error creating tables: " . $exception->getMessage() . "\n";
}
?>