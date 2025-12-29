<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/Expense.php';

$database = new Database();
$db = $database->getConnection();
$expense = new Expense($db);

$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : die();
$expense->user_id = $user_id;

$stmt = $expense->getAnalytics();
$num = $stmt->rowCount();

if($num > 0) {
    $analytics_arr = array();
    $analytics_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $analytics_item = array(
            "year" => $year,
            "month" => $month,
            "total_expenses" => $total_expenses,
            "total_amount" => $total_amount,
            "avg_amount" => round($avg_amount, 2),
            "max_amount" => $max_amount,
            "min_amount" => $min_amount
        );
        array_push($analytics_arr["records"], $analytics_item);
    }

    http_response_code(200);
    echo json_encode($analytics_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No analytics data found."));
}
?>