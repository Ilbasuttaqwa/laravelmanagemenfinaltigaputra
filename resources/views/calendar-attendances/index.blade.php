@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-alt"></i> Kalender Absensi Karyawan
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAttendanceModal">
                            <i class="fas fa-plus"></i> Tambah Absensi
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route(auth()->user()->isAdmin() ? 'admin.calendar-attendances.index' : 'manager.calendar-attendances.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="tahun">Tahun:</label>
                                <input type="number" 
                                       name="tahun" 
                                       id="tahun" 
                                       class="form-control" 
                                       value="{{ $tahun }}" 
                                       min="2020" 
                                       max="2030"
                                       placeholder="Masukkan tahun">
                                <small class="text-muted">Range: 2020-2030</small>
                            </div>
                            <div class="col-md-3">
                                <label for="bulan">Bulan:</label>
                                <select name="bulan" id="bulan" class="form-control">
                                    @foreach($availableMonths as $monthNum => $monthName)
                                        <option value="{{ $monthNum }}" {{ $bulan == $monthNum ? 'selected' : '' }}>
                                            {{ $monthName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tipe">Tipe Karyawan:</label>
                                <select name="tipe" id="tipe" class="form-control">
                                    <option value="all" {{ $tipe == 'all' ? 'selected' : '' }}>Semua</option>
                                    <option value="karyawan" {{ $tipe == 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                    <option value="mandor" {{ $tipe == 'mandor' ? 'selected' : '' }}>Mandor</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-info btn-block">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Real-time Calendar -->
                    <div class="calendar-container">
                        <div class="calendar-header">
                            <h4 class="text-center mb-3">
                                <i class="fas fa-calendar-alt me-2"></i>
                                {{ $availableMonths[$bulan] }} {{ $tahun }}
                            </h4>
                            <div class="text-center">
                                <div class="real-time-info">
                                    <i class="fas fa-clock me-1"></i>
                                    <span id="current-time">Loading...</span>
                                    <small class="ms-2">(Waktu Jakarta)</small>
                                </div>
                            </div>
                        </div>
                        
                        @if($attendances->count() > 0)
                            <div class="table-responsive calendar-attendance-container">
                                <table class="table table-bordered table-striped calendar-attendance-table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th rowspan="2" class="text-center align-middle employee-column">No</th>
                                            <th rowspan="2" class="text-center align-middle employee-column">Nama Karyawan</th>
                                            <th rowspan="2" class="text-center align-middle employee-column">Tipe</th>
                                            <th colspan="{{ Carbon\Carbon::create($tahun, $bulan)->daysInMonth }}" class="text-center calendar-header">
                                                {{ $availableMonths[$bulan] }} {{ $tahun }}
                                            </th>
                                            <th rowspan="2" class="text-center align-middle employee-column">Total</th>
                                        </tr>
                                        <tr>
                                            @for($day = 1; $day <= Carbon\Carbon::create($tahun, $bulan)->daysInMonth; $day++)
                                                @php
                                                    $date = Carbon\Carbon::create($tahun, $bulan, $day);
                                                    $dayName = $date->format('D'); // Mon, Tue, Wed, etc.
                                                    $dayNumber = $day;
                                                    $isToday = $date->isToday();
                                                    $isWeekend = $date->isWeekend();
                                                @endphp
                                                <th class="text-center calendar-day-header {{ $isToday ? 'today' : '' }} {{ $isWeekend ? 'weekend' : '' }}">
                                                    <div class="day-number">{{ $dayNumber }}</div>
                                                    <div class="day-name">{{ $dayName }}</div>
                                                </th>
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($attendances as $index => $attendance)
                                            @php
                                                $totalFull = 0;
                                                $totalHalf = 0;
                                            @endphp
                                            <tr>
                                                <td class="text-center employee-column">{{ $index + 1 }}</td>
                                                <td class="employee-column">
                                                    <strong>{{ $attendance->nama_karyawan }}</strong>
                                                </td>
                                                <td class="text-center employee-column">
                                                    <span class="badge badge-{{ $attendance->tipe_karyawan == 'mandor' ? 'success' : 'primary' }}">
                                                        {{ ucfirst($attendance->tipe_karyawan) }}
                                                    </span>
                                                </td>
                                                @for($day = 1; $day <= Carbon\Carbon::create($tahun, $bulan)->daysInMonth; $day++)
                                                    @php
                                                        $status = $attendance->attendance_data[$day] ?? 'absen';
                                                        $date = Carbon\Carbon::create($tahun, $bulan, $day);
                                                        $isToday = $date->isToday();
                                                        $isWeekend = $date->isWeekend();
                                                        $isPast = $date->isPast();
                                                        
                                                        if ($status == 'full') $totalFull++;
                                                        if ($status == 'setengah_hari') $totalHalf++;
                                                    @endphp
                                                    <td class="text-center calendar-day-cell {{ $isToday ? 'today' : '' }} {{ $isWeekend ? 'weekend' : '' }} {{ $isPast ? 'past' : '' }}">
                                                        <select class="form-control form-control-sm attendance-select" 
                                                                data-attendance-id="{{ $attendance->id }}" 
                                                                data-day="{{ $day }}"
                                                                data-employee-type="{{ $attendance->tipe_karyawan }}"
                                                                data-employee-id="{{ $attendance->employee_id }}"
                                                                style="min-width: 60px; font-size: 11px;">
                                                            <option value="absen" {{ $status == 'absen' ? 'selected' : '' }}>A</option>
                                                            <option value="setengah_hari" {{ $status == 'setengah_hari' ? 'selected' : '' }}>S</option>
                                                            <option value="full" {{ $status == 'full' ? 'selected' : '' }}>F</option>
                                                        </select>
                                                    </td>
                                                @endfor
                                                <td class="text-center employee-column">
                                                    <div class="attendance-summary">
                                                        <small class="text-success">F: {{ $totalFull }}</small><br>
                                                        <small class="text-warning">S: {{ $totalHalf }}</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle"></i>
                                Tidak ada data karyawan untuk ditampilkan. Silakan tambah karyawan terlebih dahulu.
                            </div>
                        @endif
                        
                        <!-- Legend -->
                        <div class="calendar-legend">
                            <strong>Keterangan:</strong>
                            <div class="legend-item">
                                <span class="legend-color" style="background: #28a745;"></span>
                                <span>Hari Ini</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: #6c757d;"></span>
                                <span>Weekend</span>
                            </div>
                            <div class="legend-item">
                                <span class="legend-color" style="background: #e9ecef;"></span>
                                <span>Hari Lalu</span>
                            </div>
                            <div class="legend-item">
                                <span><strong>F:</strong> Full Day</span>
                            </div>
                            <div class="legend-item">
                                <span><strong>S:</strong> Setengah Hari</span>
                            </div>
                            <div class="legend-item">
                                <span><strong>A:</strong> Absen</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Attendance Modal -->
<div class="modal fade" id="addAttendanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('manager.calendar-attendances.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="karyawan_tipe">Tipe Karyawan:</label>
                        <select name="karyawan_tipe" id="karyawan_tipe" class="form-control" required>
                            <option value="">Pilih Tipe Karyawan</option>
                            <option value="karyawan">Karyawan</option>
                            <option value="mandor">Mandor</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="karyawan_id">Karyawan:</label>
                        <select name="karyawan_id" id="karyawan_id" class="form-control" required>
                            <option value="">Pilih Karyawan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tahun">Tahun:</label>
                        <input type="number" 
                               name="tahun" 
                               id="modal_tahun" 
                               class="form-control" 
                               value="{{ date('Y') }}" 
                               min="2020" 
                               max="2030"
                               placeholder="Masukkan tahun"
                               required>
                        <small class="text-muted">Range: 2020-2030</small>
                    </div>
                    <div class="form-group">
                        <label for="bulan">Bulan:</label>
                        <select name="bulan" id="bulan" class="form-control" required>
                            @foreach($availableMonths as $monthNum => $monthName)
                                <option value="{{ $monthNum }}" {{ $monthNum == date('n') ? 'selected' : '' }}>
                                    {{ $monthName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.calendar-container {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.calendar-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    text-align: center;
}

.real-time-info {
    background: rgba(255, 255, 255, 0.1);
    padding: 8px 16px;
    border-radius: 20px;
    display: inline-block;
    margin-top: 10px;
    font-size: 14px;
    font-weight: 500;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.real-time-info i {
    color: #ffd700;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.calendar-attendance-table {
    margin: 0;
    font-size: 12px;
}

.calendar-attendance-table thead th {
    background: #343a40;
    color: white;
    border: 1px solid #dee2e6;
    padding: 8px 4px;
    font-weight: 600;
}

.employee-column {
    background: #f8f9fa;
    font-weight: 600;
    min-width: 120px;
    position: sticky;
    left: 0;
    z-index: 10;
}

.calendar-day-header {
    background: #495057;
    color: white;
    padding: 8px 4px;
    min-width: 60px;
    position: relative;
}

.calendar-day-header.today {
    background: #28a745 !important;
    color: white;
}

.calendar-day-header.weekend {
    background: #6c757d;
}

.day-number {
    font-weight: bold;
    font-size: 14px;
}

.day-name {
    font-size: 10px;
    opacity: 0.8;
}

.calendar-day-cell {
    padding: 4px;
    border: 1px solid #dee2e6;
    background: white;
    transition: all 0.3s ease;
}

.calendar-day-cell.today {
    background: #d4edda;
    border-color: #28a745;
}

.calendar-day-cell.weekend {
    background: #f8f9fa;
}

.calendar-day-cell.past {
    background: #e9ecef;
    opacity: 0.7;
}

.attendance-select {
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 2px 4px;
    font-size: 11px;
    text-align: center;
}

.attendance-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.attendance-summary {
    font-size: 10px;
    line-height: 1.2;
}

.calendar-attendance-container {
    max-height: 600px;
    overflow-y: auto;
    overflow-x: auto;
}

/* Custom scrollbar */
.calendar-attendance-container::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.calendar-attendance-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.calendar-attendance-container::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: 4px;
}

.calendar-attendance-container::-webkit-scrollbar-thumb:hover {
    background: #5a6fd8;
}

/* Responsive */
@media (max-width: 768px) {
    .calendar-attendance-table {
        font-size: 10px;
    }
    
    .employee-column {
        min-width: 80px;
    }
    
    .calendar-day-header {
        min-width: 40px;
        padding: 4px 2px;
    }
    
    .attendance-select {
        min-width: 40px;
        font-size: 9px;
    }
}

/* Legend */
.calendar-legend {
    margin-top: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
    font-size: 12px;
}

.calendar-legend .legend-item {
    display: inline-block;
    margin-right: 15px;
}

.calendar-legend .legend-color {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 2px;
    margin-right: 5px;
    vertical-align: middle;
}

/* Modal Close Button Fix */
.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1;
    color: #000;
    text-shadow: 0 1px 0 #fff;
    opacity: 0.5;
    padding: 0.25rem 0.25rem;
    margin: -0.25rem -0.25rem -0.25rem auto;
}

.btn-close:hover {
    color: #000;
    text-decoration: none;
    opacity: 0.75;
}

.btn-close:focus {
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    opacity: 1;
}

.btn-close:disabled,
.btn-close.disabled {
    pointer-events: none;
    user-select: none;
    opacity: 0.25;
}

.btn-close::before {
    content: "×";
    font-size: 1.5rem;
    font-weight: bold;
    line-height: 1;
}
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Real-time clock for Jakarta timezone
    function updateJakartaTime() {
        const now = new Date();
        const jakartaTime = new Date(now.toLocaleString("en-US", {timeZone: "Asia/Jakarta"}));
        
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            timeZone: 'Asia/Jakarta'
        };
        
        const timeString = jakartaTime.toLocaleDateString('id-ID', options);
        $('#current-time').text(timeString);
    }
    
    // Update time every second
    updateJakartaTime();
    setInterval(updateJakartaTime, 1000);
    
    // Auto-fill current year and month
    const currentDate = new Date();
    const currentYear = currentDate.getFullYear();
    const currentMonth = currentDate.getMonth() + 1;
    
    // Set default values if not set
    if (!$('#tahun').val()) {
        $('#tahun').val(currentYear);
    }
    if (!$('#bulan').val()) {
        $('#bulan').val(currentMonth);
    }
    // Handle karyawan dropdown change
    $('#karyawan_tipe').change(function() {
        var tipe = $(this).val();
        var karyawanSelect = $('#karyawan_id');
        
        karyawanSelect.empty().append('<option value="">Pilih Karyawan</option>');
        
        if (tipe === 'karyawan') {
            // Karyawan options will be populated via AJAX
            $.get('/manager/calendar-attendances/get-employees', {tipe: 'karyawan'}, function(data) {
                karyawanSelect.empty().append('<option value="">Pilih Karyawan</option>');
                $.each(data, function(index, employee) {
                    karyawanSelect.append('<option value="' + employee.id + '">' + employee.nama + '</option>');
                });
            });
        } else if (tipe === 'mandor') {
            // Mandor options will be populated via AJAX
            $.get('/manager/calendar-attendances/get-employees', {tipe: 'mandor'}, function(data) {
                karyawanSelect.empty().append('<option value="">Pilih Karyawan</option>');
                $.each(data, function(index, employee) {
                    karyawanSelect.append('<option value="' + employee.id + '">' + employee.nama + '</option>');
                });
            });
        }
    });

    // Handle attendance status change
    $(document).on('change', '.attendance-select', function() {
        var employeeType = $(this).data('employee-type');
        var employeeId = $(this).data('employee-id');
        var day = $(this).data('day');
        var status = $(this).val();
        var selectElement = $(this);
        var currentYear = {{ $tahun }};
        var currentMonth = {{ $bulan }};
        
        // Create date string
        var dateString = currentYear + '-' + String(currentMonth).padStart(2, '0') + '-' + String(day).padStart(2, '0');
        
        // Show loading state
        selectElement.prop('disabled', true);
        selectElement.css('opacity', '0.6');

        $.ajax({
            url: '/manager/absensis',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                employee_id: employeeId,
                tanggal: dateString,
                status: status
            },
            success: function(response) {
                showNotification('Status absensi berhasil diperbarui', 'success');
                updateAttendanceSummary();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation error - data already exists, try to update
                    $.ajax({
                        url: '/manager/absensis/update-existing',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            employee_id: employeeId,
                            tanggal: dateString,
                            status: status
                        },
                        success: function(response) {
                            showNotification('Status absensi berhasil diperbarui', 'success');
                            updateAttendanceSummary();
                        },
                        error: function() {
                            showNotification('Terjadi kesalahan saat menyimpan data', 'error');
                            selectElement.val(selectElement.data('original-value')); // Revert
                        }
                    });
                } else {
                    showNotification('Terjadi kesalahan saat menyimpan data', 'error');
                    selectElement.val(selectElement.data('original-value')); // Revert
                }
            },
            complete: function() {
                // Re-enable select
                selectElement.prop('disabled', false);
                selectElement.css('opacity', '1');
            }
        });
    });
    
    // Store original values for revert functionality
    $('.attendance-select').each(function() {
        $(this).data('original-value', $(this).val());
    });
    
    // Update attendance summary
    function updateAttendanceSummary() {
        $('tbody tr').each(function() {
            var row = $(this);
            var fullCount = 0;
            var halfCount = 0;
            
            row.find('.attendance-select').each(function() {
                var status = $(this).val();
                if (status === 'full') fullCount++;
                if (status === 'setengah_hari') halfCount++;
            });
            
            var summary = row.find('.attendance-summary');
            summary.html('<small class="text-success">F: ' + fullCount + '</small><br><small class="text-warning">S: ' + halfCount + '</small>');
        });
    }

    function showNotification(message, type) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var notification = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                          message +
                          '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">' +
                          '' +
                          '</button>' +
                          '</div>';
        
        $('.card-body').prepend(notification);
        
        // Auto remove after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 3000);
    }
});
</script>
@endpush
