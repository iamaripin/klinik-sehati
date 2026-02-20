<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Admission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 

class Registration extends Component
{
    public $searchQuery = '';
    public $searchResults = [];
    public $selectedPatient = null;

    // Registration form fields
    public $visitNo = '';
    public $visitDate = '';
    public $visitTime = '';
    public $poli = '';
    public $doctorCode = '';
    public $visitType = 'POLI'; // IGD or POLI
    public $paymentType = 'UMUM';
    public $diagnosis = '';
    public $reservationCode = '';

    // Insurance fields
    public $insurance_number_at_visit = '';
    public $insurance_company_at_visit = '';
    public $insurance_used = false;

    public $isEdit = false;

    protected $listeners = ['openCreateModal', 'openEditModal'];

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->isEdit = true;

        $user = Admission::findOrFail($id);
        // Populate form fields
        $this->visitNo = $user->visit_no;
        $this->visitDate = $user->visit_date->format('Y-m-d');
        $this->visitTime = $user->visit_time;
        $this->poli = $user->poli;
        $this->doctorCode = $user->doctor_code;
        $this->visitType = $user->visit_type;
        $this->paymentType = $user->payment_type;
        $this->diagnosis = $user->diagnosis;
        $this->reservationCode = $user->reservation_code;
        $this->insurance_number_at_visit = $user->insurance_number_at_visit;
        $this->insurance_company_at_visit = $user->insurance_company_at_visit;
        $this->insurance_used = $user->insurance_used;

