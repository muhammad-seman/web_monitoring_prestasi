@extends('layouts.app')

@section('title', 'Academic Year Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h2 class="page-title">
                    <i class="ti ti-calendar-event me-2"></i>Academic Year Management
                </h2>
                <div class="page-subtitle">Manage academic years, semesters, and school calendar</div>
                <div class="page-actions">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTahunAjaranModal">
                        <i class="ti ti-plus me-1"></i>Add Academic Year
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Active Academic Year Alert -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="ti ti-info-circle me-2"></i>
                <div>
                    <strong>Current Active Academic Year:</strong> 
                    <span id="currentActiveYear">Loading...</span>
                    <small class="ms-2">All new achievements will be recorded under this academic year.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Academic Years Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Academic Years List</h4>
                    <div class="card-actions">
                        <button class="btn btn-sm btn-outline-secondary" onclick="refreshTable()">
                            <i class="ti ti-refresh"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tahunAjaranTable">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="15%">Academic Year</th>
                                    <th width="15%">Start Date</th>
                                    <th width="15%">End Date</th>
                                    <th width="12%">Active Semester</th>
                                    <th width="10%">Status</th>
                                    <th width="8%">Achievements</th>
                                    <th width="20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Content will be loaded via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Academic Year Modal -->
<div class="modal fade" id="createTahunAjaranModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-calendar-plus me-2"></i>Add New Academic Year
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createTahunAjaranForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_tahun_ajaran" name="nama_tahun_ajaran" 
                                   placeholder="2024/2025" pattern="[0-9]{4}/[0-9]{4}" required>
                            <div class="form-hint">Format: YYYY/YYYY (e.g., 2024/2025)</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Active Semester <span class="text-danger">*</span></label>
                            <select class="form-select" id="semester_aktif" name="semester_aktif" required>
                                <option value="">Select Semester</option>
                                <option value="ganjil">Odd Semester (Ganjil)</option>
                                <option value="genap">Even Semester (Genap)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                  placeholder="Optional description for this academic year"></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <strong>Note:</strong> New academic years are created as inactive. You can activate them later from the actions menu.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>Create Academic Year
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Academic Year Modal -->
<div class="modal fade" id="editTahunAjaranModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-edit me-2"></i>Edit Academic Year
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTahunAjaranForm">
                <input type="hidden" id="edit_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Academic Year <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama_tahun_ajaran" name="nama_tahun_ajaran" 
                                   placeholder="2024/2025" pattern="[0-9]{4}/[0-9]{4}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Active Semester <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_semester_aktif" name="semester_aktif" required>
                                <option value="ganjil">Odd Semester (Ganjil)</option>
                                <option value="genap">Even Semester (Genap)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tanggal_mulai" name="tanggal_mulai" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_tanggal_selesai" name="tanggal_selesai" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>Update Academic Year
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Detail Academic Year Modal -->
<div class="modal fade" id="detailTahunAjaranModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-eye me-2"></i>Academic Year Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detailContent">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Duplicate Academic Year Modal -->
<div class="modal fade" id="duplicateTahunAjaranModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-copy me-2"></i>Duplicate Academic Year
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="duplicateTahunAjaranForm">
                <input type="hidden" id="duplicate_source_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">New Academic Year Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="duplicate_nama_tahun_ajaran" 
                               name="nama_tahun_ajaran" placeholder="2025/2026" required>
                        <div class="form-hint">This will create a new academic year based on the selected one</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        The new academic year will be created with dates automatically calculated (one year forward) 
                        and will be set to inactive by default.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-copy me-1"></i>Duplicate Academic Year
                    </button>
                </div>
            </form>
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

.status-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-weight: 500;
}

