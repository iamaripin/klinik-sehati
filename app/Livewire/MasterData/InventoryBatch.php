<?php


namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Models\InventoryItems as InventoryItemsModel;
use App\Models\InventoryCategory;

class InventoryBatch extends Component
{
    public $inventory = [];

    public $id;
    public $item_code;
    public $item_name;
    public $category; // (obat/vaksin/alkes)
    public $generic_name; // (paracetamol, dll)
    public $brand_name; // (bodrex, dll)
    public $dosage_form; // (tablet, sirup, injeksi)
    public $strength; // (500mg, 10ml, dll)
    public $unit; // (strip, vial, box)
    public $minimal_stock;

    public $is_active = true;
    public $isEdit = false;

    protected $listeners = ['openCreateModal', 'openEditModal'];

    public function mount()
    {
        return $this->loadData();
    }

    public function render()
    {
        return view('livewire.master-data.inventory-items-data', [
            // 'data' => $this->inventory,
            // ambil data inventory dan batchnya untuk ditampilkan di tabel
            'data' => InventoryItemsModel::with('batches')->latest()->get(),
            'categories' => InventoryCategory::latest()->get(),
        ])->layout('layouts.app', [
            'title' => 'Data Inventory',
        ]);
    }

    private function loadData()
    {
        $this->inventory = InventoryItemsModel::latest()->get();
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

        $inventory = InventoryItemsModel::findOrFail($id);
        $this->id = $inventory->id;
        $this->item_code = $inventory->item_code;
        $this->item_name = $inventory->item_name;
        $this->category = $inventory->category;
        $this->generic_name = $inventory->generic_name;
        $this->brand_name = $inventory->brand_name;
        $this->dosage_form = $inventory->dosage_form;
        $this->strength = $inventory->strength;
        $this->unit = $inventory->unit;
        $this->minimal_stock = $inventory->minimal_stock;
        $this->is_active = $inventory->is_active;

        $this->dispatch('show-form');
    }

    public function save()
    {
        $rules = [
            'item_code'  => 'required|unique:items,item_code' . ($this->isEdit ? ',' . $this->id : ''),
            'item_name'  => 'required',
            'category'  => 'required',
            'generic_name'  => 'required',
            'dosage_form'  => 'required',
            'strength'  => 'required',
            'unit'  => 'required',
            'minimal_stock'  => 'required',

        ];

        $this->validate($rules);

        if ($this->isEdit) {

            $inventory = InventoryItemsModel::findOrFail($this->id);

            $inventory->update([
                'item_name'    => $this->item_name,
                'category'     => $this->category,
                'generic_name' => $this->generic_name,
                'brand_name'   => $this->brand_name,
                'dosage_form'  => $this->dosage_form,
                'strength'     => $this->strength,
                'unit'         => $this->unit,
                'minimal_stock' => $this->minimal_stock,
                'is_active'    => $this->is_active,
                'updated_at'    => now(),
            ]);

            $this->dispatch('toastr:info', message: 'Data item berhasil diperbarui!');
        } else {

            InventoryItemsModel::create([
                'item_code'    => $this->item_code,
                'item_name'    => $this->item_name,
                'category'     => $this->category,
                'generic_name' => $this->generic_name,
                'brand_name'   => $this->brand_name,
                'dosage_form'  => $this->dosage_form,
                'strength'     => $this->strength,
                'unit'         => $this->unit,
                'minimal_stock' => $this->minimal_stock,
                'is_active'    => $this->is_active,
                'created_at'    => now(),
            ]);

            $this->dispatch('toastr:success', message: 'Data item berhasil ditambahkan!');
        }

        $this->loadData();
        $this->resetForm();
        $this->dispatch('hide-form');
        $this->dispatch('refresh-table');
    }

    public function deleteConfirm($id)
    {
        $this->id = $id;
        $this->dispatch('confirm-update-data');
    }

    public function updateinventorystatus()
    {
        $data = InventoryItemsModel::findOrFail($this->id);

        $data->update([
            'is_active' => !$data->is_active,
        ]);

        $this->loadData();
        $this->dispatch('toastr:info', message: 'Status item berhasil diperbarui!');
        $this->dispatch('refresh-table');
    }

    protected function resetForm()
    {
        $this->reset([
            'item_code',
            'item_name',
            'category',
            'generic_name',
            'brand_name',
            'dosage_form',
            'strength',
            'unit',
            'minimal_stock',

        ]);

        $this->isEdit = false;
        $this->is_active = true;
    }
}
