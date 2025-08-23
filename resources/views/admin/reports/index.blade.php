@extends('layouts.app')

@section('title', 'Advanced Reports')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h2 class="page-title">
                    <i class="ti ti-file-report me-2"></i>Advanced Reports System
                </h2>
                <div class="page-subtitle">Generate comprehensive achievement reports with multiple formats</div>
            </div>
        </div>
    </div>

    <!-- Report Type Selection -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Report Generator</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Report Type</label>
                            <select class="form-select" id="reportType">
                                <option value="">Select Report Type...</option>
                                <option value="student">Individual Student Report</option>
                                <option value="class">Class Performance Report</option>
                                <option value="school">School-wide Report</option>
                                <option value="multi-year">Multi-Year Comparison Report</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Academic Year</label>
                            <select class="form-select" id="academicYearReport">
                                <option value="">All Academic Years</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="studentSelectReport" style="display: none;">
                            <label class="form-label">Select Student</label>
                            <select class="form-select" id="studentIdReport">
                                <option value="">Choose Student...</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="classSelectReport" style="display: none;">
                            <label class="form-label">Select Class</label>
                            <select class="form-select" id="classIdReport">
                                <option value="">Choose Class...</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-3">
                        <div class="col-md-3">
                            <label class="form-label">Date Range</label>
                            <select class="form-select" id="dateRange">
                                <option value="">All Time</option>
                                <option value="current_year">Current Academic Year</option>
                                <option value="last_year">Last Academic Year</option>
                                <option value="last_6_months">Last 6 Months</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-3" id="customDateContainer" style="display: none;">
                            <label class="form-label">Custom Date Range</label>
                            <div class="input-group">
                                <input type="date" class="form-control" id="startDate" placeholder="Start Date">
                                <input type="date" class="form-control" id="endDate" placeholder="End Date">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Export Format</label>
                            <select class="form-select" id="exportFormat">
                                <option value="pdf">PDF Document</option>
                                <option value="excel">Excel Spreadsheet</option>
                                <option value="preview">Preview Only</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-primary w-100" onclick="generateReport()">
                                <i class="ti ti-download me-1"></i>Generate Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Quick Reports</h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-primary" onclick="quickReport('current_year_summary')">
                                    <i class="ti ti-calendar-stats me-1"></i>
                                    Current Year Summary
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-success" onclick="quickReport('top_performers')">
                                    <i class="ti ti-trophy me-1"></i>
                                    Top Performers
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-info" onclick="quickReport('class_comparison')">
                                    <i class="ti ti-users me-1"></i>
                                    Class Comparison
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <button class="btn btn-outline-warning" onclick="quickReport('extracurricular_report')">
                                    <i class="ti ti-activity me-1"></i>
                                    Extracurricular Report
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Preview -->
    <div id="reportPreview" style="display: none;">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Report Preview</h4>
                        <div class="card-actions">
                            <button class="btn btn-sm btn-primary" onclick="downloadReport('pdf')">
                                <i class="ti ti-file-type-pdf me-1"></i>Download PDF
                            </button>
                            <button class="btn btn-sm btn-success" onclick="downloadReport('excel')">
                                <i class="ti ti-file-type-xls me-1"></i>Download Excel
                            </button>
                            <button class="btn btn-sm btn-secondary" onclick="closePreview()">
                                <i class="ti ti-x me-1"></i>Close
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="reportContent">
                            <!-- Report content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Reports</h4>
                </div>
                <div class="card-body">
                    <div id="recentReports">
                        <div class="d-flex align-items-center justify-content-center py-5">
                            <div class="text-center">
                                <i class="ti ti-file-report text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">No recent reports generated yet</p>
                                <p class="text-muted small">Generate your first report using the tools above</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="reportLoadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.5); z-index: 9999; display: none !important;">
        <div class="card">
            <div class="card-body text-center">
                <div class="spinner-border text-primary mb-3"></div>
                <h5>Generating Report...</h5>
                <p class="text-muted">Please wait while we prepare your report</p>
            </div>
        </div>
    </div>
</div>

<style>
.page-header {
    margin-bottom: 2rem;
}

.page-title {
    margin: 0;
    color: var(--bs-dark);
    font-weight: 600;
}

.page-subtitle {
    color: var(--bs-secondary);
    margin-top: 0.25rem;
}

.card-actions .btn {
    margin-left: 0.5rem;
}

#reportLoadingOverlay {
    backdrop-filter: blur(4px);
}

.report-metric {
    text-align: center;
    padding: 1rem;
    border: 1px solid var(--bs-border-color);
    border-radius: 0.375rem;
    margin-bottom: 1rem;
}

.report-metric-value {
    font-size: 2rem;
    font-weight: bold;
    color: var(--bs-primary);
}

.report-metric-label {
    font-size: 0.875rem;
    color: var(--bs-secondary);
    font-weight: 500;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the reports page
    initializeReports();
    
    // Setup event listeners
    setupReportEventListeners();
});

function initializeReports() {
    // Load academic years
    fetch('{{ route("admin.tahun_ajaran.for_select") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('academicYearReport');
                data.data.forEach(year => {
                    const option = document.createElement('option');
                    option.value = year.value;
                    option.textContent = year.label;
                    if (year.is_active) option.selected = true;
                    select.appendChild(option);
                });
            }
        });
    
    // Load students list
    fetch('{{ route("admin.analytics.students_list") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('studentIdReport');
                data.students.forEach(student => {
                    const option = document.createElement('option');
                    option.value = student.id;
                    option.textContent = `${student.nama} - ${student.kelas}`;
                    select.appendChild(option);
                });
            }
        });

    // Load classes list
    fetch('{{ route("admin.kelas.index") }}')
        .then(response => response.json())
        .then(data => {
            // This would need a specific endpoint for classes list
            // For now we'll leave it empty
        })
        .catch(error => {
            console.log('Classes endpoint not available');
        });
}

