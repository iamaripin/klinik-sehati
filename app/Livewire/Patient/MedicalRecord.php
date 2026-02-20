<?php

namespace App\Livewire\Patient;

use Livewire\Component;
use App\Models\Patient as PatientModel;
use App\Models\PatientRelation as PatientRelationModel;

class MedicalRecord extends Component
{

    public $searchQuery = '';
    public $hasSearched = false;
    public $searchResults = [];

    public function search()
    {
        $this->hasSearched = true;

        if ($this->searchQuery) {
            // Search patients
            $this->searchResults = PatientModel::where('patient_name', 'like', '%' . $this->searchQuery . '%')
                ->orWhere('mr_code', 'like', '%' . $this->searchQuery . '%')
                ->get();
        }
    }

    public function updatedSearchQuery($value)
    { 
        $this->hasSearched = false;
        $this->searchResults = [];  
    }

    public function openDetailPatient($mrCode)
    {
        $this->dispatch('open-patient-detail', ['mrCode' => $mrCode]);
    }
    
    public function render()
    { 
            return view('livewire.patient.medical-record', [
                'searchResults' => $this->searchResults, 
                'hasSearched' => $this->hasSearched,
            ])
            ->layout('layouts.app', [
                'title' => 'Rekam Medis Pasien',
            ]);
    }
} 
