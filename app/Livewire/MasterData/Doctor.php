<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Models\Doctor as DoctorModel;

class Doctor extends Component
{
    public $doctors = [];

    public $id;
    public $doctor_code;
    public $doctor_nik;
    public $doctor_tittle;
    public $doctor_name;
    public $doctor_suffix;
    public $doctor_prefix;
    public $doctor_sex;
    public $doctor_dob;
    public $doctor_phone;
    public $doctor_address;
    public $medical_code;
    public $doctor_email;
    public $doctor_photo;
    public $is_active = true;
    public $specialist;
    public $sip_number;
    public $sip_expiry;

    public $isEdit = false;

    protected $listeners = ['openCreateModal', 'openEditModal'];

    public function mount()
    {
        return $this->loadDoctors();
    }

    public function render()
    {
        return view('livewire.master-data.doctor-data', [
            'data' => $this->doctors,
        ])->layout('layouts.app', [
            'title' => 'Data Dokter',
        ]);
    }

    private function loadDoctors()
    {
        $this->doctors = DoctorModel::latest()->get();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->dispatch('show-form');
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->isEdit = true;

        $doctor = DoctorModel::findOrFail($id);

        $this->id              = $doctor->id;
        $this->doctor_code     = $doctor->doctor_code;
        $this->doctor_nik      = $doctor->doctor_nik;
        $this->doctor_tittle   = $doctor->doctor_tittle;
        $this->doctor_name     = $doctor->doctor_name;
        $this->doctor_suffix   = $doctor->doctor_suffix;
        $this->doctor_prefix   = $doctor->doctor_prefix;
        $this->doctor_sex      = $doctor->doctor_sex;
        $this->doctor_dob      = optional($doctor->doctor_dob)->format('Y-m-d');
        $this->doctor_phone    = $doctor->doctor_phone;
        $this->doctor_address  = $doctor->doctor_address;
        $this->medical_code    = $doctor->medical_code;
        $this->doctor_email    = $doctor->doctor_email;
        $this->doctor_photo    = $doctor->doctor_photo;
        $this->is_active       = $doctor->is_active;
        $this->specialist      = $doctor->specialist;
        $this->sip_number      = $doctor->sip_number;
        $this->sip_expiry      = optional($doctor->sip_expiry)->format('Y-m-d');

        $this->dispatch('show-form');
    }

    public function save()
    {
        $rules = [
            'doctor_code'  => 'required|unique:doctors,doctor_code' . ($this->isEdit ? ',' . $this->id : ''),
            'doctor_name'  => 'required',
        ];

        if ($this->doctor_email) {
            $rules['doctor_email'] = 'email';
        }

        $this->validate($rules);

        if ($this->isEdit) {

            $doctor = DoctorModel::findOrFail($this->id);

            $doctor->update([
                'doctor_code'    => $this->doctor_code,
                'doctor_nik'     => $this->doctor_nik,
                'doctor_tittle'  => null,
                'doctor_name'    => $this->doctor_name,
                'doctor_suffix'  => $this->doctor_suffix,
                'doctor_prefix'  => $this->doctor_prefix,
                'doctor_sex'     => $this->doctor_sex,
                'doctor_dob'     => $this->doctor_dob,
                'doctor_phone'   => $this->doctor_phone,
                'doctor_address' => $this->doctor_address,
                'medical_code'   => null,
                'doctor_email'   => $this->doctor_email,
                'doctor_photo'   => null,
                'is_active'      => $this->is_active,
                'specialist'     => $this->specialist,
                'sip_number'     => $this->sip_number,
                'sip_expiry'     => $this->sip_expiry,
                'updated_by'     => auth()->user()->username,
            ]);

            $this->dispatch('toastr:info', message: 'Data dokter berhasil diperbarui!');
        } else {

            DoctorModel::create([
                'doctor_code'    => $this->doctor_code,
                'doctor_nik'     => $this->doctor_nik,
                'doctor_tittle'  => null,
                'doctor_name'    => $this->doctor_name,
                'doctor_suffix'  => $this->doctor_suffix,
                'doctor_prefix'  => $this->doctor_prefix,
                'doctor_sex'     => $this->doctor_sex,
                'doctor_dob'     => $this->doctor_dob,
                'doctor_phone'   => $this->doctor_phone,
                'doctor_address' => $this->doctor_address,
                'medical_code'   => $this->medical_code,
                'doctor_email'   => null,
                'doctor_photo'   => null,
                'is_active'      => $this->is_active,
                'specialist'     => $this->specialist,
                'sip_number'     => $this->sip_number,
                'sip_expiry'     => $this->sip_expiry,
                'created_by'     => auth()->user()->username,
                'created_at'     => now(),
            ]);

            $this->dispatch('toastr:success', message: 'Data dokter berhasil ditambahkan!');
        }

        $this->loadDoctors();
        $this->resetForm();
        $this->dispatch('hide-form');
        $this->dispatch('refresh-table');
    }

    public function doctorConfirm($id)
    {
        $this->id = $id;
        $this->dispatch('confirm-update-doctor');
    }

    public function updateDoctorStatus()
    {
        $doctor = DoctorModel::findOrFail($this->id);

        $doctor->update([
            'is_active' => !$doctor->is_active,
        ]);

        $this->loadDoctors();
        $this->dispatch('toastr:error', message: 'Status dokter berhasil diperbarui!');
        $this->dispatch('refresh-table');
    }

    protected function resetForm()
    {
        $this->reset([
            'id',
            'doctor_code',
            'doctor_nik',
            'doctor_tittle',
            'doctor_name',
            'doctor_suffix',
            'doctor_prefix',
            'doctor_sex',
            'doctor_dob',
            'doctor_phone',
            'doctor_address',
            'medical_code',
            'doctor_email',
            'doctor_photo',
            'is_active',
            'specialist',
            'sip_number',
            'sip_expiry',
        ]);

        $this->isEdit = false;
        $this->is_active = true;
    }
}
