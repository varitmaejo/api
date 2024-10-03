<?php
header("Content-Type: application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

include 'connect.php';

try {
    $sql = "SELECT id, name, email FROM user";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = array();
    while ($row = $result->fetch_assoc()) {
        $users[] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email']
        );
    }
    
    if (empty($users)) {
        echo json_encode(array("message" => "ไม่พบข้อมูลผู้ใช้"), JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode($users, JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(array("error" => "เกิดข้อผิดพลาดในการดึงข้อมูล กรุณาลองใหม่อีกครั้ง"), JSON_UNESCAPED_UNICODE);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>