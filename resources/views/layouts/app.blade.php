<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tiga Putra Management System')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .sidebar {
            min-height: 100vh;
            background: #007bff;
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
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .btn-primary {
            background: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background: #0056b3;
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
    
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <img src="{{ asset('resources/logo.jpg') }}" alt="Tiga Putra Management" class="img-fluid" style="max-width: 80px; height: auto;">
                        </div>
                        <h4 class="text-white">
                            Tiga Putra Management
                        </h4>
                    </div>
                    
                    <ul class="nav flex-column">
                            @if(auth()->user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.gudangs.*') ? 'active' : '' }}"
                                       href="{{ route('admin.gudangs.index') }}">
                                    <i class="bi bi-building"></i>
                                        Master Gudang
                                    </a>
                                </li>
                                {{-- Admin tidak bisa akses Master Mandor --}}
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.absensis.*') ? 'active' : '' }}"
                                       href="{{ route('admin.absensis.index') }}">
                                        <i class="bi bi-calendar-check"></i>
                                        Master Absensi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.pembibitans.*') ? 'active' : '' }}"
                                       href="{{ route('admin.pembibitans.index') }}">
                                        <i class="bi bi-seedling"></i>
                                        Master Pembibitan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.kandangs.*') ? 'active' : '' }}"
                                       href="{{ route('admin.kandangs.index') }}">
                                        <i class="bi bi-house"></i>
                                        Master Kandang
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.lokasis.*') ? 'active' : '' }}"
                                       href="{{ route('admin.lokasis.index') }}">
                                        <i class="bi bi-geo-alt"></i>
                                        Master Lokasi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}"
                                       href="{{ route('admin.employees.index') }}">
                                        <i class="bi bi-people"></i>
                                        Master Karyawan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.manage') ? 'active' : '' }}"
                                       href="{{ route('admin.manage') }}">
                                        <i class="bi bi-gear"></i>
                                        Manage
                                    </a>
                                </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.monthly-attendance-reports.*') ? 'active' : '' }}"
                                   href="{{ route('admin.monthly-attendance-reports.index') }}">
                                    <i class="bi bi-graph-up"></i>
                                    Laporan Absensi Bulanan
                                </a>
                            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.calendar-attendances.*') ? 'active' : '' }}"
                   href="{{ route('admin.calendar-attendances.index') }}">
                    <i class="bi bi-calendar-check"></i>
                    Kalender Absensi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.salary-reports.*') ? 'active' : '' }}"
                   href="{{ route('admin.salary-reports.index') }}">
                    <i class="bi bi-cash-stack"></i>
                    Laporan Gaji
                                    </a>
                                </li>
                            @elseif(auth()->user()->isManager())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manager.gudangs.*') ? 'active' : '' }}"
                                       href="{{ route('manager.gudangs.index') }}">
                                    <i class="bi bi-building"></i>
                                        Master Gudang
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manager.mandors.*') ? 'active' : '' }}"
                                       href="{{ route('manager.mandors.index') }}">
                                        <i class="bi bi-person-badge"></i>
                                        Master Mandor
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manager.absensis.*') ? 'active' : '' }}"
                                       href="{{ route('manager.absensis.index') }}">
                                        <i class="bi bi-calendar-check"></i>
                                        Master Absensi
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manager.pembibitans.*') ? 'active' : '' }}"
                                       href="{{ route('manager.pembibitans.index') }}">
                                        <i class="bi bi-seedling"></i>
                                        Master Pembibitan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manager.kandangs.*') ? 'active' : '' }}"
                                       href="{{ route('manager.kandangs.index') }}">
                                        <i class="bi bi-house"></i>
                                        Master Kandang
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('manager.lokasis.*') ? 'active' : '' }}"
                                       href="{{ route('manager.lokasis.index') }}">
                                        <i class="bi bi-geo-alt"></i>
                                        Master Lokasi
                                    </a>
                                </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('manager.monthly-attendance-reports.*') ? 'active' : '' }}"
                                   href="{{ route('manager.monthly-attendance-reports.index') }}">
                                    <i class="bi bi-graph-up"></i>
                                    Laporan Absensi Bulanan
                                </a>
                            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manager.calendar-attendances.*') ? 'active' : '' }}"
                   href="{{ route('manager.calendar-attendances.index') }}">
                    <i class="bi bi-calendar-check"></i>
                    Kalender Absensi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('manager.salary-reports.*') ? 'active' : '' }}"
                   href="{{ route('manager.salary-reports.index') }}">
                    <i class="bi bi-cash-stack"></i>
                    Laporan Gaji
                                    </a>
                                </li>
                            @endif
                            
                        <hr class="my-3" style="border-color: rgba(255, 255, 255, 0.2);">
                            
                            <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                                    @csrf
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
                    @yield('content')
                    </div>
                </main>
        </div>
        
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
    </body>
    </html>