<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/Expense.php';

$database = new Database();
$db = $database->getConnection();
$expense = new Expense($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->user_id) && !empty($data->title) && !empty($data->amount) && !empty($data->category)) {
    $expense->user_id = $data->user_id;
    $expense->title = $data->title;
    $expense->amount = $data->amount;
    $expense->category = $data->category;
    $expense->description = $data->description ?? '';
    $expense->expense_date = $data->expense_date ?? date('Y-m-d');

    if($expense->create()) {
        http_response_code(201);
        echo json_encode(array("message" => "Expense was created successfully."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create expense."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to create expense. Data is incomplete."));
}
?>