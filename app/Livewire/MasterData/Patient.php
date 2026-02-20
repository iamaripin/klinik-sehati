<?php

namespace App\Livewire\MasterData;
  

use Livewire\Component;
use App\Models\Patient as PatientModel;
use App\Models\PatientRelation as PatientRelationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Patient extends Component
{
    public $isEdit = false;

    public $searchQuery = '';
    public $hasSearched = false;
    public $selectedMrCode = null;
    public $searchResults = [];
    public $relationResults = [];

    // Patient edit form
    public $editPatientId = null;
    public $patientNik = '';
    public $patientCardNumber = '';
    public $patientName = '';
    public $mrCode = '';
    public $patientContact = '';
    public $patientAddress = '';
    public $patientDob = ''; 
    public $patientSex = ''; 
    public $patientReligion = '';
    public $patientJob = '';
    public $patientStatus = '';
    public $patientBlood = '';
    public $patientRelationName;
    public $patientEmergencyContact = '';
    public $patientAlergy = '';
    public $patientNotes = ''; 

    // Relation edit/create form
    public $editRelationId = null;
    public $relationName = '';
    public $relationStatus = '';
    public $relationCode = '';
    public $relationNik = '';
    public $relationPhone = '';
    public $relationAddress = '';
    public $relationSex = '';
    public $relationDob = '';
    public $relationBlood = '';
    public $relationMrCode = '';

    protected $listeners = ['openCreateModal', 'openEditModal'];

    private function generateMrCode()
    { 
       // generate random number 6 digits and ensure it's unique
       do {
           $mrCode = rand(100000, 999999);
       } while (PatientModel::where('mr_code', $mrCode)->exists());
         return $mrCode;

    }

    public function openCreateModal()
    {
        $this->resetPatientForm();
        $this->mrCode = $this->generateMrCode();
        $this->isEdit = false;
        $this->dispatch('show-patient-modal');
    }
 
    public function search()
    {
        $this->hasSearched = true;

        if ($this->searchQuery) { 
            $this->searchResults = PatientModel::where('patient_name', 'like', '%' . $this->searchQuery . '%')
                ->orWhere('mr_code', 'like', '%' . $this->searchQuery . '%')
                ->orWhere('patient_dob', 'like', '%' . $this->searchQuery . '%')
                ->get();

            // Search patient relations using mr_code and relation_code
            $mrCodes = $this->searchResults->pluck('mr_code')->toArray();
            if (count($mrCodes) > 0) {
                // If search returns exactly one patient, load its relations and select it.
                if (count($mrCodes) === 1) {
                    $code = $mrCodes[0];
                    $this->selectedMrCode = $code;
                    $this->relationResults = PatientRelationModel::where('relation_code', $code)->get();
                } else {
                    // Multiple patients found: only show relations for the selected patient (if any).
                    if ($this->selectedMrCode && in_array($this->selectedMrCode, $mrCodes)) {
                        $this->relationResults = PatientRelationModel::where('relation_code', $this->selectedMrCode)->get();
                    } else {
                        $this->relationResults = [];
                    }
                }
            } else {
                $this->relationResults = [];
            }
        } else {
            $this->searchResults = [];
            $this->relationResults = [];
        }
    }

    public function updatedSearchQuery($value)
    { 
        $this->hasSearched = false;
        $this->searchResults = [];
        $this->relationResults = [];
        $this->selectedMrCode = null;
    }

    // Select a patient from multi-row results
    public function selectPatient($mrCode)
    {
        $this->selectedMrCode = $mrCode; 
        if ($mrCode) {
            $this->relationResults = PatientRelationModel::where('relation_code', $mrCode)->get();
        } else {
            $this->relationResults = [];
        }
    }

    // Edit the currently selected patient
    public function editSelected()
    {
        if (! $this->selectedMrCode) {
            $this->dispatch('toastr:warning', message: 'Pilih pasien terlebih dahulu');
            return;
        }

        $this->editPatient($this->selectedMrCode);
    }

    // Patient edit
    public function editPatient($id)
    {

        $this->resetPatientForm();
        $this->isEdit = true;

        $patient = PatientModel::findOrFail($id); 
        $this->editPatientId = $patient->mr_code;
        $this->patientNik = $patient->patient_nik;
        $this->patientCardNumber = $patient->patient_card_number;
        $this->patientName = $patient->patient_name;
        $this->mrCode = $patient->mr_code;
        $this->patientContact = $patient->patient_contact;
        $this->patientAddress = $patient->patient_address;
        $this->patientDob = $patient->patient_dob ? $patient->patient_dob->format('Y-m-d') : '';
        $this->patientSex = $patient->patient_sex;
        $this->patientReligion = $patient->patient_religion;
        $this->patientJob = $patient->patient_job;
        $this->patientStatus = $patient->patient_status;
        $this->patientBlood = $patient->patient_blood;
        $this->patientRelationName = $patient->patient_relation_name;
        $this->patientEmergencyContact = $patient->patient_emergency_contact;
        $this->patientAlergy = $patient->patient_alergy;
        $this->patientNotes = $patient->patient_notes;

        $this->dispatch('show-patient-modal');
    }

    // Patient save
    public function savePatient()
    {
        $this->validate([
            'patientName' => 'required',  
            'patientNik' => 'nullable',
        ], [
            'patientName.required' => 'Nama pasien wajib diisi.',  
            'patientNik.digits' => 'NIK harus terdiri dari 16 digit.',
        ]);

        // Keep track of MR code for post-create search/selection
        $createdMr = null;

        if ($this->isEdit) {
            try { 
                $lookupKey = $this->editPatientId ?: $this->mrCode;
                $patient = PatientModel::findOrFail($lookupKey);

                $patient->update([
                    'patient_name' => $this->patientName,
                    'mr_code' => $this->mrCode,
                    'patient_nik' => $this->patientNik,
                    'patient_card_number' => $this->patientCardNumber,
                    'patient_contact' => $this->patientContact,
                    'patient_address' => $this->patientAddress,
                    'patient_dob' => $this->patientDob,
                    'patient_sex' => $this->patientSex,
                    'patient_religion' => $this->patientReligion,
                    'patient_job' => $this->patientJob,
                    'patient_status' => $this->patientStatus,
                    'patient_blood' => $this->patientBlood,
                    'patient_relation_name' => $this->patientRelationName,
                    'patient_emergency_contact' => $this->patientEmergencyContact,
                    'patient_alergy' => $this->patientAlergy,
                    'patient_notes' => $this->patientNotes,
                ]);

                $this->dispatch('toastr:info', message: 'Pasien berhasil diperbarui!'. $this->isEdit);
            } catch (\Exception $e) { 
                Log::error('Patient update failed: ' . $e->getMessage(), [
                    'mrCode' => $this->mrCode,
                    'editPatientId' => $this->editPatientId,
                    'exception' => $e,
                ]);

                $this->dispatch('toastr:error', message: 'Gagal memperbarui pasien: ' . $e->getMessage());
            }
        } else {
            // For create, prefer mrCode already set on the component (from openCreateModal)
            $mr = $this->mrCode ?: $this->generateMrCode();

            PatientModel::create([
                'patient_name' => $this->patientName,
                'mr_code' => $mr,
                'patient_nik' => $this->patientNik,
                'patient_card_number' => $this->patientCardNumber,
                'patient_contact' => $this->patientContact,
                'patient_address' => $this->patientAddress,
                'patient_dob' => $this->patientDob,
                'patient_sex' => $this->patientSex,
                'patient_religion' => $this->patientReligion,
                'patient_job' => $this->patientJob,
                'patient_status' => $this->patientStatus,
                'patient_blood' => $this->patientBlood,
                'patient_relation_name' => $this->patientRelationName,
                'patient_emergency_contact' => $this->patientEmergencyContact,
                'patient_alergy' => $this->patientAlergy,
                'patient_notes' => $this->patientNotes,
            ]);

            $createdMr = $mr;
            $this->dispatch('toastr:success', message: 'Pasien berhasil ditambahkan!');
        }

        // If we just created a patient, search for the created MR and select it
        if (!empty($createdMr)) {
            $this->searchQuery = $createdMr;
        }

        $this->resetPatientForm();
        $this->dispatch('hide-patient-modal');

        // run search to surface the newly created/updated record
        $this->search();

        if (!empty($createdMr)) {
            $this->selectedMrCode = $createdMr;
        }
    }

    // Relation edit
    public function editRelation($id)
    {

        $this->resetPatientForm();
        $this->isEdit = true;
        $relation = PatientRelationModel::findOrFail($id);
        $this->editRelationId = $relation->id;
        $this->relationName = $relation->relation_name;
        $this->relationStatus = $relation->relation_status;
        $this->relationCode = $relation->relation_code;
        $this->relationNik = $relation->relation_nik;
        $this->relationPhone = $relation->relation_phone;
        $this->relationAddress = $relation->relation_address;
        $this->relationSex = $relation->relation_sex;
        $this->relationDob = $relation->relation_dob ? $relation->relation_dob->format('Y-m-d') : '';
        $this->relationBlood = $relation->relation_blood;
        // relation_code stores the patient's MR code for this relation
        $this->relationMrCode = $relation->relation_code;

        $this->dispatch('show-relation-modal');
    }

    // Relation save
    public function saveRelation()
    {
        // If MR code was not explicitly filled, fall back to the currently selected patient MR code.
        if (empty($this->relationMrCode) && $this->selectedMrCode) {
            $this->relationMrCode = $this->selectedMrCode;
        }

        // Validate public properties (Livewire expects property names here)
        $this->validate([
            'relationName' => 'required', 
            'relationStatus' => 'required',
            'relationMrCode' => 'required|numeric|exists:patients,mr_code', 
            'relationNik' => 'required',
            'relationPhone' => 'required|numeric',
            'relationDob' => 'required|date',
            'relationSex' => 'required',
            'relationBlood' => 'required',
            'relationAddress' => 'required',
        ], [
            'relationName.required' => 'Nama relasi wajib diisi.',
            'relationStatus.required' => 'Nama relasi wajib diisi.',
            'relationMrCode.required' => 'MR Code pasien wajib diisi.',
            'relationMrCode.numeric' => 'MR Code harus berupa angka.',
            'relationMrCode.exists' => 'MR Code pasien tidak ditemukan.',
            'relationNik.required' => 'Nomor NIK wajib diisi.',
            'relationNik.numeric' => 'Nomor NIK harus berupa angka.',
            'relationNik.digits' => 'NIK relasi harus terdiri dari 16 digit angka.',
            'relationPhone.required' => 'Nomor HP wajib diisi.',
            'relationPhone.numeric' => 'Nomor telepon harus berupa angka.',
            'relationDob.required' => 'Tanggal lahir wajib diisi.',
            'relationDob.date' => 'Tanggal lahir tidak valid.',
            'relationSex.required' => 'Tanggal lahir wajib diisi.',
            'relationBlood.required' => 'Tanggal lahir wajib diisi.',
            'relationAddress.required' => 'Tanggal lahir wajib diisi.',
        ]);

        if ($this->editRelationId) {
            $relation = PatientRelationModel::findOrFail($this->editRelationId);
            $relation->update([
                'relation_name' => $this->relationName,
                'relation_status' => $this->relationStatus,
                'relation_code' => $this->relationCode ?: $this->relationMrCode,
                'relation_nik' => $this->relationNik,
                'relation_phone' => $this->relationPhone,
                'relation_address' => $this->relationAddress,
                'relation_sex' => $this->relationSex,
                'relation_dob' => $this->relationDob,
                'relation_blood' => $this->relationBlood,
            ]);
            $this->dispatch('toastr:info', message: 'Relasi berhasil diperbarui!');
        } else {
            PatientRelationModel::create([
                'relation_name' => $this->relationName,
                'relation_status' => $this->relationStatus,
                'relation_code' => $this->relationCode ?: $this->relationMrCode,
                'relation_nik' => $this->relationNik,
                'relation_phone' => $this->relationPhone,
                'relation_address' => $this->relationAddress,
                'relation_sex' => $this->relationSex,
                'relation_dob' => $this->relationDob,
                'relation_blood' => $this->relationBlood,
            ]);
            $this->dispatch('toastr:success', message: 'Relasi berhasil ditambahkan!');
        }

        $this->resetRelationForm();
        $this->dispatch('hide-relation-modal');
        $this->search(); // Refresh search results
    }

    // Relation delete
    public function deleteRelation($id)
    {
        PatientRelationModel::findOrFail($id)->delete();
        $this->dispatch('toastr:error', message: 'Relasi berhasil dihapus!');
        $this->search(); // Refresh search results
    }

    // Create new relation (modal open without editing)
    public function createRelation($mrCode)
    {
        $this->resetRelationForm();
        $this->editRelationId = null;
        // prefill MR code and relation_code from the selected patient (or from current selection)
        $code = $mrCode ?: $this->selectedMrCode;
        $this->relationMrCode = $code;
        $this->relationCode = $code;
        $this->dispatch('show-relation-modal');
    }

    protected function resetPatientForm()
    {
        $this->editPatientId = null;
        $this->patientNik = '';
        $this->patientCardNumber = '';
        $this->patientName = '';
        $this->mrCode = '';
        $this->patientContact = '';
        $this->patientAddress = '';
        $this->patientDob = '';
        $this->patientSex = '';
        $this->patientReligion = '';
        $this->patientJob = '';
        $this->patientStatus = '';
        $this->patientBlood = '';
        $this->patientRelationName = '';
        $this->patientEmergencyContact = '';
        $this->patientAlergy = '';
        $this->patientNotes = '';
        $this->selectedMrCode = null;

        $this->isEdit = false;
    }

    protected function resetRelationForm()
    {
        $this->editRelationId = null;
        $this->relationName = '';
        $this->relationStatus = '';
        $this->relationCode = '';
        $this->relationNik = '';
        $this->relationPhone = '';
        $this->relationAddress = '';
        $this->relationSex = '';
        $this->relationDob = '';
        $this->relationBlood = '';
        $this->relationMrCode = '';

        $this->isEdit = false;
    }

    public function render()
    {
        return view('livewire.master-data.patient-data', [
            'searchResults' => $this->searchResults,
            'relationResults' => $this->relationResults,
            'hasSearched' => $this->hasSearched,
        ])
            ->layout('layouts.app', [
                'title' => 'Data Pasien', 
            ]);
    }

   
}
