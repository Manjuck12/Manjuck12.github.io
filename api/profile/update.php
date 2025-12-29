<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, PUT");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/UserProfile.php';

$database = new Database();
$db = $database->getConnection();

$userProfile = new UserProfile($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (!isset($data->user_id)) {
    http_response_code(400);
    echo json_encode(array("message" => "User ID is required"));
    exit();
}

// Set user profile properties
$userProfile->user_id = $data->user_id;
$userProfile->designation = isset($data->designation) ? $data->designation : null;
$userProfile->salary = isset($data->salary) ? floatval($data->salary) : 0;
$userProfile->extra_amount = isset($data->extra_amount) ? floatval($data->extra_amount) : 0;

// Create or update profile
if ($userProfile->createOrUpdate()) {
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "message" => "Profile updated successfully"
    ));
} else {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to update profile"
    ));
}
?>