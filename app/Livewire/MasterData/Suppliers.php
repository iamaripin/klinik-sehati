<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Models\Supplier as Supplier;

class Suppliers extends Component
{
    public $suppliers = [];

    public $id;
    public $supplier_code;
    public $supplier_name;
    public $contact_name;
    public $phone;
    public $address;
    public $email;

    public $isEdit = false;

    protected $listeners = ['openCreateModal', 'openEditModal'];

    public function mount()
    {
        $this->loadSuppliers();
    }

    public function render()
    {
        return view('livewire.master-data.supplier-data', [
            'data' => $this->suppliers,
        ])->layout('layouts.app', [
            'title' => 'Data Supplier',
        ]);
    }

    private function loadSuppliers()
    {
        $this->suppliers = Supplier::latest()->get();
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

        $s = Supplier::findOrFail($id);

        $this->id            = $s->id;
        $this->supplier_code = $s->supplier_code;
        $this->supplier_name = $s->supplier_name;
        $this->contact_name  = $s->contact_name;
        $this->phone         = $s->phone;
        $this->address       = $s->address;
        $this->email         = $s->email;

        $this->dispatch('show-form');
    }

    public function save()
    {
        $rules = [
            'supplier_code' => 'required|unique:suppliers,supplier_code' . ($this->isEdit ? ',' . $this->id : ''),
            'supplier_name' => 'required',
            'email'         => 'nullable|email',
        ];

        $this->validate($rules);

        if ($this->isEdit) {
            Supplier::findOrFail($this->id)->update([
                'supplier_code' => $this->supplier_code,
                'supplier_name' => $this->supplier_name,
                'contact_name'  => $this->contact_name,
                'phone'         => $this->phone,
                'address'       => $this->address,
                'email'         => $this->email,
            ]);

            $this->dispatch('toastr:info', message: 'Supplier berhasil diperbarui!');
        } else {
            Supplier::create([
                'supplier_code' => $this->supplier_code,
                'supplier_name' => $this->supplier_name,
                'contact_name'  => $this->contact_name,
                'phone'         => $this->phone,
                'address'       => $this->address,
                'email'         => $this->email,
            ]);

            $this->dispatch('toastr:success', message: 'Supplier berhasil ditambahkan!');
        }

        $this->loadSuppliers();
        $this->resetForm();
        $this->dispatch('hide-form');
        $this->dispatch('refresh-table');
    }

    public function supplierConfirm($id)
    {
        $this->id = $id;
        $this->dispatch('confirm-delete-supplier');
    }

    public function deleteSupplier()
    {
        Supplier::findOrFail($this->id)->delete();

        $this->dispatch('toastr:error', message: 'Supplier berhasil dihapus!');
        $this->loadSuppliers();
        $this->dispatch('refresh-table');
    }

    private function resetForm()
    {
        $this->reset([
            'id',
            'supplier_code',
            'supplier_name',
            'contact_name',
            'phone',
            'address',
            'email',
        ]);

        $this->isEdit = false;
    }
}
