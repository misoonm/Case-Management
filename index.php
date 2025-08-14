<?php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// الحصول على الإحصائيات
$stats = getCaseStats();

// الحصول على القضايا
$cases = getCases();
?>

<div class="container">
    <!-- إحصائيات القضايا -->
    <section class="stats-section fade-in">
        <h2 class="section-title">نظرة عامة على القضايا</h2>
        <div class="stats-container">
            <div class="stat-card total" id="totalCasesCard">
                <i class="fas fa-folder-open stat-icon"></i>
                <div class="stat-value" id="totalCases"><?= $stats['total'] ?></div>
                <div class="stat-label">إجمالي القضايا</div>
            </div>
            
            <div class="stat-card active" id="activeCasesCard">
                <i class="fas fa-tasks stat-icon"></i>
                <div class="stat-value" id="activeCases"><?= $stats['active'] ?></div>
                <div class="stat-label">قضايا نشطة</div>
            </div>
            
            <div class="stat-card completed" id="completedCasesCard">
                <i class="fas fa-check-circle stat-icon"></i>
                <div class="stat-value" id="completedCases"><?= $stats['completed'] ?></div>
                <div class="stat-label">قضايا مكتملة</div>
            </div>
        </div>
        
        <!-- أدوات البحث والفلترة -->
        <div class="tools-section fade-in">
            <div class="tools-grid">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="ابحث باسم صاحب القضية أو رقمها...">
                </div>
                
                <button class="btn btn-primary" id="filterBtn">
                    <i class="fas fa-filter"></i> فلترة القضايا
                </button>
                
                <button class="btn btn-accent" id="newCaseBtn">
                    <i class="fas fa-plus"></i> تسجيل قضية جديدة
                </button>
            </div>
        </div>
    </section>
    
    <!-- جدول القضايا -->
    <section class="cases-section fade-in">
        <h2 class="section-title">قائمة القضايا</h2>
        <div style="overflow-x: auto;">
            <table class="cases-table">
                <thead>
                    <tr>
                        <th>رقم القضية</th>
                        <th>صاحب القضية</th>
                        <th>نوع القضية</th>
                        <th>المحكمة</th>
                        <th>القاضي</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                        <th>مفضلة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody id="casesTableBody">
                    <?php foreach ($cases as $case): ?>
                    <tr>
                        <td class="case-id"><?= $case['case_number'] ?></td>
                        <td><?= $case['client_name'] ?></td>
                        <td><span class="case-type <?= getTypeClass($case['case_type']) ?>"><?= $case['case_type'] ?></span></td>
                        <td><?= $case['court_name'] ?></td>
                        <td><?= $case['judge_name'] ?></td>
                        <td><span class="case-status <?= getStatusClass($case['case_status']) ?>">
                            <?= getStatusIcon($case['case_status']) ?> 
                            <?= $case['case_status'] ?>
                        </span></td>
                        <td><?= date('d/m/Y', strtotime($case['registration_date'])) ?></td>
                        <td><button class="favorite-btn <?= $case['is_favorite'] ? 'favorited' : '' ?>" data-id="<?= $case['id'] ?>">
                            <i class="fas fa-star"></i>
                        </button></td>
                        <td>
                            <button class="action-btn"><i class="fas fa-eye"></i></button>
                            <button class="action-btn"><i class="fas fa-edit"></i></button>
                            <button class="action-btn"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php
// وظائف مساعدة لتنسيق العرض
function getTypeClass($type) {
    $classes = [
        'جنائية' => 'criminal',
        'تجارية' => 'commercial',
        'أحوال شخصية' => 'personal'
    ];
    return $classes[$type] ?? '';
}

function getStatusClass($status) {
    $classes = [
        'جديدة' => 'new',
        'قيد النظر' => 'in-progress',
        'منتهية' => 'closed',
        'مغلقة' => 'closed'
    ];
    return $classes[$status] ?? '';
}

function getStatusIcon($status) {
    $icons = [
        'جديدة' => '<i class="fas fa-file"></i>',
        'قيد النظر' => '<i class="fas fa-spinner"></i>',
        'منتهية' => '<i class="fas fa-check"></i>',
        'مغلقة' => '<i class="fas fa-lock"></i>'
    ];
    return $icons[$status] ?? '';
}

require_once 'includes/footer.php';
?>
