<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$search = isset($_GET['search']) ? $_GET['search'] : '';
$filters = isset($_GET['filters']) ? json_decode($_GET['filters'], true) : [];

$cases = getFilteredCases($search, $filters);

echo json_encode([
    'success' => true,
    'data' => $cases
]);

function getFilteredCases($search, $filters) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT 
                c.id, c.case_number, c.title, cl.full_name AS client_name,
                ct.name AS case_type, cs.name AS case_status, co.name AS court_name,
                j.full_name AS judge_name, c.registration_date, c.last_update, c.is_favorite
              FROM cases c
              JOIN clients cl ON c.client_id = cl.id
              JOIN case_types ct ON c.case_type_id = ct.id
              JOIN case_statuses cs ON c.status_id = cs.id
              JOIN courts co ON c.court_id = co.id
              JOIN judges j ON c.judge_id = j.id
              WHERE 1=1";

    $params = [];

    // تطبيق البحث
    if (!empty($search)) {
        $query .= " AND (c.case_number ILIKE :search OR cl.full_name ILIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    // تطبيق الفلاتر
    if (!empty($filters['types'])) {
        $query .= " AND ct.name IN (".implode(',', array_fill(0, count($filters['types']), '?')).")";
        $params = array_merge($params, $filters['types']);
    }

    // ... فلترة حسب الحالة والمفضلة

    $query .= " ORDER BY c.registration_date DESC";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
