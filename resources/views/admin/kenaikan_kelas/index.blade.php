@extends('layouts.app')

@section('title', 'Class Progression Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h2 class="page-title">
                    <i class="ti ti-school me-2"></i>Class Progression Management
                </h2>
                <div class="page-subtitle">Manage student progression from Class XI to XII with automated criteria evaluation</div>
                <div class="page-actions">
                    <div class="btn-group">
                        <button class="btn btn-success" onclick="showBulkProcessModal()">
                            <i class="ti ti-wand me-1"></i>Auto Process All
                        </button>
                        <button class="btn btn-primary" onclick="showManualProgressionModal()">
                            <i class="ti ti-plus me-1"></i>Manual Progression
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-metric border-left-success">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="metric-value text-success" id="promotedCount">0</div>
                            <div class="metric-label">Promoted to XII</div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="metric-icon bg-success">
                                <i class="ti ti-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-metric border-left-warning">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="metric-value text-warning" id="pendingCount">0</div>
                            <div class="metric-label">Pending Review</div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="metric-icon bg-warning">
                                <i class="ti ti-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-metric border-left-danger">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="metric-value text-danger" id="notPromotedCount">0</div>
                            <div class="metric-label">Not Promoted</div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="metric-icon bg-danger">
                                <i class="ti ti-arrow-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card card-metric border-left-info">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <div class="metric-value text-info" id="eligibleCount">0</div>
                            <div class="metric-label">Eligible Students</div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="metric-icon bg-info">
                                <i class="ti ti-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Academic Year</label>
                            <select class="form-select" id="academicYearFilter">
                                <option value="">All Academic Years</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="naik">Promoted</option>
                                <option value="tidak_naik">Not Promoted</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Search Student</label>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search by name or NISN...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-primary w-100" onclick="applyFilters()">
                                <i class="ti ti-search me-1"></i>Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Class Progression Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Class Progression Records</h4>
                    <div class="card-actions">
                        <button class="btn btn-sm btn-outline-secondary" onclick="exportToExcel()">
                            <i class="ti ti-download"></i> Export
                        </button>
                        <button class="btn btn-sm btn-outline-secondary ms-1" onclick="refreshTable()">
                            <i class="ti ti-refresh"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="progressionTable">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="15%">Student</th>
                                    <th width="10%">NISN</th>
                                    <th width="12%">From Class</th>
                                    <th width="12%">To Class</th>
                                    <th width="10%">Academic Year</th>
                                    <th width="10%">Status</th>
                                    <th width="8%">Criteria Score</th>
                                    <th width="12%">Date</th>
                                    <th width="6%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Content loaded via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Showing <span id="showingStart">0</span> to <span id="showingEnd">0</span> of <span id="totalRecords">0</span> entries
                        </div>
                        <nav>
                            <ul class="pagination pagination-sm mb-0" id="paginationNav">
                                <!-- Pagination links will be generated here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Process Modal -->
<div class="modal fade" id="bulkProcessModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-wand me-2"></i>Automated Class Progression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkProcessForm">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Automated Processing</strong><br>
                        This will evaluate all Class XI students based on the criteria below and automatically process their class progression.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select class="form-select" id="bulk_tahun_ajaran_id" name="tahun_ajaran_id" required>
                                <!-- Options loaded via JavaScript -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Eligible Students</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="eligibleStudentsCount" readonly>
                                <button type="button" class="btn btn-outline-secondary" onclick="loadEligibleStudents()">
                                    <i class="ti ti-refresh"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6><i class="ti ti-settings me-1"></i>Promotion Criteria</h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Minimum Achievements</label>
                            <input type="number" class="form-control" id="min_prestasi" name="kriteria[min_prestasi]" 
                                   value="1" min="0" placeholder="0">
                            <div class="form-hint">Minimum number of achievements required</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Minimum Average Grade</label>
                            <input type="number" class="form-control" id="min_nilai_rata" name="kriteria[min_nilai_rata]" 
                                   value="75" min="0" max="100" step="0.1" placeholder="75">
                            <div class="form-hint">Minimum average grade (0-100)</div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Non-Academic Requirement</label>
                            <select class="form-select" id="include_non_akademik" name="kriteria[include_non_akademik]">
                                <option value="0">Not Required</option>
                                <option value="1">Required</option>
                            </select>
                            <div class="form-hint">Require at least one non-academic achievement</div>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Important:</strong> 
                        <ul class="mb-0 mt-2">
                            <li>Students meeting criteria will be automatically promoted to Class XII</li>
                            <li>Students not meeting criteria will be marked as "Not Promoted"</li>
                            <li>You can review and modify individual cases after processing</li>
                            <li>This process cannot be undone in bulk</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ti ti-wand me-1"></i>Process All Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manual Progression Modal -->
<div class="modal fade" id="manualProgressionModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-user-plus me-2"></i>Manual Class Progression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="manualProgressionForm">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <select class="form-select" id="manual_tahun_ajaran_id" name="tahun_ajaran_id" required onchange="loadEligibleStudentsForManual()">
                                <!-- Options loaded via JavaScript -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Target Class XII</label>
                            <select class="form-select" id="default_target_class">
                                <!-- Options loaded via JavaScript -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quick Actions</label>
                            <div class="btn-group w-100">
                                <button type="button" class="btn btn-sm btn-success" onclick="selectAllStudents()">
                                    <i class="ti ti-check-all"></i> Select All
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="clearAllSelections()">
                                    <i class="ti ti-x"></i> Clear All
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm" id="eligibleStudentsTable">
                            <thead>
                                <tr>
                                    <th width="5%">
                                        <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onchange="toggleAllStudents()">
                                    </th>
                                    <th width="20%">Student Name</th>
                                    <th width="10%">NISN</th>
                                    <th width="12%">Current Class</th>
                                    <th width="15%">Target Class XII</th>
                                    <th width="10%">Status</th>
                                    <th width="8%">Achievements</th>
                                    <th width="8%">Avg Grade</th>
                                    <th width="12%">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Content loaded via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label">General Notes</label>
                            <textarea class="form-control" name="keterangan" rows="2" 
                                      placeholder="Optional notes for this progression batch..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>Process Selected Students
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-eye me-2"></i>Progression Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Content loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
}