        $this->dispatch('show-form');
    }

    public function search()
    {
        if (empty($this->searchQuery)) {
            $this->searchResults = [];
            return;
        }

        // Search patients by name, mr_code, or phone
        $this->searchResults = Patient::where('patient_name', 'like', '%' . $this->searchQuery . '%')
            ->orWhere('mr_code', 'like', '%' . $this->searchQuery . '%')
            ->orWhere('patient_phone', 'like', '%' . $this->searchQuery . '%')
            ->limit(10)
            ->get();
    }

    // Select a patient from search results
    public function selectPatient($mrCode)
    {
        $this->selectedPatient = Patient::findOrFail($mrCode);
        $this->generateVisitNo();
        $this->searchResults = [];
        $this->searchQuery = '';
        $this->visitDate = now()->format('Y-m-d');
        $this->visitTime = now()->format('H:i');
        $this->poli = '';
        $this->doctorCode = '';
        $this->visitType = 'POLI';
        $this->paymentType = 'UMUM';
        $this->diagnosis = '';
        $this->reservationCode = '';
        $this->insurance_number_at_visit = '';
        $this->insurance_company_at_visit = '';
        $this->insurance_used = false;
    }

    // Generate visit_no in format JLN-mr_code-XXX
    private function generateVisitNo()
    {
        if (!$this->selectedPatient) {
            return;
        }

        $mrCode = $this->selectedPatient->mr_code;
        
        // Get the last visit number for this patient
        $lastAdmission = DB::table('admissions')
            ->where('mr_code', $mrCode)
            ->orderByDesc('id')
            ->first();

        $nextSequence = 1;
        if ($lastAdmission && $lastAdmission->visit_no) {
            // Extract the last number from visit_no (e.g., "JLN-123-005" -> 5)
            $parts = explode('-', $lastAdmission->visit_no);
            if (count($parts) >= 3) {
                $lastNum = (int) end($parts);
                $nextSequence = $lastNum + 1;
            }
        }

        $this->visitNo = sprintf('JLN-%s-%03d', $mrCode, $nextSequence);
    }

    // Save registration
    public function save()
    {
        $rules = [
            'visitDate' => 'required|date',
            'visitTime' => 'required|date_format:H:i',
            'poli' => 'required|string',
            'doctorCode' => 'required|string',
            'visitType' => 'required|in:IGD,POLI',
            'paymentType' => 'required|in:UMUM,BPJS,ASURANSI',
        ];

        // If payment is BPJS or ASURANSI then insurance fields required
        if (in_array($this->paymentType, ['BPJS', 'ASURANSI'])) {
            $rules['insurance_number_at_visit'] = 'required|string';
            $rules['insurance_company_at_visit'] = 'required|string';
        }

        $this->validate($rules, [
            'visitDate.required' => 'Tanggal kunjungan wajib diisi.',
            'visitTime.required' => 'Waktu kunjungan wajib diisi.',
            'poli.required' => 'Poli wajib dipilih.',
            'doctorCode.required' => 'Kode dokter wajib diisi.',
            'visitType.required' => 'Jenis kunjungan wajib dipilih.',
            'paymentType.required' => 'Tipe pembayaran wajib dipilih.',
            'insurance_number_at_visit.required' => 'Nomor asuransi wajib diisi untuk BPJS/Asuransi.',
            'insurance_company_at_visit.required' => 'Nama perusahaan asuransi wajib diisi untuk BPJS/Asuransi.',
        ]);

        if (!$this->selectedPatient) {
            $this->dispatch('toastr:warning', message: 'Pilih pasien terlebih dahulu.');
            return;
        }

        try {
            DB::beginTransaction();

            $insuranceUsed = in_array($this->paymentType, ['BPJS', 'ASURANSI']);

            // Create admission record
            $admissionId = DB::table('admissions')->insertGetId([
                'mr_code' => $this->selectedPatient->mr_code,
                'visit_no' => $this->visitNo,
                'visit_date' => $this->visitDate,
                'visit_time' => $this->visitTime,
                'poli' => $this->poli,
                'doctor_code' => $this->doctorCode,
                'visit_type' => $this->visitType,
                'payment_type' => $this->paymentType,
                'diagnosis' => $this->diagnosis,
                'reservation_code' => $this->reservationCode,
                'status' => 'REGISTERED',
                'insurance_used' => $insuranceUsed,
                'insurance_number_at_visit' => $insuranceUsed ? $this->insurance_number_at_visit : null,
                'insurance_company_at_visit' => $insuranceUsed ? $this->insurance_company_at_visit : null,
                'created_by' => auth()->user()->id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create general_queue record
            DB::table('general_queue')->insert([
                'mr_code' => $this->selectedPatient->mr_code,
                'visit_no' => $this->visitNo,
                'visit_date' => $this->visitDate,
                'doctor_code' => $this->doctorCode,
                'queue_no' => $this->generateQueueNo(),
                'poli' => $this->poli,
                'queue_status' => 'WAITING',
                'created_by' => auth()->user()->id ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            $this->dispatch('toastr:success', message: 'Registrasi berhasil! Visit No: ' . $this->visitNo);
            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage(), ['exception' => $e]);
            $this->dispatch('toastr:error', message: 'Registrasi gagal: ' . $e->getMessage());
        }
    }

    // Generate queue number for the day (auto-increment per poli and visit_date)
    private function generateQueueNo()
    {
        $today = now()->format('Y-m-d');
        $lastQueue = DB::table('general_queue')
            ->where('visit_date', $today)
            ->where('poli', $this->poli)
            ->orderByDesc('queue_no')
            ->first();

        return ($lastQueue ? $lastQueue->queue_no : 0) + 1;
    }

    // Clear form
    private function resetForm()
    {
        $this->selectedPatient = null;
        $this->visitNo = '';
        $this->visitDate = '';
        $this->visitTime = '';
        $this->poli = '';
        $this->doctorCode = '';
        $this->visitType = 'POLI';
        $this->paymentType = 'UMUM';
        $this->diagnosis = '';
        $this->reservationCode = '';
        $this->insurance_number_at_visit = '';
        $this->insurance_company_at_visit = '';
        $this->insurance_used = false;
        $this->searchQuery = '';
        $this->searchResults = [];
    }

    // Deselect patient
    public function deselectPatient()
    {
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.registration.index')
            ->layout('layouts.app', [
                'title' => 'Registrasi Pasien',
            ]);
    }
}
