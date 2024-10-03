<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'connect.php';

$response = array();

// รับข้อมูล JSON
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['_method']) && $data['_method'] === 'DELETE')) {
    if (isset($data['id'])) {
        $id = $data['id'];
        
        // ตรวจสอบว่า $id เป็นตัวเลขหรือไม่
        if (is_numeric($id)) {
            $id = intval($id);
        } else {
            $response["status"] = "error";
            $response["message"] = "ID ไม่ถูกต้อง";
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }

        // ใช้ Prepared Statement เพื่อป้องกัน SQL Injection
        $sql = "DELETE FROM user WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response["status"] = "success";
                $response["message"] = "ลบข้อมูลสำเร็จ";
            } else {
                $response["status"] = "warning";
                $response["message"] = "ไม่พบข้อมูลที่ต้องการลบ";
            }
        } else {
            $response["status"] = "error";
            $response["message"] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $response["status"] = "error";
        $response["message"] = "ไม่ได้ระบุ ID";
    }
} else {
    $response["status"] = "error";
    $response["message"] = "Method ไม่ถูกต้อง";
}

$conn->close();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>