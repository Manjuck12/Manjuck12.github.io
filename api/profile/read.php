<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/UserProfile.php';

$database = new Database();
$db = $database->getConnection();

$userProfile = new UserProfile($db);

// Get user_id from query parameters
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (!$user_id) {
    http_response_code(400);
    echo json_encode(array("message" => "User ID is required"));
    exit();
}

$userProfile->user_id = $user_id;

if ($userProfile->read($user_id)) {
    // Get daily extras
    $daily_extras = $userProfile->getDailyExtras();
    
    // Get analytics
    $analytics = $userProfile->getProfileAnalytics();
    
    $profile_data = array(
        "id" => $userProfile->id,
        "user_id" => $userProfile->user_id,
        "designation" => $userProfile->designation,
        "salary" => $userProfile->salary,
        "extra_amount" => $userProfile->extra_amount,
        "daily_extras" => $daily_extras,
        "analytics" => $analytics,
        "created_at" => $userProfile->created_at,
        "updated_at" => $userProfile->updated_at
    );
    
    http_response_code(200);
    echo json_encode($profile_data);
} else {
    // Return default profile if not found
    $default_profile = array(
        "id" => null,
        "user_id" => $user_id,
        "designation" => null,
        "salary" => 0,
        "extra_amount" => 0,
        "daily_extras" => array(),
        "analytics" => array(
            "monthly_data" => array(),
            "current_month_total" => 0,
            "yearly_total" => 0,
            "salary" => 0,
            "designation" => null
        ),
        "created_at" => null,
        "updated_at" => null
    );
    
    http_response_code(200);
    echo json_encode($default_profile);
}
?>