.page-actions {
    margin-left: auto;
}

.card-metric {
    transition: all 0.3s ease;
}

.card-metric:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.border-left-success {
    border-left: 4px solid #28a745;
}

.border-left-warning {
    border-left: 4px solid #ffc107;
}

.border-left-danger {
    border-left: 4px solid #dc3545;
}

.border-left-info {
    border-left: 4px solid #17a2b8;
}

.metric-value {
    font-size: 2rem;
    font-weight: bold;
}

.metric-label {
    font-size: 0.875rem;
    color: var(--bs-secondary);
    font-weight: 500;
}

.metric-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-naik {
    background-color: #d1edf2;
    color: #0c5460;
}

.status-tidak_naik {
    background-color: #f8d7da;
    color: #721c24;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.criteria-score {
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
}

.score-high {
    background-color: #d1edfd;
    color: #0c5460;
}

.score-medium {
    background-color: #fff3cd;
    color: #856404;
}

.score-low {
    background-color: #f8d7da;
    color: #721c24;
}

.page-subtitle {
    color: var(--bs-secondary);
    margin-top: 0.25rem;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .page-actions {
        margin-left: 0;
        margin-top: 1rem;
    }
}
</style>

@push('scripts')
<script>
let currentPage = 1;
let totalPages = 1;

document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
    loadAcademicYears();
    loadTargetClasses();
    loadProgressionData();
    setupEventListeners();
});

function setupEventListeners() {
    // Bulk process form
    document.getElementById('bulkProcessForm').addEventListener('submit', function(e) {
        e.preventDefault();
        processBulkProgression();
    });

    // Manual progression form
    document.getElementById('manualProgressionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        processManualProgression();
    });

    // Search functionality
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 500);
    });
}

function loadStatistics() {
    // This would typically be an API call to get current statistics
    fetch(`{{ route('admin.kenaikan_kelas.index') }}?api=stats`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('promotedCount').textContent = data.promoted || 0;
            document.getElementById('pendingCount').textContent = data.pending || 0;
            document.getElementById('notPromotedCount').textContent = data.not_promoted || 0;
            document.getElementById('eligibleCount').textContent = data.eligible || 0;
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
            // Set demo data for now
            document.getElementById('promotedCount').textContent = '87';
            document.getElementById('pendingCount').textContent = '12';
            document.getElementById('notPromotedCount').textContent = '5';
            document.getElementById('eligibleCount').textContent = '23';
        });
}

