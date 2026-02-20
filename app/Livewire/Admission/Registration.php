<?php

namespace App\Livewire\Admission;

use Livewire\Component;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Admission;
use App\Models\GeneralQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// paginate
use Livewire\WithPagination;

class Registration extends Component
{
    public $searchQuery = '';
    public $searchResults = [];
    public $selectedPatient = null;

    public $id;
    public $visitNo = ''; 
    public $visitDate = '';
    public $visitTime = '';
    public $poli = '';
    public $doctorCode = 'obgusup';
    public $visitType = 'POLI'; // IGD or POLI
    public $paymentType = 'PRIBADI';
    public $diagnosis = '';
    public $complaint = '';
    public $reservationCode = '';
    // Insurance fields
    public $insurance_number_at_visit;
    public $insurance_company_at_visit;
    public $insurance_used = false;

    // Patient form
    public $selectedMrCode = null;
    public $editPatientId = null;
    public $patientName = '';
    public $mrCode = '';
    public $patientEmail = '';
    public $patientPhone = '';
    public $patientAddress = '';
    public $patientDob = '';
    public $patientSex = '';
    public $patientNik = '';
    public $patientReligion = '';
    public $patientJob = '';
    public $patientStatus = '';
    public $patientBlood = '';
    public $patientEmergencyContact = '';
    public $patientAlergy = '';
    public $patientSpecial = '';
    public $patientNotes = '';

    public $isEdit = false;
    protected $listeners = ['openEditModal', 'createNewPatient'];
    
    public function openEditModal($id)
    {
        $this->resetForm();
        $this->isEdit = true;

        $admission = Admission::findOrFail($id);
        $this->id        = $admission->id;
        $this->visitNo = $admission->visit_no;
        $this->visitDate = $admission->visit_date->format('Y-m-d');
        $this->visitTime = $admission->visit_time;
        $this->poli = $admission->poli;
        $this->doctorCode = $admission->doctor_code;
        $this->visitType = $admission->visit_type;
        $this->paymentType = $admission->payment_type;
        $this->diagnosis = $admission->diagnosis;
        $this->complaint = $admission->complaint;
        $this->reservationCode = $admission->reservation_code;
        $this->insurance_number_at_visit = $admission->insurance_number_at_visit;
        $this->insurance_company_at_visit = $admission->insurance_company_at_visit;
        
        $this->dispatch('show-form');
    }

    public function createNewPatient()
    {
        $this->resetPatientForm();
        $this->mrCode = $this->generateMrCode();
        $this->isEdit = false;
        $this->dispatch('show-patient-modal');
    }

    protected function resetPatientForm()
    {
        $this->editPatientId = null;
        $this->patientName = '';
        $this->mrCode = '';
        $this->patientEmail = '';
        $this->patientPhone = '';
        $this->patientAddress = '';
        $this->patientDob = '';
        $this->patientSex = '';
        $this->patientNik = '';
        $this->patientReligion = '';
        $this->patientJob = '';
        $this->patientStatus = '';
        $this->patientBlood = '';
        $this->patientEmergencyContact = '';
        $this->patientAlergy = '';
        $this->patientSpecial = '';
        $this->patientNotes = '';
        $this->selectedMrCode = null;

        $this->isEdit = false;
    }


