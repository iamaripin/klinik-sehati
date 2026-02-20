<?php

namespace App\Livewire\Nurse;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\User as UserModel;
use App\Models\Doctor;
use App\Models\Nurse;
use App\Models\Admission;
use App\Models\GeneralQueue;

class Anamnesa extends Component
{
    public $users = [];

    public $id;
    public $patient_name;
    public $mr_code;
    public $visit_no;
    public $patient_dob;
    public $umur;
    public $complaint;
    
    public $nurse_id;
    public $bp_systolic;
    public $bp_diastolic;
    public $temperature;
    public $weight_kg;
    public $height_cm;
    public $bmi;
    public $anamnesa;
    public $recorded_by;

    public $isEdit = false;
    public $history = [];
    // protected $listeners = ['openCreateModal', 'openEditModal'];

    public $search = '';

    protected $queryString = [
        'search' => ['except' => '']
    ];

    public function render()
    {
        $registrations = Admission::with(['patient', 'doctor', 'generalQueue'])
            ->where('visit_date', now()->format('Y-m-d'))
            ->when($this->search, function ($q) {
                $q->where('mr_code', 'like', "%{$this->search}%")
                    ->orWhere('visit_no', 'like', "%{$this->search}%")
                    ->orWhereHas('patient', function ($p) {
                        $p->where('name', 'like', "%{$this->search}%");
                    });
            })
            ->latest()
            ->get();

        return view('livewire.nurse.anamnesa', [
            'doctors' => Doctor::orderBy('doctor_code')->get(),
            'registrations' => $registrations,
        ])->layout('layouts.app', [
            'title' => 'Antrian Pasien Rawat Jalan',
        ]);
    }
    
    public function openAnamnesaMdl($id)
    {
        $this->resetForm();
        $this->isEdit = false;

        $adm = Admission::with(['patient', 'doctor', 'generalQueue'])
            ->where('id', $id)
            ->first();

        $this->id        = $adm->id;
        $this->patient_name   = $adm->patient->patient_name;
        $this->mr_code       = $adm->mr_code;
        $this->visit_no      = $adm->visit_no;
        $this->patient_dob    = $adm->patient->patient_dob->format('d-m-Y');
        $this->umur          = $this->umurLengkap($adm->patient->patient_dob);
        $this->complaint      = $adm->complaint;

        // Load visit history for this MR (latest first)
        $admissions = Admission::with('doctor')
            ->where('mr_code', $adm->mr_code)
            ->orderBy('visit_date', 'desc')
            ->orderBy('visit_time', 'desc')
            ->get();

        $history = [];
        foreach ($admissions as $a) {
            $n = Nurse::where('mr_code', $a->mr_code)
                ->where('visit_no', $a->visit_no)
                ->orderBy('created_at', 'desc')
                ->first();

            $history[] = [
                'patient_name' => $a->patient->patient_name,
                'visit_date' => $a->visit_date ? $a->visit_date->format('d-m-Y') : null,
                'visit_time' => $a->visit_time ?? null,
                'doctor' => $a->doctor->doctor_prefix . ' '.$a->doctor->doctor_name . ' '. $a->doctor->doctor_suffix ?? ($a->doctor_code ?? '-'),
                'complaint' => $a->complaint ?? '-',
                'weight_kg' => $n->weight_kg ?? '-',
                'height_cm' => $n->height_cm ?? '-',
                'temperature' => $n->temperature ?? '-',
                'bp_systolic' => $n->bp_systolic ?? '-',
                'bp_diastolic' => $n->bp_diastolic ?? '-',
                'bmi' => $n->bmi ?? '-',
                'anamnesa' => $n->anamnesa ?? '-', 
                'medication' => '-',
                'visit_no' => $a->visit_no,
            ];
        }

        $this->history = $history;

        $nurse = Nurse::where('visit_no', $adm->visit_no)->first();
        if($nurse) {
            $this->isEdit = true;
            $this->nurse_id = $nurse->id;
            $this->temperature = $nurse->temperature;
            $this->bp_systolic = $nurse->bp_systolic;
            $this->bp_diastolic = $nurse->bp_diastolic;
            $this->weight_kg = $nurse->weight_kg;
            $this->height_cm = $nurse->height_cm;
            $this->bmi = $nurse->bmi;
            $this->anamnesa = $nurse->anamnesa;
        } 

        // $this->dispatch('show-form');
        $this->dispatch('open-cw', 'riwayatMdl');
        $this->dispatch('open-cw', 'anamnesaMdl');
    }

    public function callPatient()
    {
        GeneralQueue::where('mr_code', $this->mr_code)
            ->where('visit_no', $this->visit_no)
            ->update(['queue_status' => 'CALL']);

        $this->dispatch('toastr:success', message: 'Pasien dipanggil!');
    } 

    public function resetQueueStatus()
    {
        GeneralQueue::where('mr_code', $this->mr_code)
            ->where('visit_no', $this->visit_no)
            ->update(['queue_status' => 'WAITING']);

        $this->dispatch('toastr:success', message: 'Pasien diubah statusnya menjadi waiting!');
    }

    public function save()
    {
        $rules = [
            'temperature' => 'required|numeric',
            'bp_systolic' => 'required|numeric',
            'bp_diastolic' => 'required|numeric',
            'weight_kg' => 'required|numeric',
            'height_cm' => 'required|numeric',
        ];
 
        $this->validate($rules);
        Nurse::updateOrCreate(
            ['visit_no' => $this->visit_no],
            [
                'mr_code'      => $this->mr_code,
                'visit_no'     => $this->visit_no,
                'recorded_by' => auth()->user()->username ?? null,
                'created_at' => now(),
                'updated_at' => now(),

                'temperature'  => $this->temperature,
                'bp_systolic'     => $this->bp_systolic,
                'bp_diastolic'    => $this->bp_diastolic,
                'weight_kg'  => $this->weight_kg,
                'height_cm'    => $this->height_cm,
                'bmi'      => $this->bmi,
                'anamnesa'      => $this->anamnesa,
            ]
        );

        $this->dispatch('toastr:success', message: 'Data anamnesa berhasil disimpan!');
   
        $this->dispatch('hide-form');
        $this->dispatch('refresh-table');
    } 

    function umurLengkap($tanggalLahir)
    {
        $dob = Carbon::parse($tanggalLahir);
        $now = Carbon::now();

        return $dob->diff($now)->format('%y tahun %m bulan %d hari');
    }

    protected function resetForm()
    {
        $this->reset([
            'id',
            'nurse_id',
            'patient_name',
            'mr_code',
            'visit_no',
            'bp_systolic',
            'bp_diastolic',
            'temperature',
            'weight_kg',
            'height_cm',
            'bmi',
            'anamnesa',
        ]);

        $this->isEdit = false;
    }

    public function updated($property)
    {
        if (in_array($property, ['weight_kg', 'height_cm'])) {
            $this->calculateBmi();
        }
    }

    public function calculateBmi()
    {
        if ($this->weight_kg && $this->height_cm) {

            $heightMeter = $this->height_cm / 100;

            if ($heightMeter > 0) {
                $this->bmi = round(
                    $this->weight_kg / ($heightMeter * $heightMeter),
                    2
                );
            }
        } else {
            $this->bmi = null;
        }
    }
}