function loadAcademicYears() {
    fetch('{{ route("admin.tahun_ajaran.for_select") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const selects = ['academicYearFilter', 'bulk_tahun_ajaran_id', 'manual_tahun_ajaran_id'];
                selects.forEach(selectId => {
                    const select = document.getElementById(selectId);
                    if (select) {
                        // Clear existing options except the first one for filter
                        if (selectId === 'academicYearFilter') {
                            select.innerHTML = '<option value="">All Academic Years</option>';
                        } else {
                            select.innerHTML = '<option value="">Select Academic Year</option>';
                        }
                        
                        data.data.forEach(year => {
                            const option = document.createElement('option');
                            option.value = year.value;
                            option.textContent = year.label;
                            if (year.is_active) option.selected = true;
                            select.appendChild(option);
                        });
                    }
                });
            }
        })
        .catch(error => console.error('Error loading academic years:', error));
}

function loadTargetClasses() {
    // In a real implementation, this would fetch Class XII options
    const targetClassSelect = document.getElementById('default_target_class');
    if (targetClassSelect) {
        targetClassSelect.innerHTML = `
            <option value="">Select Target Class</option>
            <option value="1">XII IPA 1</option>
            <option value="2">XII IPA 2</option>
            <option value="3">XII IPS 1</option>
            <option value="4">XII IPS 2</option>
        `;
    }
}

function loadProgressionData(page = 1) {
    const academicYear = document.getElementById('academicYearFilter').value;
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchInput').value;

    // In a real implementation, this would be an API call
    setTimeout(() => {
        const tbody = document.querySelector('#progressionTable tbody');
        tbody.innerHTML = generateSampleProgressionData();
        updatePagination(page, 5, 127); // Sample pagination
    }, 500);
}

function generateSampleProgressionData() {
    const sampleData = [
        { 
            id: 1, 
            student: 'Ahmad Budi Santoso', 
            nisn: '1234567890', 
            fromClass: 'XI IPA 1', 
            toClass: 'XII IPA 1', 
            academicYear: '2024/2025', 
            status: 'naik', 
            score: 85, 
            date: '2024-07-15' 
        },
        { 
            id: 2, 
            student: 'Siti Rahma Dewi', 
            nisn: '1234567891', 
            fromClass: 'XI IPA 2', 
            toClass: 'XII IPA 2', 
            academicYear: '2024/2025', 
            status: 'pending', 
            score: 72, 
            date: null 
        },
        { 
            id: 3, 
            student: 'Muhammad Fajar', 
            nisn: '1234567892', 
            fromClass: 'XI IPS 1', 
            toClass: 'XII IPS 1', 
            academicYear: '2024/2025', 
            status: 'tidak_naik', 
            score: 45, 
            date: '2024-07-15' 
        },
    ];

    return sampleData.map((item, index) => {
        const statusClass = getStatusClass(item.status);
        const scoreClass = getScoreClass(item.score);
        
        return `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.student}</strong></td>
                <td><code>${item.nisn}</code></td>
                <td><span class="badge bg-secondary">${item.fromClass}</span></td>
                <td><span class="badge bg-primary">${item.toClass}</span></td>
                <td>${item.academicYear}</td>
                <td><span class="status-badge ${statusClass}">${getStatusText(item.status)}</span></td>
                <td><span class="criteria-score ${scoreClass}">${item.score}/100</span></td>
                <td>${item.date ? formatDate(item.date) : '-'}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="viewProgressionDetail(${item.id})" title="View Details">
                        <i class="ti ti-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning ms-1" onclick="editProgression(${item.id})" title="Edit">
                        <i class="ti ti-edit"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function getStatusClass(status) {
    switch(status) {
        case 'naik': return 'status-naik';
        case 'tidak_naik': return 'status-tidak_naik';
        case 'pending': return 'status-pending';
        default: return 'status-pending';
    }
}

function getStatusText(status) {
    switch(status) {
        case 'naik': return 'Promoted';
        case 'tidak_naik': return 'Not Promoted';
        case 'pending': return 'Pending';
        default: return 'Unknown';
    }
}

function getScoreClass(score) {
    if (score >= 80) return 'score-high';
    if (score >= 60) return 'score-medium';
    return 'score-low';
}

function updatePagination(currentPage, totalPages, totalRecords) {
    document.getElementById('showingStart').textContent = ((currentPage - 1) * 20) + 1;
    document.getElementById('showingEnd').textContent = Math.min(currentPage * 20, totalRecords);
    document.getElementById('totalRecords').textContent = totalRecords;

    const paginationNav = document.getElementById('paginationNav');
    let paginationHTML = '';

    // Previous button
    paginationHTML += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadProgressionData(${currentPage - 1})">&laquo;</a>
        </li>
    `;

    // Page numbers
    for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
        paginationHTML += `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadProgressionData(${i})">${i}</a>
            </li>
        `;
    }

    // Next button
    paginationHTML += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadProgressionData(${currentPage + 1})">&raquo;</a>
        </li>
    `;

    paginationNav.innerHTML = paginationHTML;
}

function applyFilters() {
    currentPage = 1;
    loadProgressionData(currentPage);
}

function refreshTable() {
    loadStatistics();
    loadProgressionData(currentPage);
    showAlert('Data refreshed successfully', 'info');
}

function showBulkProcessModal() {
    loadEligibleStudents();
    const modal = new bootstrap.Modal(document.getElementById('bulkProcessModal'));
    modal.show();
}

function showManualProgressionModal() {
    const modal = new bootstrap.Modal(document.getElementById('manualProgressionModal'));
    modal.show();
}

function loadEligibleStudents() {
    const academicYearId = document.getElementById('bulk_tahun_ajaran_id').value;
    if (!academicYearId) {
        document.getElementById('eligibleStudentsCount').value = 'Select academic year first';
        return;
    }

    fetch(`{{ route('admin.kenaikan_kelas.eligible_students') }}?tahun_ajaran_id=${academicYearId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('eligibleStudentsCount').value = `${data.data.length} eligible students`;
            } else {
                document.getElementById('eligibleStudentsCount').value = 'Error loading students';
            }
        })
        .catch(error => {
            console.error('Error loading eligible students:', error);
            document.getElementById('eligibleStudentsCount').value = 'Error loading students';
        });
}

