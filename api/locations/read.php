<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/Location.php';

$database = new Database();
$db = $database->getConnection();
$location = new Location($db);

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : die();
$year = isset($_GET['year']) ? $_GET['year'] : null;
$month = isset($_GET['month']) ? $_GET['month'] : null;

$location->user_id = $user_id;

if ($year && $month) {
    $stmt = $location->readByMonth($year, $month);
} else {
    $stmt = $location->read();
}

$num = $stmt->rowCount();

if($num > 0) {
    $locations_arr = array();
    $locations_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $location_item = array(
            "id" => $id,
            "user_id" => $user_id,
            "location_name" => $location_name,
            "location_type" => $location_type,
            "date" => $date,
            "time_start" => $time_start,
            "time_end" => $time_end,
            "notes" => $notes,
            "created_at" => $created_at
        );
        array_push($locations_arr["records"], $location_item);
    }

    http_response_code(200);
    echo json_encode($locations_arr);
} else {
    http_response_code(200);
    echo json_encode(array("records" => array()));
}
?>