    private function generateMrCode()
    {
        // generate random number 6 digits and ensure it's unique
        do {
            $mrCode = rand(100000, 999999);
        } while (Patient::where('mr_code', $mrCode)->exists());
        return $mrCode;
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
        $this->poli = 'POLI-OBGYN';
        $this->doctorCode = 'obgusup';
        $this->visitType = 'POLI';
        $this->paymentType = 'PRIBADI';
        $this->diagnosis = '';
        $this->complaint = '';
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

    public function savePatient()
    {
        $this->validate([
            'patientName' => 'required',
            'patientNik' => 'nullable',
            'patientEmail' => 'nullable|email',
        ], [
            'patientName.required' => 'Nama pasien wajib diisi.',
            'patientNik.digits' => 'NIK harus terdiri dari 16 digit.',
            'patientEmail.email' => 'Format email tidak valid.',
        ]);
  
        $mr = $this->mrCode ?: $this->generateMrCode();

            Patient::create([
                'patient_name' => $this->patientName,
                'mr_code' => $mr,
                'patient_nik' => $this->patientNik,
                'patient_email' => $this->patientEmail,
                'patient_phone' => $this->patientPhone,
                'patient_address' => $this->patientAddress,
                'patient_dob' => $this->patientDob,
                'patient_sex' => $this->patientSex,
                'patient_religion' => $this->patientReligion,
                'patient_job' => $this->patientJob,
                'patient_status' => $this->patientStatus,
                'patient_blood' => $this->patientBlood,
                'patient_emergency_contact' => $this->patientEmergencyContact,
                'patient_alergy' => $this->patientAlergy,
                'patient_special' => $this->patientSpecial,
                'patient_notes' => $this->patientNotes,
            ]);
 
            $this->dispatch('toastr:success', message: 'Pasien berhasil ditambahkan!');
 
            $this->selectedPatient = Patient::findOrFail($mr);
            $this->generateVisitNo();
            $this->visitDate = now()->format('Y-m-d');
            $this->visitTime = now()->format('H:i');
            $this->poli = 'POLI-UMUM';
            $this->doctorCode = '';
            $this->visitType = 'POLI';
            $this->paymentType = 'UMUM';
            $this->diagnosis = '';
            $this->complaint = '';
            $this->reservationCode = '';
            $this->insurance_number_at_visit = '';
            $this->insurance_company_at_visit = '';
            $this->insurance_used = false;
 
            $this->resetPatientForm();
            $this->dispatch('hide-patient-modal'); 
    }

    // Save registration
    public function save()
    { 
        if ($this->isEdit) {
            try {
                DB::beginTransaction();
                $admission = Admission::findOrFail($this->id);

                $updateData = [ 
                    'poli' => $this->poli,
                    'doctor_code' => $this->doctorCode,
                    'visit_type' => $this->visitType,
                    'payment_type' => $this->paymentType,
                    'diagnosis' => $this->diagnosis,
                    'complaint' => $this->complaint,
                    'reservation_code' => $this->reservationCode,
                    // 'insurance_number_at_visit' => in_array($this->paymentType, ['BPJS', 'ASURANSI']) ? $this->insurance_number_at_visit : null,
                    // 'insurance_company_at_visit' => in_array($this->paymentType, ['BPJS', 'ASURANSI']) ? $this->insurance_company_at_visit : null,
                    'updated_by' => auth()->user()->username ?? null,
                    'updated_at' => now(),
                ];
    
                $admission->update($updateData); 
                DB::commit();
                $this->dispatch('toastr:success', message: 'Ubah data berhasil! Visit No: ' . $this->visitNo);
                $this->resetForm();
                $this->dispatch('hide-form');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Registration failed: ' . $e->getMessage(), ['exception' => $e]);
                $this->dispatch('toastr:error', message: 'Ubah data gagal: ' . $e->getMessage());
            } 
        } else {
            $rules = [
                'visitDate' => 'required|date',
                'visitTime' => 'required|date_format:H:i',
                'poli' => 'required|in:POLI-UMUM,POLI-ANAK,POLI-GIGI,POLI-OBGYN',
                'doctorCode' => 'string',
                'visitType' => 'required|in:IGD,POLI',
                'paymentType' => 'required|in:PRIBADI,CLAIM',
                'complaint' => 'required|string',
            ];

            // if (in_array($this->paymentType, ['BPJS', 'ASURANSI'])) {
            //     $rules['insurance_number_at_visit'] = 'required|string';
            //     $rules['insurance_company_at_visit'] = 'required|string';
            // }

            $this->validate($rules, [
                'visitDate.required' => 'Tanggal kunjungan wajib diisi.',
                'visitTime.required' => 'Waktu kunjungan wajib diisi.',
                'poli.required' => 'Poli wajib dipilih.',
                'doctorCode.required' => 'Kode dokter wajib diisi.',
                'visitType.required' => 'Jenis kunjungan wajib dipilih.',
                'paymentType.required' => 'Tipe pembayaran wajib dipilih.',
                'complaint.required' => 'Keluhan wajib diisi.',
                'insurance_number_at_visit.required' => 'Nomor asuransi wajib diisi untuk BPJS/Asuransi.',
                'insurance_company_at_visit.required' => 'Nama perusahaan asuransi wajib diisi untuk BPJS/Asuransi.',
            ]);

            if (!$this->selectedPatient) {
                $this->dispatch('toastr:warning', message: 'Pilih pasien terlebih dahulu.');
                return;
            }

            $activeStatus = ['REGISTERED', 'IN_PROGRESS'];

            if (DB::table('admissions')
                ->where('mr_code', trim($this->selectedPatient->mr_code))
                ->whereIn('status', $activeStatus)
                ->exists()
            ) {
                $this->dispatch('toastr:error', message: 'Pasien masih memiliki kunjungan aktif.');
                DB::rollBack();
                return;
            }else{
                try {
                    DB::beginTransaction();
                    $admission = DB::table('admissions')->insertGetId([
                        'mr_code' => $this->selectedPatient->mr_code,
                        'visit_no' => $this->visitNo,
                        'visit_date' => $this->visitDate,
                        'visit_time' => $this->visitTime,
                        'poli' => $this->poli,
                        'doctor_code' => $this->doctorCode,
                        'visit_type' => $this->visitType,
                        'payment_type' => $this->paymentType,
                        'diagnosis' => $this->diagnosis,
                        'complaint' => $this->complaint,
                        'reservation_code' => $this->reservationCode,
                        'status' => 'REGISTERED',
                        'insurance_used' => in_array($this->paymentType, ['BPJS', 'ASURANSI']) ? true : false,
                        'insurance_number_at_visit' => in_array($this->paymentType, ['BPJS', 'ASURANSI']) ? $this->insurance_number_at_visit : null,
                        'insurance_company_at_visit' => in_array($this->paymentType, ['BPJS', 'ASURANSI']) ? $this->insurance_company_at_visit : null,
                        'created_by' => auth()->user()->username ?? null,
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
                        'queue_prefix' => $this->generateQueuePrefix($this->paymentType),
                        'poli' => $this->poli,
                        'queue_status' => 'WAITING',
                        'created_by' => auth()->user()->username ?? null,
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
 
        }
 
    }

    public function deleteConfirm($id)
    {
        $this->id = $id;
        $this->dispatch('confirm-delete-admission');
    }

    public function deleteAdmission()
    {
        Admission::findOrFail($this->id)->delete();
        $this->dispatch('toastr:error', message: 'Admission berhasil dihapus!');
        $this->dispatch('refresh-table');
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

    public static function generateQueuePrefix($paymentType)
    {
        // Tentukan prefix
        $today = now()->format('Y-m-d');
        $prefix = match ($paymentType) {
            'PRIBADI' => 'P',
            'CLAIM'   => 'C',
            default   => throw new \Exception('Payment type tidak valid')
        };

        // Ambil nomor terakhir berdasarkan payment_type
        $lastQueue = GeneralQueue::where('visit_date', $today)
            ->where('queue_prefix', 'like', $prefix . '-%')
            ->orderByDesc('queue_no')
            ->first();

        if ($lastQueue) {
            $lastNumber = (int) substr($lastQueue->queue_prefix, 2);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
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
        $this->paymentType = '';
        $this->diagnosis = '';
        $this->complaint = '';
        $this->reservationCode = '';
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
        return view('livewire.admission.registration',
            [
                'doctor' => Doctor::orderBy('doctor_code')->where('doctor_code', 'obgusup')->first(),
                'registrations' => Admission::with(['patient', 'doctor','generalQueue'])
                                    ->where('status', 'REGISTERED')
                                    ->where('visit_date', now()->format('Y-m-d'))
                                    ->latest()
                                    ->get(),
            ])
            ->layout('layouts.app', [
                'title' => 'Registrasi Pasien',
            ]);
    } 
    
}
