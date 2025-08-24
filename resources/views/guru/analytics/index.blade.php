@extends('layouts.app')

@section('title', 'Analytics Kelas - Guru')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-chart-line mr-2"></i>Analytics Kelas
    </h1>
    <div>
        <select id="academicYearFilter" class="form-control d-inline-block" style="width: auto;">
            <option value="">Semua Tahun Ajaran</option>
            @foreach($academicYears as $year)
                <option value="{{ $year->id }}" {{ ($currentYear && $currentYear->id == $year->id) ? 'selected' : '' }}>
                    {{ $year->nama_tahun_ajaran }} {{ $year->is_active ? '(Aktif)' : '' }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<!-- Analysis Mode Selection -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="btn-group w-100" role="group">
                    <button type="button" class="btn btn-primary active" data-mode="overview">
                        <i class="fas fa-tachometer-alt mr-1"></i>Overview Kelas
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-mode="individual">
                        <i class="fas fa-user-graduate mr-1"></i>Analisis Siswa
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-mode="performance">
                        <i class="fas fa-chart-bar mr-1"></i>Performa Kelas
                    </button>
                    <button type="button" class="btn btn-outline-primary" data-mode="progression">
                        <i class="fas fa-arrow-up mr-1"></i>Tracking Progress
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overview Mode -->
<div id="overviewMode" class="analysis-mode">
    <!-- Class Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Kelas Diampu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalKelas">{{ $kelas->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Siswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalSiswa">{{ $siswa->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Prestasi Diterima</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800" id="prestasiDiterima">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-medal fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Rata-rata per Siswa</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="rataRataPerSiswa">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Class Performance Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Perbandingan Performa Kelas</h6>
                </div>
                <div class="card-body">
                    <div id="classComparisonChart"></div>
                </div>
            </div>
        </div>

        <!-- Achievement Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Prestasi</h6>
                </div>
                <div class="card-body">
                    <div id="achievementTypeChart"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Top 10 Siswa Berprestasi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="topPerformersTable">
                    <thead>
                        <tr>
                            <th>Ranking</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Total Prestasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Individual Student Analysis Mode -->
<div id="individualMode" class="analysis-mode" style="display: none;">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="studentSelect">Pilih Siswa:</label>
                            <select id="studentSelect" class="form-control">
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($siswa as $student)
                                    <option value="{{ $student->id }}">{{ $student->nama }} ({{ $student->kelas->nama_kelas ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>&nbsp;</label>
                            <button class="btn btn-primary d-block" id="analyzeStudentBtn">
                                <i class="fas fa-search mr-1"></i>Analisis Siswa
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Analysis Results -->
    <div id="studentAnalysisResults" style="display: none;">
        <!-- Student Basic Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Informasi Siswa</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6" id="studentBasicInfo">
                        <!-- Will be populated by JavaScript -->
                    </div>
                    <div class="col-md-6" id="studentAchievementSummary">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Charts -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Timeline Prestasi</h6>
                    </div>
                    <div class="card-body">
                        <div id="studentTimelineChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Kategori Prestasi</h6>
                    </div>
                    <div class="card-body">
                        <div id="studentCategoryChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Rekomendasi Pengembangan</h6>
            </div>
            <div class="card-body">
                <div id="studentRecommendations">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Class Performance Analysis Mode -->
<div id="performanceMode" class="analysis-mode" style="display: none;">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="classSelect">Filter Kelas (Opsional):</label>
                            <select id="classSelect" class="form-control">
                                <option value="">Semua Kelas</option>
                                @foreach($kelas as $class)
                                    <option value="{{ $class->id }}">{{ $class->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>&nbsp;</label>
                            <button class="btn btn-primary d-block" id="analyzeClassBtn">
                                <i class="fas fa-chart-bar mr-1"></i>Analisis Kelas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Class Performance Results -->
    <div id="classAnalysisResults" style="display: none;">
        <!-- Class Metrics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Siswa</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="classMetricsTotalStudents">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Prestasi</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="classMetricsTotalAchievements">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-trophy fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rata-rata</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="classMetricsAverage">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calculator fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Kelas Terbaik</div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800" id="classMetricsTopClass">-</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-star fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
            <div class="col-xl-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Trend Bulanan</h6>
                    </div>
                    <div class="card-body">
                        <div id="classMonthlyTrendsChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Distribusi Level</h6>
                    </div>
                    <div class="card-body">
                        <div id="classCompetitionChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top and Low Performers -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-success">Top Performers</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm" id="classTopPerformersTable">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                        <th>Prestasi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-warning">Perlu Perhatian</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm" id="classUnderperformersTable">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Kelas</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="improvementAreas" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Progression Mode -->
<div id="progressionMode" class="analysis-mode" style="display: none;">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="progressionStudentSelect">Pilih Siswa untuk Tracking:</label>
                            <select id="progressionStudentSelect" class="form-control">
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($siswa as $student)
                                    <option value="{{ $student->id }}">{{ $student->nama }} ({{ $student->kelas->nama_kelas ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>&nbsp;</label>
                            <button class="btn btn-primary d-block" id="trackProgressBtn">
                                <i class="fas fa-chart-line mr-1"></i>Track Progress
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Progression Results -->
    <div id="progressionResults" style="display: none;">
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Progres Akademik</h6>
                    </div>
                    <div class="card-body">
                        <div id="academicProgressionChart"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Pertumbuhan Prestasi</h6>
                    </div>
                    <div class="card-body">
                        <div id="achievementGrowthChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Milestone Achievements</h6>
                    </div>
                    <div class="card-body">
                        <div id="milestonesList"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Prediksi Masa Depan</h6>
                    </div>
                    <div class="card-body">
                        <div id="futurePredictions"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="spinner-border text-light" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
$(document).ready(function() {
    let currentAcademicYear = '{{ $currentYear ? $currentYear->id : "" }}';
    
    // Initialize with overview data
    loadOverviewData();
    
    // Mode switching
    $('.btn-group button').on('click', function() {
        const mode = $(this).data('mode');
        
        // Update button states
        $('.btn-group button').removeClass('btn-primary active').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary active');
        
        // Show/hide mode panels
        $('.analysis-mode').hide();
        $(`#${mode}Mode`).show();
        
        // Load appropriate data
        switch(mode) {
            case 'overview':
                loadOverviewData();
                break;
            case 'individual':
                // Data will be loaded when student is selected
                break;
            case 'performance':
                // Data will be loaded when analyze button is clicked
                break;
            case 'progression':
                // Data will be loaded when student is selected
                break;
        }
    });
    
    // Academic year filter change
    $('#academicYearFilter').on('change', function() {
        currentAcademicYear = $(this).val();
        const activeMode = $('.btn-group .btn-primary').data('mode');
        
        if (activeMode === 'overview') {
            loadOverviewData();
        }
    });
    
    // Student analysis
    $('#analyzeStudentBtn').on('click', function() {
        const studentId = $('#studentSelect').val();
        if (!studentId) {
            alert('Silakan pilih siswa terlebih dahulu');
            return;
        }
        loadStudentAnalysis(studentId);
    });
    
    // Class performance analysis
    $('#analyzeClassBtn').on('click', function() {
        const classId = $('#classSelect').val();
        loadClassPerformanceAnalysis(classId);
    });
    
    // Student progression tracking
    $('#trackProgressBtn').on('click', function() {
        const studentId = $('#progressionStudentSelect').val();
        if (!studentId) {
            alert('Silakan pilih siswa terlebih dahulu');
            return;
        }
        loadStudentProgression(studentId);
    });
    
    function showLoading() {
        $('#loadingOverlay').show();
    }
    
    function hideLoading() {
        $('#loadingOverlay').hide();
    }
    
    function loadOverviewData() {
        showLoading();
        
        // This would typically be loaded from the dashboard controller
        // For now, we'll simulate with existing data
        updateOverviewStats();
        renderClassComparisonChart();
        renderAchievementTypeChart();
        loadTopPerformers();
        
        hideLoading();
    }
    
    function updateOverviewStats() {
        // These would be updated via AJAX calls to get real-time data
        // For now showing static data from the controller
    }
    
    function renderClassComparisonChart() {
        const options = {
            series: [{
                name: 'Rata-rata Prestasi per Siswa',
                data: [] // Would be populated from dashboard data
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            xaxis: {
                categories: [] // Class names
            },
            title: {
                text: 'Perbandingan Performa Antar Kelas'
            }
        };
        
        const chart = new ApexCharts(document.querySelector("#classComparisonChart"), options);
        chart.render();
    }
    
    function renderAchievementTypeChart() {
        const options = {
            series: [44, 55], // Example data
            chart: {
                type: 'donut',
                height: 300
            },
            labels: ['Akademik', 'Non-Akademik'],
            colors: ['#4e73df', '#1cc88a'],
            legend: {
                position: 'bottom'
            }
        };
        
        const chart = new ApexCharts(document.querySelector("#achievementTypeChart"), options);
        chart.render();
    }
    
    function loadTopPerformers() {
        // This would load from the enhanced dashboard data
        const tbody = $('#topPerformersTable tbody');
        tbody.html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
        
        // Simulate loading top performers
        setTimeout(() => {
            tbody.html(`
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Data akan dimuat setelah implementasi lengkap
                    </td>
                </tr>
            `);
        }, 500);
    }
    
    function loadStudentAnalysis(studentId) {
        showLoading();
        
        $.get(`/guru/analytics/student/${studentId}`, {
            academic_year: currentAcademicYear
        })
        .done(function(response) {
            if (response.success) {
                displayStudentAnalysis(response);
            } else {
                alert('Gagal memuat data analisis siswa');
            }
        })
        .fail(function() {
            alert('Error: Tidak dapat memuat data analisis siswa');
        })
        .always(function() {
            hideLoading();
        });
    }
    
    function displayStudentAnalysis(data) {
        $('#studentAnalysisResults').show();
        
        // Display basic info
        $('#studentBasicInfo').html(`
            <h6>Informasi Dasar</h6>
            <p><strong>Nama:</strong> ${data.student.nama}</p>
            <p><strong>NISN:</strong> ${data.student.nisn}</p>
            <p><strong>Kelas:</strong> ${data.analysis.basic_info.kelas}</p>
            <p><strong>Jenis Kelamin:</strong> ${data.analysis.basic_info.jenis_kelamin}</p>
        `);
        
        // Display achievement summary
        const summary = data.analysis.achievement_summary;
        $('#studentAchievementSummary').html(`
            <h6>Ringkasan Prestasi</h6>
            <p><strong>Total:</strong> ${summary.total_prestasi}</p>
            <p><strong>Diterima:</strong> ${summary.diterima}</p>
            <p><strong>Pending:</strong> ${summary.pending}</p>
            <p><strong>Tingkat Penerimaan:</strong> ${summary.acceptance_rate}%</p>
        `);
        
        // Display recommendations
        const recommendations = data.analysis.recommendations;
        let recHtml = '<div class="alert alert-info"><h6>Rekomendasi:</h6><ul>';
        recommendations.forEach(rec => {
            recHtml += `<li>${rec}</li>`;
        });
        recHtml += '</ul></div>';
        $('#studentRecommendations').html(recHtml);
        
        // Render charts
        renderStudentTimelineChart(data.analysis.timeline_data);
        renderStudentCategoryChart(data.analysis.category_breakdown);
    }
    
    function renderStudentTimelineChart(timelineData) {
        const chartData = timelineData.map(item => ({
            x: item.tanggal,
            y: 1,
            nama: item.nama_prestasi,
            kategori: item.kategori
        }));
        
        const options = {
            series: [{
                name: 'Prestasi',
                data: chartData
            }],
            chart: {
                type: 'scatter',
                height: 300
            },
            xaxis: {
                type: 'datetime',
                title: {
                    text: 'Tanggal'
                }
            },
            title: {
                text: 'Timeline Prestasi Siswa'
            }
        };
        
        const chart = new ApexCharts(document.querySelector("#studentTimelineChart"), options);
        chart.render();
    }
    
    function renderStudentCategoryChart(categoryData) {
        const series = Object.values(categoryData);
        const labels = Object.keys(categoryData);
        
        const options = {
            series: series,
            chart: {
                type: 'pie',
                height: 300
            },
            labels: labels,
            colors: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b'],
            legend: {
                position: 'bottom'
            }
        };
        
        const chart = new ApexCharts(document.querySelector("#studentCategoryChart"), options);
        chart.render();
    }
    
    function loadClassPerformanceAnalysis(classId) {
        showLoading();
        
        $.get('/guru/analytics/class-performance', {
            academic_year: currentAcademicYear,
            kelas_id: classId
        })
        .done(function(response) {
            if (response.success) {
                displayClassAnalysis(response);
            } else {
                alert('Gagal memuat data analisis kelas');
            }
        })
        .fail(function() {
            alert('Error: Tidak dapat memuat data analisis kelas');
        })
        .always(function() {
            hideLoading();
        });
    }
    
    function displayClassAnalysis(data) {
        $('#classAnalysisResults').show();
        
        const metrics = data.analysis.performance_metrics;
        $('#classMetricsTotalStudents').text(metrics.total_students);
        $('#classMetricsTotalAchievements').text(metrics.total_achievements);
        $('#classMetricsAverage').text(metrics.average_per_student);
        $('#classMetricsTopClass').text(metrics.top_performing_class ? metrics.top_performing_class.nama_kelas : '-');
        
        // Render charts and tables
        renderClassMonthlyTrendsChart(data.analysis.monthly_trends);
        renderClassCompetitionChart(data.analysis.achievement_distribution.by_level);
        displayClassTopPerformers(data.analysis.top_performers);
        displayClassUnderperformers(data.analysis.underperformers);
        displayImprovementAreas(data.analysis.improvement_areas);
    }
    
    function renderClassMonthlyTrendsChart(trendsData) {
        const chartData = trendsData.map(item => ({
            x: item.bulan,
            y: item.total
        }));
        
        const options = {
            series: [{
                name: 'Prestasi',
                data: chartData
            }],
            chart: {
                type: 'area',
                height: 350
            },
            xaxis: {
                type: 'datetime'
            },
            title: {
                text: 'Trend Prestasi Bulanan'
            }
        };
        
        const chart = new ApexCharts(document.querySelector("#classMonthlyTrendsChart"), options);
        chart.render();
    }
    
    function renderClassCompetitionChart(levelData) {
        const series = levelData.map(item => item.total);
        const labels = levelData.map(item => item.tingkat_kompetisi);
        
        const options = {
            series: series,
            chart: {
                type: 'donut',
                height: 300
            },
            labels: labels,
            legend: {
                position: 'bottom'
            }
        };
        
        const chart = new ApexCharts(document.querySelector("#classCompetitionChart"), options);
        chart.render();
    }
    
    function displayClassTopPerformers(performers) {
        const tbody = $('#classTopPerformersTable tbody');
        let html = '';
        
        performers.forEach(performer => {
            html += `
                <tr>
                    <td>${performer.nama}</td>
                    <td>${performer.kelas?.nama_kelas || '-'}</td>
                    <td>${performer.total_prestasi}</td>
                </tr>
            `;
        });
        
        tbody.html(html || '<tr><td colspan="3" class="text-center text-muted">Tidak ada data</td></tr>');
    }
    
    function displayClassUnderperformers(underperformers) {
        const tbody = $('#classUnderperformersTable tbody');
        let html = '';
        
        underperformers.forEach(student => {
            html += `
                <tr>
                    <td>${student.nama}</td>
                    <td>${student.kelas?.nama_kelas || '-'}</td>
                    <td>Belum ada prestasi</td>
                </tr>
            `;
        });
        
        tbody.html(html || '<tr><td colspan="3" class="text-center text-muted">Semua siswa sudah berprestasi!</td></tr>');
    }
    
    function displayImprovementAreas(areas) {
        let html = '<h6>Area yang Perlu Perbaikan:</h6>';
        if (areas.length > 0) {
            html += '<ul>';
            areas.forEach(area => {
                html += `<li>${area}</li>`;
            });
            html += '</ul>';
        } else {
            html += '<p class="text-success">Semua area sudah baik!</p>';
        }
        
        $('#improvementAreas').html(html);
    }
    
    function loadStudentProgression(studentId) {
        showLoading();
        
        $.get(`/guru/analytics/student-progression/${studentId}`)
        .done(function(response) {
            if (response.success) {
                displayStudentProgression(response);
            } else {
                alert('Gagal memuat data progres siswa');
            }
        })
        .fail(function() {
            alert('Error: Tidak dapat memuat data progres siswa');
        })
        .always(function() {
            hideLoading();
        });
    }
    
    function displayStudentProgression(data) {
        $('#progressionResults').show();
        
        renderAcademicProgressionChart(data.progression.academic_progression);
        renderAchievementGrowthChart(data.progression.achievement_growth);
        displayMilestones(data.progression.milestone_achievements);
        displayFuturePredictions(data.progression.future_predictions);
    }
    
    function renderAcademicProgressionChart(progressionData) {
        const chartData = progressionData.map(item => ({
            x: item.nama_tahun_ajaran,
            y: item.total
        }));
        
        const options = {
            series: [{
                name: 'Prestasi Akademik',
                data: chartData
            }],
            chart: {
                type: 'line',
                height: 300
            },
            xaxis: {
                title: {
                    text: 'Tahun Ajaran'
                }
            },
            title: {
                text: 'Progres Prestasi Akademik'
            }
        };
        
        const chart = new ApexCharts(document.querySelector("#academicProgressionChart"), options);
        chart.render();
    }
    
    function renderAchievementGrowthChart(growthData) {
        const chartData = growthData.map(item => ({
            x: item.tahun.toString(),
            y: item.total
        }));
        
        const options = {
            series: [{
                name: 'Total Prestasi',
                data: chartData
            }],
            chart: {
                type: 'bar',
                height: 300
            },
            xaxis: {
                title: {
                    text: 'Tahun'
                }
            },
            title: {
                text: 'Pertumbuhan Prestasi per Tahun'
            }
        };
        
        const chart = new ApexCharts(document.querySelector("#achievementGrowthChart"), options);
        chart.render();
    }
    
    function displayMilestones(milestones) {
        let html = '';
        milestones.forEach(milestone => {
            html += `
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary rounded-circle p-2 mr-3">
                        <i class="fas fa-trophy text-white"></i>
                    </div>
                    <div>
                        <strong>${milestone.description}</strong><br>
                        <small class="text-muted">${milestone.date || 'Tanggal tidak tersedia'}</small>
                    </div>
                </div>
            `;
        });
        
        $('#milestonesList').html(html || '<p class="text-muted">Belum ada milestone yang dicapai</p>');
    }
    
    function displayFuturePredictions(predictions) {
        let html = '';
        predictions.forEach(prediction => {
            html += `<div class="alert alert-info">${prediction}</div>`;
        });
        
        $('#futurePredictions').html(html || '<p class="text-muted">Tidak ada prediksi tersedia</p>');
    }
});
</script>
@endpush