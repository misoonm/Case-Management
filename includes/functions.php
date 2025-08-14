<?php
function getCases() {
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
              ORDER BY c.registration_date DESC";

    $stmt = $db->prepare($query);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCaseStats() {
    $database = new Database();
    $db = $database->getConnection();

    $stats = [
        'total' => 0,
        'active' => 0,
        'completed' => 0,
        'favorites' => 0
    ];

    // إجمالي القضايا
    $query = "SELECT COUNT(*) FROM cases";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['total'] = $stmt->fetchColumn();

    // القضايا النشطة (جديدة + قيد النظر)
    $query = "SELECT COUNT(*) FROM cases c
              JOIN case_statuses cs ON c.status_id = cs.id
              WHERE cs.name IN ('جديدة', 'قيد النظر')";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['active'] = $stmt->fetchColumn();

    // القضايا المكتملة (منتهية + مغلقة)
    $query = "SELECT COUNT(*) FROM cases c
              JOIN case_statuses cs ON c.status_id = cs.id
              WHERE cs.name IN ('منتهية', 'مغلقة')";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['completed'] = $stmt->fetchColumn();

    // القضايا المفضلة
    $query = "SELECT COUNT(*) FROM cases WHERE is_favorite = true";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats['favorites'] = $stmt->fetchColumn();

    return $stats;
}

function addCase($data) {
    $database = new Database();
    $db = $database->getConnection();

    // الحصول على معرفات من الأسماء
    $caseTypeId = getTypeIdByName($db, $data['case_type']);
    $caseStatusId = getStatusIdByName($db, $data['case_status']);
    $clientId = getClientIdByName($db, $data['client_name']);
    $courtId = getCourtIdByName($db, $data['court']);
    $judgeId = getJudgeIdByName($db, $data['judge']);

    $query = "INSERT INTO cases 
                (case_number, title, description, client_id, court_id, judge_id, 
                 case_type_id, status_id, registration_date, last_update, is_favorite)
              VALUES 
                (:case_number, :title, :description, :client_id, :court_id, :judge_id, 
                 :case_type_id, :status_id, :registration_date, :last_update, :is_favorite)";

    $stmt = $db->prepare($query);

    // ربط المعاملات
    $stmt->bindParam(':case_number', $data['case_number']);
    $stmt->bindParam(':title', $data['title']);
    $stmt->bindParam(':description', $data['description']);
    $stmt->bindParam(':client_id', $clientId);
    $stmt->bindParam(':court_id', $courtId);
    $stmt->bindParam(':judge_id', $judgeId);
    $stmt->bindParam(':case_type_id', $caseTypeId);
    $stmt->bindParam(':status_id', $caseStatusId);
    $stmt->bindParam(':registration_date', $data['registration_date']);
    $stmt->bindParam(':last_update', $data['last_update']);
    $stmt->bindParam(':is_favorite', $data['is_favorite']);

    return $stmt->execute();
}

// وظائف مساعدة للحصول على المعرفات
function getTypeIdByName($db, $name) {
    $query = "SELECT id FROM case_types WHERE name = :name";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// ... وظائف مماثلة للجداول الأخرى
?>