function loadEligibleStudentsForManual() {
    const academicYearId = document.getElementById('manual_tahun_ajaran_id').value;
    if (!academicYearId) return;

    const tbody = document.querySelector('#eligibleStudentsTable tbody');
    tbody.innerHTML = '<tr><td colspan="9" class="text-center">Loading...</td></tr>';

    // In real implementation, this would fetch from the API
    setTimeout(() => {
        tbody.innerHTML = generateEligibleStudentsHTML();
    }, 500);
}

function generateEligibleStudentsHTML() {
    const sampleStudents = [
        { id: 1, name: 'Ahmad Rizki', nisn: '1234567890', currentClass: 'XI IPA 1', achievements: 3, avgGrade: 82 },
        { id: 2, name: 'Siti Aminah', nisn: '1234567891', currentClass: 'XI IPA 2', achievements: 1, avgGrade: 76 },
        { id: 3, name: 'Budi Setiawan', nisn: '1234567892', currentClass: 'XI IPS 1', achievements: 5, avgGrade: 89 },
    ];

    return sampleStudents.map(student => `
        <tr>
            <td>
                <input type="checkbox" class="form-check-input student-checkbox" 
                       name="siswa_selections[${student.id}][siswa_id]" value="${student.id}">
            </td>
            <td><strong>${student.name}</strong></td>
            <td><code>${student.nisn}</code></td>
            <td><span class="badge bg-secondary">${student.currentClass}</span></td>
            <td>
                <select class="form-select form-select-sm" name="siswa_selections[${student.id}][kelas_tujuan_id]">
                    <option value="">Select Class XII</option>
                    <option value="1">XII IPA 1</option>
                    <option value="2">XII IPA 2</option>
                    <option value="3">XII IPS 1</option>
                </select>
            </td>
            <td>
                <select class="form-select form-select-sm" name="siswa_selections[${student.id}][status]">
                    <option value="naik">Promote</option>
                    <option value="tidak_naik">Don't Promote</option>
                    <option value="pending">Pending</option>
                </select>
            </td>
            <td><span class="badge bg-info">${student.achievements}</span></td>
            <td><span class="badge bg-success">${student.avgGrade}</span></td>
            <td>
                <input type="text" class="form-control form-control-sm" 
                       placeholder="Notes..." style="min-width: 100px;">
            </td>
        </tr>
    `).join('');
}

