<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'connect.php';

$response = array();

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the input is JSON
    $input = json_decode(file_get_contents("php://input"), true);
    if ($input) {
        $name = isset($input['name']) ? $conn->real_escape_string($input['name']) : "";
        $email = isset($input['email']) ? $conn->real_escape_string($input['email']) : "";
    } else {
        // If not JSON, try to get data from POST
        $name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : "";
        $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : "";
    }

    if (empty($name) || empty($email)) {
        $response["status"] = "error";
        $response["message"] = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        $sql = "INSERT INTO user (name, email) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $name, $email);

        if ($stmt->execute()) {
            $response["status"] = "success";
            $response["message"] = "บันทึกข้อมูลสำเร็จ";
        } else {
            $response["status"] = "error";
            $response["message"] = "มีข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    $response["status"] = "error";
    $response["message"] = "Method ไม่ถูกต้อง กรุณาใช้ POST";
}

$conn->close();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>