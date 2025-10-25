<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Services\AntiDuplicationService;

class StoreAbsensiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'employee_id' => 'required|string',
            'pembibitan_id' => 'nullable',
            'tanggal' => 'required|date',
            'status' => 'required|in:full,setengah_hari',
            'gaji_pokok_saat_itu' => 'required|numeric|min:0',
            'gaji_hari_itu' => 'required|numeric|min:0'
        ];
    }

    public function withValidator($validator)
    {
        // Temporarily disable AntiDuplicationService for testing
        // $validator->after(function ($validator) {
        //     // Check for duplicate attendance
        //     $antiDuplicationService = app(AntiDuplicationService::class);
            
        //     // Get employee name for duplicate check
        //     $employeeId = $this->input('employee_id');
        //     $tanggal = $this->input('tanggal');
            
        //     if ($employeeId && $tanggal) {
        //         $employeeName = $this->getEmployeeName($employeeId);
                
        //         if ($employeeName) {
        //             $duplicateCheck = $antiDuplicationService->checkDuplicateAbsensi($employeeName, $tanggal);
                    
        //             if ($duplicateCheck['is_duplicate']) {
        //                 $validator->errors()->add('tanggal', $duplicateCheck['message']);
        //             }
        //         }
        //     }
        // });
    }

    private function getEmployeeName($employeeId)
    {
        try {
            if (str_starts_with($employeeId, 'employee_')) {
                $id = str_replace('employee_', '', $employeeId);
                $employee = \App\Models\Employee::find($id);
                return $employee ? $employee->nama : null;
            } elseif (str_starts_with($employeeId, 'gudang_')) {
                $id = str_replace('gudang_', '', $employeeId);
                $gudang = \App\Models\Gudang::find($id);
                return $gudang ? $gudang->nama : null;
            } elseif (str_starts_with($employeeId, 'mandor_')) {
                $id = str_replace('mandor_', '', $employeeId);
                $mandor = \App\Models\Mandor::find($id);
                return $mandor ? $mandor->nama : null;
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'Karyawan harus dipilih.',
            'employee_id.regex' => 'Format ID karyawan tidak valid.',
            'tanggal.required' => 'Tanggal harus diisi.',
            'tanggal.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini.',
            'status.required' => 'Status harus dipilih.',
            'status.in' => 'Status harus Full Day atau ½ Hari.',
            'gaji_pokok_saat_itu.required' => 'Gaji pokok harus diisi.',
            'gaji_pokok_saat_itu.numeric' => 'Gaji pokok harus berupa angka.',
            'gaji_pokok_saat_itu.min' => 'Gaji pokok tidak boleh negatif.',
            'gaji_hari_itu.required' => 'Gaji perhari harus diisi.',
            'gaji_hari_itu.numeric' => 'Gaji perhari harus berupa angka.',
            'gaji_hari_itu.min' => 'Gaji perhari tidak boleh negatif.'
        ];
    }
}
