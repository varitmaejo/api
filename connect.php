<?php
// ไฟล์: connect.php

// ตั้งค่าการแสดงข้อผิดพลาด (ใช้ในการพัฒนาเท่านั้น ให้ปิดในการใช้งานจริง)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ข้อมูลสำหรับการเชื่อมต่อฐานข้อมูล
$host = "147.50.231.17"; 
$user = "itshun_111111111111";
$password = "fr3254!Wd";
$dbname = "itshun_111111111111";

// ฟังก์ชันสำหรับจัดการข้อผิดพลาด
function handleDbError($message, $error = null) {
    $logMessage = $message;
    if ($error !== null) {
        $logMessage .= ": " . $error;
    }
    error_log($logMessage);
    die("ขออภัย เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล กรุณาลองใหม่อีกครั้งในภายหลัง");
}

// สร้างการเชื่อมต่อ
try {
    $conn = new mysqli($host, $user, $password, $dbname);
    
    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        handleDbError("การเชื่อมต่อฐานข้อมูลล้มเหลว", $conn->connect_error);
    }
    
    // ตั้งค่า charset เป็น utf8mb4 เพื่อรองรับภาษาไทยและอีโมจิ
    if (!$conn->set_charset("utf8mb4")) {
        handleDbError("การตั้งค่า charset utf8mb4 ล้มเหลว", $conn->error);
    }
    
    // หากต้องการทดสอบการเชื่อมต่อ สามารถใช้โค้ดนี้
    // echo "เชื่อมต่อฐานข้อมูลสำเร็จ";

} catch (Exception $e) {
    handleDbError("เกิดข้อผิดพลาดที่ไม่คาดคิด", $e->getMessage());
}

// ฟังก์ชันสำหรับปิดการเชื่อมต่อฐานข้อมูล (ใช้เมื่อทำงานเสร็จ)
function closeDbConnection() {
    global $conn;
    if ($conn) {
        $conn->close();
    }
}

// ไม่ต้องปิดการเชื่อมต่อที่นี่ เพราะเราต้องการให้การเชื่อมต่อพร้อมใช้งานสำหรับไฟล์อื่นๆ
// ให้เรียกใช้ closeDbConnection() เมื่อทำงานเสร็จสิ้นในไฟล์ที่ใช้งานการเชื่อมต่อนี้
?>