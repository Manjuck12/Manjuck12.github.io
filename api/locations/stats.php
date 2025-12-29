<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/Location.php';

$database = new Database();
$db = $database->getConnection();
$location = new Location($db);

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : die();
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$month = isset($_GET['month']) ? $_GET['month'] : null;

$location->user_id = $user_id;
$stmt = $location->getStats($year, $month);
$num = $stmt->rowCount();

if($num > 0) {
    $stats_arr = array();
    $stats_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $stat_item = array(
            "location_type" => $location_type,
            "location_name" => $location_name,
            "count" => (int)$count
        );
        array_push($stats_arr["records"], $stat_item);
    }

    http_response_code(200);
    echo json_encode($stats_arr);
} else {
    http_response_code(200);
    echo json_encode(array("records" => array()));
}
?>