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
                    <form method="GET" action="{{ route('manager.calendar-attendances.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="tahun">Tahun:</label>
                                <select name="tahun" id="tahun" class="form-control">
                                    @foreach($availableYears as $year)
                                        <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
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
                                    <option value="gudang" {{ $tipe == 'gudang' ? 'selected' : '' }}>Gudang</option>
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

                    <!-- Calendar Table -->
                    @if($attendances->count() > 0)
                        <div class="table-responsive calendar-attendance-container">
                            <table class="table table-bordered table-striped calendar-attendance-table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th rowspan="2" class="text-center align-middle">No</th>
                                        <th rowspan="2" class="text-center align-middle">Nama Karyawan</th>
                                        <th rowspan="2" class="text-center align-middle">Tipe</th>
                                        <th colspan="{{ Carbon\Carbon::create($tahun, $bulan)->daysInMonth }}" class="text-center">
                                            {{ $availableMonths[$bulan] }} {{ $tahun }}
                                        </th>
                                        <th rowspan="2" class="text-center align-middle">Aksi</th>
                                    </tr>
                                    <tr>
                                        @for($day = 1; $day <= Carbon\Carbon::create($tahun, $bulan)->daysInMonth; $day++)
                                            <th class="text-center">{{ $day }}</th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendances as $index => $attendance)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $attendance->nama_karyawan }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-{{ $attendance->tipe_karyawan == 'gudang' ? 'primary' : 'success' }}">
                                                    {{ ucfirst($attendance->tipe_karyawan) }}
                                                </span>
                                            </td>
                                            @for($day = 1; $day <= Carbon\Carbon::create($tahun, $bulan)->daysInMonth; $day++)
                                                @php
                                                    $status = $attendance->attendance_data[$day] ?? 'absen';
                                                @endphp
                                                <td class="text-center">
                                                    <select class="form-control form-control-sm attendance-select" 
                                                            data-attendance-id="{{ $attendance->id }}" 
                                                            data-day="{{ $day }}"
                                                            data-employee-type="{{ $attendance->tipe_karyawan }}"
                                                            data-employee-id="{{ $attendance->tipe_karyawan == 'gudang' ? $attendance->gudang_id : $attendance->mandor_id }}"
                                                            style="min-width: 80px;">
                                                        <option value="absen" {{ $status == 'absen' ? 'selected' : '' }}>Absen</option>
                                                        <option value="setengah_hari" {{ $status == 'setengah_hari' ? 'selected' : '' }}>Setengah Hari</option>
                                                        <option value="full" {{ $status == 'full' ? 'selected' : '' }}>Full Day</option>
                                                    </select>
                                                </td>
                                            @endfor
                                            <td class="text-center">
                                                <span class="badge badge-info">Realtime</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i>
                            Tidak ada data absensi untuk periode yang dipilih.
                        </div>
                    @endif
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="karyawan_tipe">Tipe Karyawan:</label>
                        <select name="karyawan_tipe" id="karyawan_tipe" class="form-control" required>
                            <option value="">Pilih Tipe Karyawan</option>
                            <option value="gudang">Gudang</option>
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
                        <select name="tahun" id="tahun" class="form-control" required>
                            @for($year = date('Y') - 1; $year <= date('Y') + 1; $year++)
                                <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
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

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Handle karyawan dropdown change
    $('#karyawan_tipe').change(function() {
        var tipe = $(this).val();
        var karyawanSelect = $('#karyawan_id');
        
        karyawanSelect.empty().append('<option value="">Pilih Karyawan</option>');
        
        if (tipe === 'gudang') {
            // Gudang options will be populated via AJAX
            $.get('/manager/calendar-attendances/get-employees', {tipe: 'gudang'}, function(data) {
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

        $.ajax({
            url: '/manager/absensis',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                karyawan_id: employeeType + '_' + employeeId,
                tanggal: dateString,
                status: status
            },
            success: function(response) {
                showNotification('Status absensi berhasil diperbarui', 'success');
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    // Validation error - data already exists, try to update
                    $.ajax({
                        url: '/manager/absensis/update-existing',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            karyawan_id: employeeType + '_' + employeeId,
                            tanggal: dateString,
                            status: status
                        },
                        success: function(response) {
                            showNotification('Status absensi berhasil diperbarui', 'success');
                        },
                        error: function() {
                            showNotification('Terjadi kesalahan saat menyimpan data', 'error');
                        }
                    });
                } else {
                    showNotification('Terjadi kesalahan saat menyimpan data', 'error');
                }
            }
        });
    });

    function showNotification(message, type) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var notification = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                          message +
                          '<button type="button" class="close" data-dismiss="alert">' +
                          '<span>&times;</span>' +
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
