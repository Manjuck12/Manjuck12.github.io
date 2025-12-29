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

$stmt = $expense->read();
$num = $stmt->rowCount();

if($num > 0) {
    $expenses_arr = array();
    $expenses_arr["records"] = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $expense_item = array(
            "id" => $id,
            "title" => $title,
            "amount" => $amount,
            "category" => $category,
            "description" => $description,
            "expense_date" => $expense_date,
            "created_at" => $created_at
        );
        array_push($expenses_arr["records"], $expense_item);
    }

    http_response_code(200);
    echo json_encode($expenses_arr);
} else {
    http_response_code(404);
    echo json_encode(array("message" => "No expenses found."));
}
?>