<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/UserProfile.php';

$database = new Database();
$db = $database->getConnection();

$userProfile = new UserProfile($db);

// Get posted data
$data = json_decode(file_get_contents("php://input"));

// Validate required fields
if (!isset($data->user_id) || !isset($data->extra_id)) {
    http_response_code(400);
    echo json_encode(array("message" => "User ID and extra ID are required"));
    exit();
}

// Set properties
$userProfile->user_id = $data->user_id;
$extra_id = $data->extra_id;

// Delete daily extra amount
if ($userProfile->deleteDailyExtra($extra_id)) {
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "message" => "Daily extra amount deleted successfully"
    ));
} else {
    http_response_code(500);
    echo json_encode(array(
        "success" => false,
        "message" => "Unable to delete daily extra amount"
    ));
}
?>