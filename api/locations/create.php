<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/Location.php';

$database = new Database();
$db = $database->getConnection();
$location = new Location($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->user_id) && !empty($data->location_name) && !empty($data->location_type) && !empty($data->date)) {
    $location->user_id = $data->user_id;
    $location->location_name = $data->location_name;
    $location->location_type = $data->location_type;
    $location->date = $data->date;
    $location->time_start = $data->time_start ?? null;
    $location->time_end = $data->time_end ?? null;
    $location->notes = $data->notes ?? '';

    if($location->create()) {
        http_response_code(201);
        echo json_encode(array(
            "success" => true,
            "message" => "Location was created successfully.",
            "location" => array(
                "id" => $location->id,
                "user_id" => $location->user_id,
                "location_name" => $location->location_name,
                "location_type" => $location->location_type,
                "date" => $location->date,
                "time_start" => $location->time_start,
                "time_end" => $location->time_end,
                "notes" => $location->notes
            )
        ));
    } else {
        http_response_code(503);
        echo json_encode(array("success" => false, "message" => "Unable to create location."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Unable to create location. Data is incomplete."));
}
?>