function setupReportEventListeners() {
    // Report type change
    document.getElementById('reportType').addEventListener('change', function() {
        const studentContainer = document.getElementById('studentSelectReport');
        const classContainer = document.getElementById('classSelectReport');
        
        // Hide all containers first
        studentContainer.style.display = 'none';
        classContainer.style.display = 'none';
        
        // Show relevant container
        if (this.value === 'student') {
            studentContainer.style.display = 'block';
        } else if (this.value === 'class') {
            classContainer.style.display = 'block';
        }
    });
    
    // Date range change
    document.getElementById('dateRange').addEventListener('change', function() {
        const customContainer = document.getElementById('customDateContainer');
        if (this.value === 'custom') {
            customContainer.style.display = 'block';
        } else {
            customContainer.style.display = 'none';
        }
    });
}

function showReportLoading() {
    document.getElementById('reportLoadingOverlay').style.display = 'flex';
}

function hideReportLoading() {
    document.getElementById('reportLoadingOverlay').style.display = 'none';
}

function generateReport() {
    const reportType = document.getElementById('reportType').value;
    const academicYear = document.getElementById('academicYearReport').value;
    const studentId = document.getElementById('studentIdReport').value;
    const classId = document.getElementById('classIdReport').value;
    const dateRange = document.getElementById('dateRange').value;
    const exportFormat = document.getElementById('exportFormat').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!reportType) {
        alert('Please select a report type');
        return;
    }
    
    if (reportType === 'student' && !studentId) {
        alert('Please select a student');
        return;
    }
    
    if (reportType === 'class' && !classId) {
        alert('Please select a class');
        return;
    }
    
    showReportLoading();
    
    // Prepare request data
    const requestData = {
        academic_year: academicYear,
        date_range: dateRange,
        start_date: startDate,
        end_date: endDate,
        student_id: studentId,
        class_id: classId,
        format: exportFormat
    };
    
    let endpoint = '';
    switch(reportType) {
        case 'student':
            endpoint = '{{ route("admin.reports.student") }}';
            break;
        case 'class':
            endpoint = '{{ route("admin.reports.class") }}';
            break;
        case 'school':
            endpoint = '{{ route("admin.reports.school") }}';
            break;
        case 'multi-year':
            endpoint = '{{ route("admin.reports.multi_year_comparison") }}';
            break;
    }
    
    // Convert data to URL parameters
    const params = new URLSearchParams(requestData);
    
    if (exportFormat === 'preview') {
        // Load preview
        fetch(endpoint + '?' + params.toString())
            .then(response => response.text())
            .then(html => {
                document.getElementById('reportContent').innerHTML = html;
                document.getElementById('reportPreview').style.display = 'block';
                hideReportLoading();
                
                // Scroll to preview
                document.getElementById('reportPreview').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating report preview');
                hideReportLoading();
            });
    } else {
        // Download report
        const downloadUrl = endpoint + '?' + params.toString();
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.download = '';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        setTimeout(() => {
            hideReportLoading();
        }, 2000);
    }
}

function quickReport(type) {
    const reportMapping = {
        'current_year_summary': {
            reportType: 'school',
            dateRange: 'current_year',
            format: 'preview'
        },
        'top_performers': {
            reportType: 'school',
            dateRange: 'current_year',
            format: 'preview'
        },
        'class_comparison': {
            reportType: 'school',
            dateRange: 'current_year',
            format: 'preview'
        },
        'extracurricular_report': {
            reportType: 'school',
            dateRange: 'current_year',
            format: 'preview'
        }
    };
    
    const config = reportMapping[type];
    if (config) {
        // Set form values
        document.getElementById('reportType').value = config.reportType;
        document.getElementById('dateRange').value = config.dateRange;
        document.getElementById('exportFormat').value = config.format;
        
        // Generate report
        generateReport();
    }
}

function downloadReport(format) {
    const reportType = document.getElementById('reportType').value;
    const academicYear = document.getElementById('academicYearReport').value;
    const studentId = document.getElementById('studentIdReport').value;
    const classId = document.getElementById('classIdReport').value;
    const dateRange = document.getElementById('dateRange').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    const requestData = {
        academic_year: academicYear,
        date_range: dateRange,
        start_date: startDate,
        end_date: endDate,
        student_id: studentId,
        class_id: classId,
        format: format
    };
    
    let endpoint = '';
    switch(reportType) {
        case 'student':
            endpoint = '{{ route("admin.reports.student") }}';
            break;
        case 'class':
            endpoint = '{{ route("admin.reports.class") }}';
            break;
        case 'school':
            endpoint = '{{ route("admin.reports.school") }}';
            break;
        case 'multi-year':
            endpoint = '{{ route("admin.reports.multi_year_comparison") }}';
            break;
    }
    
    const params = new URLSearchParams(requestData);
    const downloadUrl = endpoint + '?' + params.toString();
    
    const link = document.createElement('a');
    link.href = downloadUrl;
    link.download = '';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function closePreview() {
    document.getElementById('reportPreview').style.display = 'none';
}
</script>
@endpush
@endsection