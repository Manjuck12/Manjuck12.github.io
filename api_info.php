<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// API Information endpoint
$api_info = [
    "message" => "Expense Tracker API",
    "version" => "1.0.0",
    "status" => "active",
    "endpoints" => [
        "POST /api/auth/login.php" => "User login",
        "POST /api/auth/register.php" => "User registration", 
        "POST /api/auth/update_profile.php" => "Update user profile",
        "POST /api/auth/change_password.php" => "Change user password",
        "GET /api/expenses/read.php?user_id={id}" => "Get user expenses",
        "POST /api/expenses/create.php" => "Create expense",
        "PUT /api/expenses/update.php" => "Update expense", 
        "DELETE /api/expenses/delete.php" => "Delete expense",
        "GET /api/expenses/analytics.php?user_id={id}" => "Get expense analytics"
    ],
    "server_time" => date('Y-m-d H:i:s'),
    "timezone" => date_default_timezone_get()
];

echo json_encode($api_info, JSON_PRETTY_PRINT);
?>