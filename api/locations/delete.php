<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/Location.php';

$database = new Database();
$db = $database->getConnection();
$location = new Location($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->user_id)) {
    $location->id = $data->id;
    $location->user_id = $data->user_id;

    if($location->delete()) {
        http_response_code(200);
        echo json_encode(array("success" => true, "message" => "Location was deleted successfully."));
    } else {
        http_response_code(503);
        echo json_encode(array("success" => false, "message" => "Unable to delete location."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Unable to delete location. Data is incomplete."));
}
?>