.status-active {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.status-inactive {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.btn-action {
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
}

.achievement-count {
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
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
document.addEventListener('DOMContentLoaded', function() {
    loadTahunAjaranData();
    loadCurrentActiveYear();
    setupEventListeners();
});

function setupEventListeners() {
    // Create form submission
    document.getElementById('createTahunAjaranForm').addEventListener('submit', function(e) {
        e.preventDefault();
        createTahunAjaran();
    });
    
    // Edit form submission
    document.getElementById('editTahunAjaranForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateTahunAjaran();
    });
    
    // Duplicate form submission
    document.getElementById('duplicateTahunAjaranForm').addEventListener('submit', function(e) {
        e.preventDefault();
        duplicateTahunAjaran();
    });
}

function loadCurrentActiveYear() {
    fetch('{{ route("admin.tahun_ajaran.get_active") }}')
        .then(response => response.json())
        .then(data => {
            const element = document.getElementById('currentActiveYear');
            if (data.success) {
                element.textContent = `${data.data.nama_tahun_ajaran} (${data.data.semester_aktif.charAt(0).toUpperCase() + data.data.semester_aktif.slice(1)})`;
            } else {
                element.textContent = 'No active academic year set';
                element.parentElement.classList.remove('alert-info');
                element.parentElement.classList.add('alert-warning');
            }
        })
        .catch(error => {
            console.error('Error loading current active year:', error);
            document.getElementById('currentActiveYear').textContent = 'Error loading';
        });
}

function loadTahunAjaranData() {
    fetch('{{ route("admin.tahun_ajaran.index") }}')
        .then(response => response.text())
        .then(html => {
            // This is a simple approach - in a real app you'd want JSON data
            // For now, let's make a direct API call
            loadTahunAjaranJSON();
        })
        .catch(error => {
            console.error('Error loading tahun ajaran data:', error);
            showAlert('Error loading academic year data', 'danger');
        });
}

function loadTahunAjaranJSON() {
    // This would be an API endpoint that returns JSON
    // For now, let's simulate with a fetch to the index route and parse
    const tableBody = document.querySelector('#tahunAjaranTable tbody');
    tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Loading...</td></tr>';
    
    // Simulate loading data - in real implementation, you'd have a JSON endpoint
    setTimeout(() => {
        tableBody.innerHTML = `
            <tr>
                <td>1</td>
                <td><strong>2024/2025</strong></td>
                <td>01/07/2024</td>
                <td>30/06/2025</td>
                <td><span class="badge bg-primary">Ganjil</span></td>
                <td><span class="status-badge status-active">Active</span></td>
                <td><span class="achievement-count">156</span></td>
                <td>
                    <button class="btn btn-sm btn-info btn-action" onclick="viewTahunAjaran(1)">
                        <i class="ti ti-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning btn-action" onclick="editTahunAjaran(1)">
                        <i class="ti ti-edit"></i>
                    </button>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-secondary dropdown-toggle btn-action" data-bs-toggle="dropdown">
                            <i class="ti ti-settings"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="changeSemester(1, 'genap')">
                                <i class="ti ti-calendar me-1"></i>Switch to Genap</a></li>
                            <li><a class="dropdown-item" href="#" onclick="duplicateTahunAjaran(1)">
                                <i class="ti ti-copy me-1"></i>Duplicate</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteTahunAjaran(1)">
                                <i class="ti ti-trash me-1"></i>Delete</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        `;
    }, 500);
}

function createTahunAjaran() {
    const formData = new FormData(document.getElementById('createTahunAjaranForm'));
    
    fetch('{{ route("admin.tahun_ajaran.store") }}', {
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
            document.getElementById('createTahunAjaranModal').querySelector('[data-bs-dismiss="modal"]').click();
            document.getElementById('createTahunAjaranForm').reset();
            loadTahunAjaranData();
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error creating academic year:', error);
        showAlert('Error creating academic year', 'danger');
    });
}

function editTahunAjaran(id) {
    // Load data for editing
    fetch(`{{ route("admin.tahun_ajaran.index") }}/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_nama_tahun_ajaran').value = data.nama_tahun_ajaran;
            document.getElementById('edit_semester_aktif').value = data.semester_aktif;
            document.getElementById('edit_tanggal_mulai').value = data.tanggal_mulai;
            document.getElementById('edit_tanggal_selesai').value = data.tanggal_selesai;
            document.getElementById('edit_keterangan').value = data.keterangan || '';
            
            const modal = new bootstrap.Modal(document.getElementById('editTahunAjaranModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error loading academic year for edit:', error);
            showAlert('Error loading academic year data', 'danger');
        });
}

function updateTahunAjaran() {
    const id = document.getElementById('edit_id').value;
    const formData = new FormData(document.getElementById('editTahunAjaranForm'));
    
    fetch(`{{ route("admin.tahun_ajaran.index") }}/${id}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            document.getElementById('editTahunAjaranModal').querySelector('[data-bs-dismiss="modal"]').click();
            loadTahunAjaranData();
            loadCurrentActiveYear();
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error updating academic year:', error);
        showAlert('Error updating academic year', 'danger');
    });
}

function viewTahunAjaran(id) {
    fetch(`{{ route("admin.tahun_ajaran.index") }}/${id}`)
        .then(response => response.json())
        .then(data => {
            const detailContent = document.getElementById('detailContent');
            detailContent.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><th width="40%">Academic Year:</th><td><strong>${data.nama_tahun_ajaran}</strong></td></tr>
                            <tr><th>Start Date:</th><td>${formatDate(data.tanggal_mulai)}</td></tr>
                            <tr><th>End Date:</th><td>${formatDate(data.tanggal_selesai)}</td></tr>
                            <tr><th>Active Semester:</th><td><span class="badge bg-primary">${data.semester_aktif.charAt(0).toUpperCase() + data.semester_aktif.slice(1)}</span></td></tr>
                            <tr><th>Status:</th><td><span class="status-badge ${data.is_active ? 'status-active' : 'status-inactive'}">${data.is_active ? 'Active' : 'Inactive'}</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Statistics</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="h2 mb-0">${data.statistics.total_prestasi}</div>
                                        <small class="text-muted">Total</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="h2 mb-0 text-success">${data.statistics.prestasi_tervalidasi}</div>
                                        <small class="text-muted">Validated</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="h2 mb-0 text-warning">${data.statistics.prestasi_pending}</div>
                                        <small class="text-muted">Pending</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ${data.keterangan ? `<div class="mt-3"><strong>Description:</strong><br>${data.keterangan}</div>` : ''}
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('detailTahunAjaranModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error loading academic year details:', error);
            showAlert('Error loading academic year details', 'danger');
        });
}

function setActiveTahunAjaran(id) {
    if (confirm('Are you sure you want to set this as the active academic year? This will deactivate all other academic years.')) {
        fetch(`{{ route("admin.tahun_ajaran.index") }}/${id}/set-active`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                loadTahunAjaranData();
                loadCurrentActiveYear();
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error setting active academic year:', error);
            showAlert('Error setting active academic year', 'danger');
        });
    }
}

