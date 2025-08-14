<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (addCase($data)) {
        echo json_encode(['success' => true, 'message' => 'تمت إضافة القضية بنجاح']);
    } else {
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء إضافة القضية']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'طريقة الطلب غير صالحة']);
}
?>
