<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Tiga Putra Management System'); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('favicon.ico')); ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }
        .main-content {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
            border-radius: 12px 12px 0 0 !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(44, 62, 80, 0.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 62, 80, 0.4);
        }
        
        /* Logo styling - Round with white background */
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .logo-round {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            padding: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border: 3px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        
        .logo-round:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        .logo-round img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }
        
        /* Professional table styling */
        .table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .table thead th {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }
        
        .table tbody td {
            border-color: #e9ecef;
            vertical-align: middle;
        }
        
        /* Professional form styling */
        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #2c3e50;
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
        }
        
        /* Professional badge styling */
        .badge {
            border-radius: 20px;
            font-weight: 500;
            padding: 0.5em 0.8em;
        }
        
        /* Professional alert styling */
        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        /* Professional statistics cards */
        .stats-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid #2c3e50;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .stats-card .card-title {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        
        .stats-card h4 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0;
        }
        
        /* Professional button styling */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
        }
        
        .btn-warning:hover {
            background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4);
        }
        
        /* Compact pagination styling */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.125rem;
            margin: 0.5rem 0;
            font-size: 0.75rem;
        }

        .pagination .page-link {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.2;
            border-radius: 0.25rem;
            border: 1px solid #d1d5db;
            color: #374151;
            text-decoration: none;
            background-color: #ffffff;
            transition: all 0.2s ease;
            min-width: 32px;
            height: 32px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pagination .page-link:hover {
            background-color: #f3f4f6;
            border-color: #9ca3af;
            color: #111827;
        }

        .pagination .page-item.active .page-link {
            background-color: #2563eb;
            border-color: #2563eb;
            color: #ffffff;
        }

        .pagination .page-item.disabled .page-link {
            background-color: #f9fafb;
            border-color: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
        }

        /* Make pagination container more compact */
        .pagination .page-item {
            margin: 0;
        }

        /* Reduce pagination info text size */
        .pagination-info {
            font-size: 0.75rem;
            color: #6b7280;
            margin: 0.25rem 0;
        }

        /* Main content styling */
        .main-content {
            min-height: calc(100vh - 100px);
            padding-bottom: 1rem;
        }

        /* Ultra-compact table column sizing */
        .table {
            font-size: 0.8rem;
            table-layout: fixed;
        }

        .table th,
        .table td {
            padding: 0.375rem 0.25rem;
            vertical-align: middle;
            word-wrap: break-word;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Ultra-precise column widths for maximum space efficiency */
        .table th:nth-child(1),
        .table td:nth-child(1) {
            width: 4%;
            text-align: center;
        }

        .table th:nth-child(2),
        .table td:nth-child(2) {
            width: 18%;
        }

        .table th:nth-child(3),
        .table td:nth-child(3) {
            width: 8%;
            text-align: center;
        }

        .table th:nth-child(4),
        .table td:nth-child(4) {
            width: 12%;
            text-align: center;
        }

        .table th:nth-child(5),
        .table td:nth-child(5) {
            width: 10%;
            text-align: center;
        }

        .table th:nth-child(6),
        .table td:nth-child(6) {
            width: 8%;
            text-align: center;
        }

        .table th:nth-child(7),
        .table td:nth-child(7) {
            width: 8%;
            text-align: center;
        }

        .table th:nth-child(8),
        .table td:nth-child(8) {
            width: 8%;
            text-align: center;
        }

        .table th:nth-child(9),
        .table td:nth-child(9) {
            width: 12%;
            text-align: center;
        }

        .table th:nth-child(10),
        .table td:nth-child(10) {
            width: 12%;
            text-align: center;
        }

        /* Ultra-compact badges and buttons */
        .badge {
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
        }

        .btn-group-sm .btn {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }

        /* Compact form controls */
        .form-control {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }

        /* Compact cards */
        .card-body {
            padding: 1rem;
        }

        .card-header {
            padding: 0.75rem 1rem;
        }

        /* Responsive table adjustments */
        @media (max-width: 1200px) {
            .table {
                font-size: 0.8rem;
            }
            
            .table th,
            .table td {
                padding: 0.375rem 0.25rem;
            }
        }

        @media (max-width: 992px) {
            .table {
                font-size: 0.75rem;
            }
            
            .table th,
            .table td {
                padding: 0.25rem 0.125rem;
            }
        }

        /* Calendar Attendance specific styling - STRONG OVERRIDE for global table styles */
        .calendar-attendance-table {
            font-size: 0.875rem !important;
            table-layout: auto !important;
            width: auto !important;
        }

        .calendar-attendance-table th,
        .calendar-attendance-table td {
            padding: 0.5rem !important;
            text-align: center !important;
            width: auto !important;
            min-width: auto !important;
            background-color: #ffffff !important;
            color: #212529 !important;
            border: 1px solid #dee2e6 !important;
        }

        /* Override ALL nth-child selectors for calendar table */
        .calendar-attendance-table th:nth-child(1),
        .calendar-attendance-table td:nth-child(1),
        .calendar-attendance-table th:nth-child(2),
        .calendar-attendance-table td:nth-child(2),
        .calendar-attendance-table th:nth-child(3),
        .calendar-attendance-table td:nth-child(3),
        .calendar-attendance-table th:nth-child(4),
        .calendar-attendance-table td:nth-child(4),
        .calendar-attendance-table th:nth-child(5),
        .calendar-attendance-table td:nth-child(5),
        .calendar-attendance-table th:nth-child(6),
        .calendar-attendance-table td:nth-child(6),
        .calendar-attendance-table th:nth-child(7),
        .calendar-attendance-table td:nth-child(7),
        .calendar-attendance-table th:nth-child(8),
        .calendar-attendance-table td:nth-child(8),
        .calendar-attendance-table th:nth-child(9),
        .calendar-attendance-table td:nth-child(9),
        .calendar-attendance-table th:nth-child(10),
        .calendar-attendance-table td:nth-child(10) {
            width: auto !important;
            min-width: auto !important;
        }

        /* Make daily columns (4th to 34th) wider and visible */
        .calendar-attendance-table th:nth-child(n+4):nth-child(-n+34),
        .calendar-attendance-table td:nth-child(n+4):nth-child(-n+34) {
            min-width: 60px !important;
            width: 60px !important;
            background-color: #ffffff !important;
            color: #212529 !important;
            font-weight: 600 !important;
        }

        /* Make select boxes more visible with strong contrast */
        .calendar-attendance-table .form-control,
        .calendar-attendance-table select,
        .calendar-attendance-table select option {
            min-width: 50px !important;
            width: 50px !important;
            font-size: 0.8rem !important;
            background-color: #ffffff !important;
            color: #212529 !important;
            border: 1px solid #ced4da !important;
            padding: 0.25rem 0.5rem !important;
        }

        /* Force text color for all elements in calendar table */
        .calendar-attendance-table * {
            color: #212529 !important;
        }

        /* Specific styling for badge elements */
        .calendar-attendance-table .badge {
            background-color: #007bff !important;
            color: #ffffff !important;
        }

        .calendar-attendance-table .badge-primary {
            background-color: #007bff !important;
            color: #ffffff !important;
        }

        .calendar-attendance-table .badge-success {
            background-color: #28a745 !important;
            color: #ffffff !important;
        }

        /* Make table headers more visible */
        .calendar-attendance-table thead th {
            background-color: #f8f9fa !important;
            color: #212529 !important;
            font-weight: 600 !important;
            border: 1px solid #dee2e6 !important;
        }

        /* Make table scrollable horizontally */
        .calendar-attendance-container {
            overflow-x: auto !important;
        }

        /* Global fix for white text issues in all tables */
        .table td,
        .table th {
            color: #212529 !important;
        }

        .table .badge {
            color: #ffffff !important;
        }

        .table .badge-primary {
            background-color: #007bff !important;
            color: #ffffff !important;
        }

        .table .badge-success {
            background-color: #28a745 !important;
            color: #ffffff !important;
        }

        .table select,
        .table select option,
        .table .form-control {
            color: #212529 !important;
            background-color: #ffffff !important;
        }

        /* Fix table header text visibility */
        .table-dark th,
        .table-dark td,
        .thead-dark th {
            color: #ffffff !important;
            background-color: #343a40 !important;
        }

        .table-dark th {
            color: #ffffff !important;
            background-color: #343a40 !important;
        }
    </style>
    
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <div class="logo-container">
                <div class="logo-round">
                    <img src="<?php echo e(asset('logo.jpg')); ?>" alt="Tiga Putra Management">
                </div>
            </div>
            <h4 class="text-white fw-bold" style="text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                Tiga Putra Management
            </h4>
            <p class="text-white-50 small mb-0">Management System</p>
        </div>
                    
                    <ul class="nav flex-column">
                            <?php if(auth()->user()->isAdmin()): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('admin.gudangs.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('admin.gudangs.index')); ?>">
                                    <i class="bi bi-building"></i>
                                        Master Gudang
                                    </a>
                                </li>
                                
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('admin.karyawan-kandangs.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('admin.karyawan-kandangs.index')); ?>">
                                        <i class="bi bi-people-fill"></i>
                                        Master Karyawan Kandang
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('admin.lokasis.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('admin.lokasis.index')); ?>">
                                        <i class="bi bi-geo-alt"></i>
                                        Master Lokasi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('admin.kandangs.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('admin.kandangs.index')); ?>">
                                        <i class="bi bi-house"></i>
                                        Master Kandang
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('admin.pembibitans.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('admin.pembibitans.index')); ?>">
                                        <i class="bi bi-egg"></i>
                                        Master Pembibitan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('admin.absensis.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('admin.absensis.index')); ?>">
                                        <i class="bi bi-table"></i>
                                        Transaksi Absensi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('admin.employees.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('admin.employees.index')); ?>">
                                        <i class="bi bi-people"></i>
                                        Master Karyawan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('admin.manage') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('admin.manage')); ?>">
                                        <i class="bi bi-gear"></i>
                                        Manage
                                    </a>
                                </li>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('admin.salary-reports.*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('admin.salary-reports.index')); ?>">
                    <i class="bi bi-cash-stack"></i>
                    Laporan Gaji
                                    </a>
                                </li>
                            <?php elseif(auth()->user()->isManager()): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('manager.gudangs.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('manager.gudangs.index')); ?>">
                                    <i class="bi bi-building"></i>
                                        Master Gudang
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('manager.mandors.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('manager.mandors.index')); ?>">
                                        <i class="bi bi-person-badge"></i>
                                        Master Mandor
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('manager.karyawan-kandangs.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('manager.karyawan-kandangs.index')); ?>">
                                        <i class="bi bi-people-fill"></i>
                                        Master Karyawan Kandang
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('manager.lokasis.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('manager.lokasis.index')); ?>">
                                        <i class="bi bi-geo-alt"></i>
                                        Master Lokasi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('manager.kandangs.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('manager.kandangs.index')); ?>">
                                        <i class="bi bi-house"></i>
                                        Master Kandang
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('manager.pembibitans.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('manager.pembibitans.index')); ?>">
                                        <i class="bi bi-egg"></i>
                                        Master Pembibitan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo e(request()->routeIs('manager.absensis.*') ? 'active' : ''); ?>"
                                       href="<?php echo e(route('manager.absensis.index')); ?>">
                                        <i class="bi bi-table"></i>
                                        Transaksi Absensi
                                    </a>
                                </li>
            <li class="nav-item">
                <a class="nav-link <?php echo e(request()->routeIs('manager.salary-reports.*') ? 'active' : ''); ?>"
                   href="<?php echo e(route('manager.salary-reports.index')); ?>">
                    <i class="bi bi-cash-stack"></i>
                    Laporan Gaji
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                        <hr class="my-3" style="border-color: rgba(255, 255, 255, 0.2);">
                            
                            <li class="nav-item">
                            <form method="POST" action="<?php echo e(route('logout')); ?>" style="display: inline;">
                                    <?php echo csrf_field(); ?>
                                <button type="submit" class="nav-link btn btn-link text-start w-100" style="border: none; background: none; color: rgba(255, 255, 255, 0.8);">
                                        <i class="bi bi-box-arrow-right"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="pt-3 pb-2 mb-3">
                    <?php echo $__env->yieldContent('content'); ?>
                    </div>
                </main>
        </div>
        
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
    </body>
    </html><?php /**PATH C:\laragon\www\Managemen\resources\views/layouts/app.blade.php ENDPATH**/ ?>