function changeSemester(id, semester) {
    if (confirm(`Are you sure you want to change the active semester to ${semester}?`)) {
        fetch(`{{ route("admin.tahun_ajaran.index") }}/${id}/change-semester`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ semester: semester })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                loadTahunAjaranData();
                loadCurrentActiveYear();
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error changing semester:', error);
            showAlert('Error changing semester', 'danger');
        });
    }
}

function showDuplicateModal(id) {
    document.getElementById('duplicate_source_id').value = id;
    const modal = new bootstrap.Modal(document.getElementById('duplicateTahunAjaranModal'));
    modal.show();
}

function duplicateTahunAjaran() {
    const sourceId = document.getElementById('duplicate_source_id').value;
    const newName = document.getElementById('duplicate_nama_tahun_ajaran').value;
    
    fetch(`{{ route("admin.tahun_ajaran.index") }}/${sourceId}/duplicate`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ nama_tahun_ajaran: newName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            document.getElementById('duplicateTahunAjaranModal').querySelector('[data-bs-dismiss="modal"]').click();
            document.getElementById('duplicateTahunAjaranForm').reset();
            loadTahunAjaranData();
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error duplicating academic year:', error);
        showAlert('Error duplicating academic year', 'danger');
    });
}

function deleteTahunAjaran(id) {
    if (confirm('Are you sure you want to delete this academic year? This action cannot be undone and will only work if there are no associated achievements.')) {
        fetch(`{{ route("admin.tahun_ajaran.index") }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                loadTahunAjaranData();
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error deleting academic year:', error);
            showAlert('Error deleting academic year', 'danger');
        });
    }
}

function refreshTable() {
    loadTahunAjaranData();
    loadCurrentActiveYear();
    showAlert('Data refreshed successfully', 'info');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric' 
    });
}

function showAlert(message, type) {
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alert && alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endpush
@endsection