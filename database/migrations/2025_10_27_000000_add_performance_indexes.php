<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Check if an index exists on a table
     */
    private function indexExists($table, $index)
    {
        $indexes = \DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $idx) {
            if ($idx->Key_name === $index) {
                return true;
            }
        }
        return false;
    }

    /**
     * Run the migrations.
     * Add performance indexes for large datasets
     */
    public function up(): void
    {
        // Add indexes to absensis table for better performance
        if (Schema::hasTable('absensis')) {
            Schema::table('absensis', function (Blueprint $table) {
            // Check if indexes don't exist before adding them
            if (!$this->indexExists('absensis', 'idx_absensi_employee_date')) {
                $table->index(['employee_id', 'tanggal'], 'idx_absensi_employee_date');
            }
            
            if (!$this->indexExists('absensis', 'idx_absensi_tanggal')) {
                $table->index('tanggal', 'idx_absensi_tanggal');
            }
            
            if (!$this->indexExists('absensis', 'idx_absensi_status')) {
                $table->index('status', 'idx_absensi_status');
            }
            
            if (!$this->indexExists('absensis', 'idx_absensi_nama')) {
                $table->index('nama_karyawan', 'idx_absensi_nama');
            }
            
            if (!$this->indexExists('absensis', 'idx_absensi_pembibitan')) {
                $table->index('pembibitan_id', 'idx_absensi_pembibitan');
            }
            
            if (!$this->indexExists('absensis', 'idx_absensi_created')) {
                $table->index('created_at', 'idx_absensi_created');
            }
            });
        }
        
        // Add indexes to employees table
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
            if (!$this->indexExists('employees', 'idx_employees_jabatan')) {
                $table->index('jabatan', 'idx_employees_jabatan');
            }
            
            if (!$this->indexExists('employees', 'idx_employees_kandang')) {
                $table->index('kandang_id', 'idx_employees_kandang');
            }
            
            if (!$this->indexExists('employees', 'idx_employees_nama')) {
                $table->index('nama', 'idx_employees_nama');
            }
            });
        }
        
        // Add indexes to salary_reports table
        if (Schema::hasTable('salary_reports')) {
            Schema::table('salary_reports', function (Blueprint $table) {
            if (!$this->indexExists('salary_reports', 'idx_salary_employee_period')) {
                $table->index(['employee_id', 'tahun', 'bulan'], 'idx_salary_employee_period');
            }
            
            if (!$this->indexExists('salary_reports', 'idx_salary_period')) {
                $table->index(['tahun', 'bulan'], 'idx_salary_period');
            }
            
            if (!$this->indexExists('salary_reports', 'idx_salary_tipe')) {
                $table->index('tipe_karyawan', 'idx_salary_tipe');
            }
            
            if (!$this->indexExists('salary_reports', 'idx_salary_pembibitan')) {
                $table->index('pembibitan_id', 'idx_salary_pembibitan');
            }
            });
        }
        
        // Add indexes to pembibitans table
        if (Schema::hasTable('pembibitans')) {
            Schema::table('pembibitans', function (Blueprint $table) {
            if (!$this->indexExists('pembibitans', 'idx_pembibitan_kandang')) {
                $table->index('kandang_id', 'idx_pembibitan_kandang');
            }
            
            if (!$this->indexExists('pembibitans', 'idx_pembibitan_lokasi')) {
                $table->index('lokasi_id', 'idx_pembibitan_lokasi');
            }
            
            if (!$this->indexExists('pembibitans', 'idx_pembibitan_judul')) {
                $table->index('judul', 'idx_pembibitan_judul');
            }
            });
        }
        
        // Add indexes to kandangs table
        if (Schema::hasTable('kandangs')) {
            Schema::table('kandangs', function (Blueprint $table) {
            if (!$this->indexExists('kandangs', 'idx_kandang_lokasi')) {
                $table->index('lokasi_id', 'idx_kandang_lokasi');
            }
            
            if (!$this->indexExists('kandangs', 'idx_kandang_nama')) {
                $table->index('nama_kandang', 'idx_kandang_nama');
            }
            });
        }
        
        // Add indexes to gudangs table
        if (Schema::hasTable('gudangs')) {
            Schema::table('gudangs', function (Blueprint $table) {
            if (!$this->indexExists('gudangs', 'idx_gudang_nama')) {
                $table->index('nama', 'idx_gudang_nama');
            }
            });
        }
        
        // Add indexes to mandors table
        if (Schema::hasTable('mandors')) {
            Schema::table('mandors', function (Blueprint $table) {
            if (!$this->indexExists('mandors', 'idx_mandor_nama')) {
                $table->index('nama', 'idx_mandor_nama');
            }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from absensis table
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndex('idx_absensi_employee_date');
            $table->dropIndex('idx_absensi_tanggal');
            $table->dropIndex('idx_absensi_status');
            $table->dropIndex('idx_absensi_nama');
            $table->dropIndex('idx_absensi_pembibitan');
            $table->dropIndex('idx_absensi_created');
        });
        
        // Remove indexes from employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('idx_employees_jabatan');
            $table->dropIndex('idx_employees_kandang');
            $table->dropIndex('idx_employees_nama');
        });
        
        // Remove indexes from salary_reports table
        Schema::table('salary_reports', function (Blueprint $table) {
            $table->dropIndex('idx_salary_employee_period');
            $table->dropIndex('idx_salary_period');
            $table->dropIndex('idx_salary_tipe');
            $table->dropIndex('idx_salary_pembibitan');
        });
        
        // Remove indexes from pembibitans table
        Schema::table('pembibitans', function (Blueprint $table) {
            $table->dropIndex('idx_pembibitan_kandang');
            $table->dropIndex('idx_pembibitan_lokasi');
            $table->dropIndex('idx_pembibitan_judul');
        });
        
        // Remove indexes from kandangs table
        Schema::table('kandangs', function (Blueprint $table) {
            $table->dropIndex('idx_kandang_lokasi');
            $table->dropIndex('idx_kandang_nama');
        });
        
        // Remove indexes from gudangs table
        Schema::table('gudangs', function (Blueprint $table) {
            $table->dropIndex('idx_gudang_nama');
        });
        
        // Remove indexes from mandors table
        Schema::table('mandors', function (Blueprint $table) {
            $table->dropIndex('idx_mandor_nama');
        });
    }
};