function processBulkProgression() {
    const formData = new FormData(document.getElementById('bulkProcessForm'));
    
    fetch('{{ route("admin.kenaikan_kelas.bulk_process") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            if (data.details) {
                showBulkProcessResults(data.details);
            }
            document.getElementById('bulkProcessModal').querySelector('[data-bs-dismiss="modal"]').click();
            loadStatistics();
            loadProgressionData();
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error processing bulk progression:', error);
        showAlert('Error processing bulk progression', 'danger');
    });
}

function processManualProgression() {
    const formData = new FormData(document.getElementById('manualProgressionForm'));
    
    fetch('{{ route("admin.kenaikan_kelas.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            document.getElementById('manualProgressionModal').querySelector('[data-bs-dismiss="modal"]').click();
            loadStatistics();
            loadProgressionData();
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error processing manual progression:', error);
        showAlert('Error processing manual progression', 'danger');
    });
}

function showBulkProcessResults(results) {
    let message = `Processing completed!\n\n`;
    message += `✅ Promoted: ${results.promoted_count} students\n`;
    message += `❌ Not Promoted: ${results.not_promoted_count} students\n\n`;
    
    if (results.promoted.length > 0) {
        message += `Promoted students:\n${results.promoted.join(', ')}\n\n`;
    }
    
    if (results.not_promoted.length > 0) {
        message += `Not promoted students:\n${results.not_promoted.join(', ')}`;
    }
    
    alert(message);
}

function toggleAllStudents() {
    const masterCheckbox = document.getElementById('selectAllCheckbox');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    
    studentCheckboxes.forEach(checkbox => {
        checkbox.checked = masterCheckbox.checked;
    });
}

function selectAllStudents() {
    document.getElementById('selectAllCheckbox').checked = true;
    toggleAllStudents();
}

function clearAllSelections() {
    document.getElementById('selectAllCheckbox').checked = false;
    toggleAllStudents();
}

function viewProgressionDetail(id) {
    // Load progression details
    const detailContent = document.getElementById('detailContent');
    detailContent.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6><i class="ti ti-user me-1"></i>Student Information</h6>
                <table class="table table-borderless table-sm">
                    <tr><th width="40%">Name:</th><td><strong>Ahmad Budi Santoso</strong></td></tr>
                    <tr><th>NISN:</th><td>1234567890</td></tr>
                    <tr><th>Current Class:</th><td><span class="badge bg-secondary">XI IPA 1</span></td></tr>
                    <tr><th>Target Class:</th><td><span class="badge bg-primary">XII IPA 1</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6><i class="ti ti-chart-bar me-1"></i>Progression Details</h6>
                <table class="table table-borderless table-sm">
                    <tr><th width="40%">Status:</th><td><span class="status-badge status-naik">Promoted</span></td></tr>
                    <tr><th>Academic Year:</th><td>2024/2025</td></tr>
                    <tr><th>Processing Date:</th><td>July 15, 2024</td></tr>
                    <tr><th>Criteria Score:</th><td><span class="criteria-score score-high">85/100</span></td></tr>
                </table>
            </div>
        </div>
        
        <hr>
        
        <h6><i class="ti ti-list-check me-1"></i>Evaluation Criteria</h6>
        <div class="row">
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body text-center">
                        <div class="h3 text-success">5</div>
                        <div class="text-muted">Total Achievements</div>
                        <small class="text-success">✓ Requirement: ≥1</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body text-center">
                        <div class="h3 text-success">82.5</div>
                        <div class="text-muted">Average Grade</div>
                        <small class="text-success">✓ Requirement: ≥75</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-sm">
                    <div class="card-body text-center">
                        <div class="h3 text-success">2</div>
                        <div class="text-muted">Non-Academic</div>
                        <small class="text-success">✓ Requirement: ≥1</small>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();
}

function editProgression(id) {
    // Implementation for editing progression
    alert('Edit functionality would open an edit modal here');
}

function exportToExcel() {
    // Implementation for Excel export
    alert('Excel export functionality would be implemented here');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

function showAlert(message, type) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        if (alert && alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endpush
@endsection