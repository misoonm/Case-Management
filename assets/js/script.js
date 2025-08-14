// البحث في القضايا
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.trim();
    fetchCases(searchTerm);
});

// فلترة القضايا
document.getElementById('filterBtn').addEventListener('click', function() {
    // فتح نافذة الفلترة
    // ... (تطبيق الفلاتر المختارة)
    const filters = {
        types: ['جنائية', 'تجارية'],
        statuses: ['جديدة', 'قيد النظر'],
        favorites: true
    };
    
    fetchCases('', filters);
});

// إضافة قضية جديدة
document.getElementById('caseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        case_number: document.getElementById('caseId').value,
        title: document.getElementById('caseTitle').value,
        client_name: document.getElementById('clientName').value,
        court: document.getElementById('court').value,
        judge: document.getElementById('judge').value,
        case_type: document.getElementById('caseType').value,
        case_status: document.getElementById('caseStatus').value,
        description: document.getElementById('description').value,
        registration_date: document.getElementById('registrationDate').value,
        last_update: document.getElementById('updateDate').value || document.getElementById('registrationDate').value,
        is_favorite: document.getElementById('isFavorite').checked
    };
    
    // إظهار مؤشر التحميل
    document.querySelector('.loader').classList.add('active');
    
    fetch('/api/add_case.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            // تحديث الجدول
            fetchCases();
            // إغلاق النموذج
            document.getElementById('caseModal').classList.remove('active');
            // إعادة تعيين النموذج
            document.getElementById('caseForm').reset();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء إرسال البيانات');
    })
    .finally(() => {
        document.querySelector('.loader').classList.remove('active');
    });
});

// وظيفة لجلب القضايا من الخادم
function fetchCases(search = '', filters = {}) {
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (filters) params.append('filters', JSON.stringify(filters));
    
    fetch(`/api/get_cases.php?${params.toString()}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            renderCases(data.data);
        }
    });
}

// وظيفة لعرض القضايا في الجدول
function renderCases(cases) {
    const tableBody = document.getElementById('casesTableBody');
    tableBody.innerHTML = '';
    
    cases.forEach(c => {
        const row = document.createElement('tr');
        
        // ... بناء الصف كما في المثال السابق
        row.innerHTML = `
            <td class="case-id">${c.case_number}</td>
            <td>${c.client_name}</td>
            <td><span class="case-type ${getTypeClass(c.case_type)}">${c.case_type}</span></td>
            <td>${c.court_name}</td>
            <td>${c.judge_name}</td>
            <td><span class="case-status ${getStatusClass(c.case_status)}">
                ${getStatusIcon(c.case_status)} 
                ${c.case_status}
            </span></td>
            <td>${formatDate(c.registration_date)}</td>
            <td><button class="favorite-btn ${c.is_favorite ? 'favorited' : ''}" data-id="${c.id}">
                <i class="fas fa-star"></i>
            </button></td>
            <td>
                <button class="action-btn"><i class="fas fa-eye"></i></button>
                <button class="action-btn"><i class="fas fa-edit"></i></button>
                <button class="action-btn"><i class="fas fa-trash"></i></button>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
    
    // إضافة أحداث المفضلة
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const caseId = this.getAttribute('data-id');
            toggleFavorite(caseId, this);
        });
    });
}

// وظائف مساعدة
function getTypeClass(type) {
    const classes = {
        'جنائية': 'criminal',
        'تجارية': 'commercial',
        'أحوال شخصية': 'personal'
    };
    return classes[type] || '';
}

function getStatusClass(status) {
    const classes = {
        'جديدة': 'new',
        'قيد النظر': 'in-progress',
        'منتهية': 'closed',
        'مغلقة': 'closed'
    };
    return classes[status] || '';
}

function getStatusIcon(status) {
    const icons = {
        'جديدة': '<i class="fas fa-file"></i>',
        'قيد النظر': '<i class="fas fa-spinner"></i>',
        'منتهية': '<i class="fas fa-check"></i>',
        'مغلقة': '<i class="fas fa-lock"></i>'
    };
    return icons[status] || '';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return `${date.getDate()}/${date.getMonth() + 1}/${date.getFullYear()}`;
}

// تهيئة عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    fetchCases();
});
