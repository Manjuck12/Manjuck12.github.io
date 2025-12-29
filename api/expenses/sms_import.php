<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->expense_id) && !empty($data->raw_sms)) {
    try {
        // Store SMS import metadata
        $query = "INSERT INTO sms_imports 
                  (expense_id, raw_sms, sms_sender, sms_date, confidence, bank_name, merchant, import_timestamp) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(1, $data->expense_id);
        $stmt->bindParam(2, $data->raw_sms);
        $stmt->bindParam(3, $data->sms_sender);
        $stmt->bindParam(4, $data->sms_date);
        $stmt->bindParam(5, $data->confidence);
        $stmt->bindParam(6, $data->bank_name);
        $stmt->bindParam(7, $data->merchant);
        $stmt->bindParam(8, $data->import_timestamp);
        
        if($stmt->execute()) {
            http_response_code(201);
            echo json_encode(array("message" => "SMS import metadata saved successfully."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to save SMS import metadata."));
        }
    } catch(Exception $e) {
        http_response_code(503);
        echo json_encode(array("message" => "Database error: " . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to save SMS import metadata. Data is incomplete."));
}
?>