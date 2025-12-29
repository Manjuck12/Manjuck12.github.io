<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

echo json_encode(array(
    "message" => "Expense Tracker API",
    "version" => "1.0.0",
    "endpoints" => array(
        "POST /api/auth/login.php" => "User login",
        "GET /api/expenses/read.php?user_id={id}" => "Get user expenses",
        "POST /api/expenses/create.php" => "Create expense",
        "PUT /api/expenses/update.php" => "Update expense",
        "DELETE /api/expenses/delete.php" => "Delete expense",
        "GET /api/expenses/analytics.php?user_id={id}" => "Get expense analytics"
    )
));
?>