<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';
include_once '../../models/Expense.php';

$database = new Database();
$db = $database->getConnection();
$expense = new Expense($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->user_id) && !empty($data->title) && !empty($data->amount) && !empty($data->category)) {
    $expense->id = $data->id;
    $expense->user_id = $data->user_id;
    $expense->title = $data->title;
    $expense->amount = $data->amount;
    $expense->category = $data->category;
    $expense->description = $data->description ?? '';
    $expense->expense_date = $data->expense_date;

    if($expense->update()) {
        http_response_code(200);
        echo json_encode(array("message" => "Expense was updated successfully."));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to update expense."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to update expense. Data is incomplete."));
}
?>