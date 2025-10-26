<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SalaryReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'lokasi_id',
        'kandang_id',
        'pembibitan_id',
        'nama_karyawan',
        'tipe_karyawan',
        'gaji_pokok',
        'jml_hari_kerja',
        'total_gaji',
        'tanggal_mulai',
        'tanggal_selesai',
        'tahun',
        'bulan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'gaji_pokok' => 'decimal:2',
        'total_gaji' => 'decimal:2',
    ];

    // Relationships
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class);
    }

    public function pembibitan()
    {
        return $this->belongsTo(Pembibitan::class);
    }

    // Accessors
    public function getGajiPokokFormattedAttribute()
    {
        return 'Rp ' . number_format($this->gaji_pokok, 0, ',', '.');
    }

    public function getTotalGajiFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_gaji, 0, ',', '.');
    }

    public function getTipeKaryawanLabelAttribute()
    {
        return ucfirst($this->tipe_karyawan);
    }

    public function getTipeKaryawanBadgeAttribute()
    {
        $badgeClass = $this->tipe_karyawan == 'mandor' ? 'success' : 'primary';
        return "<span class='badge badge-{$badgeClass}'>{$this->tipe_karyawan_label}</span>";
    }

    public function getPeriodeAttribute()
    {
        return Carbon::create($this->tahun, $this->bulan)->format('F Y');
    }

    public function getTanggalRangeAttribute()
    {
        return $this->tanggal_mulai->format('d/m/Y') . ' - ' . $this->tanggal_selesai->format('d/m/Y');
    }

    // Scopes
    public function scopePeriode($query, $tahun, $bulan)
    {
        return $query->where('tahun', $tahun)->where('bulan', $bulan);
    }

    public function scopeTipeKaryawan($query, $tipe)
    {
        if ($tipe !== 'all') {
            // Handle different tipe values
            if ($tipe === 'gudang') {
                return $query->where('tipe_karyawan', 'like', '%gudang%');
            } elseif ($tipe === 'mandor') {
                return $query->where('tipe_karyawan', 'mandor');
            } elseif ($tipe === 'karyawan') {
                return $query->where('tipe_karyawan', 'karyawan');
            }
        }
        return $query;
    }

    public function scopeLokasi($query, $lokasiId)
    {
        if ($lokasiId) {
            return $query->where('lokasi_id', $lokasiId);
        }
        return $query;
    }

    public function scopeKandang($query, $kandangId)
    {
        if ($kandangId) {
            return $query->where('kandang_id', $kandangId);
        }
        return $query;
    }

    public function scopePembibitan($query, $pembibitanId)
    {
        if ($pembibitanId) {
            return $query->where('pembibitan_id', $pembibitanId);
        }
        return $query;
    }

    public function scopeTanggalRange($query, $tanggalMulai, $tanggalSelesai)
    {
        if ($tanggalMulai && $tanggalSelesai) {
            return $query->whereBetween('tanggal_mulai', [$tanggalMulai, $tanggalSelesai])
                        ->orWhereBetween('tanggal_selesai', [$tanggalMulai, $tanggalSelesai]);
        }
        return $query;
    }
}