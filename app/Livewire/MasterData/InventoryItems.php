<?php


namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Models\InventoryItems as InventoryItemsModel;
use App\Models\InventoryCategory;
use App\Models\InventoryBatch;

class InventoryItems extends Component
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

    // batch related props
    public $batch_id;
    public $item_id;
    public $batch_number;
    public $expired_date;
    public $purchase_price;
    public $sell_price;
    public $stock_qty;
    public $item_search = '';
    public $selected_item_label = '';
    public $filteredItems = [];

    protected $listeners = ['openCreateModal', 'openEditModal', 'openCreateBatchModal', 'openEditBatchModal', 'selectItem'];

    public function mount()
    {
        return $this->loadData();
    }

    public function render()
    {
        return view('livewire.master-data.inventory-items-data', [
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

    public function openCreateBatchModal()
    {
        $this->resetBatchForm();
        $this->resetValidation();
        $this->dispatch('show-batch-form');
        $this->dispatch('activate-batches-tab');
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
                'minimal_stock'=> $this->minimal_stock, 
                'is_active'    => $this->is_active,
                'updated_at'    => now(),
            ]);

            $this->dispatch('toastr:info', message: 'Data item berhasil diperbarui!');
        } else {

            InventoryItemsModel  ::create([
                'item_code'    => $this->item_code,
                'item_name'    => $this->item_name,
                'category'     => $this->category,
                'generic_name' => $this->generic_name,
                'brand_name'   => $this->brand_name,
                'dosage_form'  => $this->dosage_form,
                'strength'     => $this->strength,
                'unit'         => $this->unit,
                'minimal_stock'=> $this->minimal_stock,
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

    /* Batch CRUD */
    public function openEditBatchModal($id)
    {
        $this->resetBatchForm();
        $this->isEdit = true;

        $batch = InventoryBatch::findOrFail($id);
        $this->batch_id = $batch->id;
        $this->item_id = $batch->item_id;
        $this->batch_number = $batch->batch_number;
        $this->expired_date = $batch->expired_date?->format('Y-m-d');
        $this->purchase_price = $batch->purchase_price;
        $this->sell_price = $batch->sell_price;
        $this->stock_qty = $batch->stock_qty;

        $this->resetValidation();
        $this->dispatch('show-batch-form');
        $this->dispatch('activate-batches-tab');
    }

    public function selectItem($id)
    {
        $item = InventoryItemsModel::find($id);
        if (!$item) return;

        $this->item_id = $item->id;
        $this->selected_item_label = $item->item_code . ' - ' . $item->item_name;
        $this->item_search = $this->selected_item_label;
    }

    public function getFilteredItemsProperty()
    {
        if (empty($this->item_search)) {
            return collect();
        }

        return InventoryItemsModel::where('item_name', 'like', '%' . $this->item_search . '%')
            ->orWhere('item_code', 'like', '%' . $this->item_search . '%')
            ->limit(10)
            ->get();
    }

    public function saveBatch()
    {
        $rules = [
            'item_id' => 'required|exists:items,id',
            'batch_number' => 'required',
            'expired_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric',
            'sell_price' => 'nullable|numeric',
            'stock_qty' => 'required|integer',
        ];

        $this->validate($rules);

        if ($this->batch_id) {
            $batch = InventoryBatch::findOrFail($this->batch_id);
            $batch->update([
                'item_id' => $this->item_id,
                'batch_number' => $this->batch_number,
                'expired_date' => $this->expired_date,
                'purchase_price' => $this->purchase_price,
                'sell_price' => $this->sell_price,
                'stock_qty' => $this->stock_qty,
            ]);

            $this->dispatch('toastr:info', message: 'Batch berhasil diperbarui!');
        } else {
            InventoryBatch::create([
                'item_id' => $this->item_id,
                'batch_number' => $this->batch_number,
                'expired_date' => $this->expired_date,
                'purchase_price' => $this->purchase_price,
                'sell_price' => $this->sell_price,
                'stock_qty' => $this->stock_qty,
            ]);

            $this->dispatch('toastr:success', message: 'Batch berhasil ditambahkan!');
        }

        $this->loadData();
        $this->resetBatchForm();
        $this->dispatch('hide-batch-form');
        $this->dispatch('batchTable');
        $this->dispatch('activate-batches-tab');
    }

    public function deleteBatchConfirm($id)
    {
        $this->batch_id = $id;
        $this->dispatch('confirm-delete-batch');
    }

    public function deleteBatch()
    {
        $batch = InventoryBatch::findOrFail($this->batch_id);
        $batch->delete();

        $this->dispatch('toastr:info', message: 'Batch berhasil dihapus!');
        $this->loadData();
        $this->dispatch('batchTable');
        $this->dispatch('activate-batches-tab');
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

    protected function resetBatchForm()
    {
        $this->reset([
            'batch_id',
            'item_id',
            'batch_number',
            'expired_date',
            'purchase_price',
            'sell_price',
            'stock_qty',
            'item_search',
            'selected_item_label',
        ]);

        $this->isEdit = false;
        $this->resetValidation();
    }
}
