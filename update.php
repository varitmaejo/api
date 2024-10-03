<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *"); // ปรับแต่งตามความเหมาะสมในการใช้งานจริง
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include 'connect.php';

$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูล JSON จาก request body
    $data = json_decode(file_get_contents("php://input"), true);

    // ตรวจสอบว่ามีข้อมูลครบถ้วน
    if (isset($data['id']) && isset($data['name']) && isset($data['email'])) {
        $id = $conn->real_escape_string($data['id']);
        $name = $conn->real_escape_string($data['name']);
        $email = $conn->real_escape_string($data['email']);

        // ใช้ Prepared Statement เพื่อป้องกัน SQL Injection
        $sql = "UPDATE user SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $name, $email, $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response["status"] = "success";
                $response["message"] = "แก้ไขข้อมูลสำเร็จ";
            } else {
                $response["status"] = "warning";
                $response["message"] = "ไม่มีการเปลี่ยนแปลงข้อมูล";
            }
        } else {
            $response["status"] = "error";
            $response["message"] = "มีข้อผิดพลาดในการแก้ไขข้อมูล: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $response["status"] = "error";
        $response["message"] = "ข้อมูลไม่ครบถ้วน";
    }
} else {
    $response["status"] = "error";
    $response["message"] = "Method ไม่ถูกต้อง กรุณาใช้ POST";
}

$conn->close();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>