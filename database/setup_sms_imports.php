<?php
include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Create SMS imports table
$query = "CREATE TABLE IF NOT EXISTS sms_imports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expense_id INT NOT NULL,
    raw_sms TEXT NOT NULL,
    sms_sender VARCHAR(50),
    sms_date DATETIME,
    confidence INT DEFAULT 0,
    bank_name VARCHAR(100),
    merchant VARCHAR(200),
    import_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expense_id) REFERENCES expenses(id) ON DELETE CASCADE
)";

if($db->exec($query)) {
    echo "SMS imports table created successfully.\n";
} else {
    echo "Error creating SMS imports table.\n";
}

// Add indexes for better performance
$indexQueries = [
    "CREATE INDEX IF NOT EXISTS idx_sms_expense_id ON sms_imports(expense_id)",
    "CREATE INDEX IF NOT EXISTS idx_sms_import_date ON sms_imports(import_timestamp)",
    "CREATE INDEX IF NOT EXISTS idx_sms_bank ON sms_imports(bank_name)"
];

foreach($indexQueries as $indexQuery) {
    if($db->exec($indexQuery)) {
        echo "Index created successfully.\n";
    } else {
        echo "Error creating index.\n";
    }
}

echo "SMS imports database setup completed.\n";
?>