<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/UserProfile.php';

$database = new Database();
$db = $database->getConnection();

$userProfile = new UserProfile($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (!isset($data->user_id) || !isset($data->amount) || !isset($data->date)) {
    http_response_code(400);
    echo json_encode(array("message" => "User ID, amount, and date are required"));
    exit();
}

// Validate amount
if (!is_numeric($data->amount) || floatval($data->amount) <= 0) {
    http_response_code(400);
    echo json_encode(array("message" => "Amount must be a positive number"));
    exit();
}

// Set properties
$userProfile->user_id = $data->user_id;
$amount = floatval($data->amount);
$description = isset($data->description) ? $data->description : null;
$date = $data->date;

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    http_response_code(400);
    echo json_encode(array("message" => "Invalid date format. Use YYYY-MM-DD"));
    exit();
}

// Add daily extra amount
if ($userProfile->addDailyExtra($amount, $description, $date)) {
    http_response_code(201);
    echo json_encode(array(
        "success" => true,
        "message" => "Daily extra amount added successfully"
    ));
} else {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to add daily extra amount"
    ));
}
?>