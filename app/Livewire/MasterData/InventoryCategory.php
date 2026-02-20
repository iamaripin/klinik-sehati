<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use App\Models\InventoryCategory as InventoryCategoryModels;

class InventoryCategory extends Component
{
    public $categories = [];

    public $id;
    public $category_code;
    public $category_name;
    public $description;
    public $is_active = true;

    public $isEdit = false;

    protected $listeners = ['openCreateModal', 'openEditModal'];

    public function mount()
    {
        $this->loadCategories();
    }

    public function render()
    {
        return view('livewire.master-data.inventory-category-data', [
            'data' => $this->categories,
        ])->layout('layouts.app', [
            'title' => 'Inventory Categories',
        ]);
    }

    private function loadCategories()
    {
        $this->categories = InventoryCategoryModels::latest()->get();
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

        $c = InventoryCategoryModels::findOrFail($id);

        $this->id            = $c->id;
        $this->category_code = $c->category_code;
        $this->category_name = $c->category_name;
        $this->description   = $c->description;
        $this->is_active     = $c->is_active;

        $this->dispatch('show-form');
    }

    public function save()
    {
        $rules = [
            'category_code' => 'required|unique:inventory_categories,category_code' . ($this->isEdit ? ',' . $this->id : ''),
            'category_name' => 'required',
        ];

        $this->validate($rules);

        if ($this->isEdit) {
            InventoryCategoryModels::findOrFail($this->id)->update([
                'category_code' => $this->category_code,
                'category_name' => $this->category_name,
                'description'   => $this->description,
                'is_active'     => $this->is_active,
            ]);

            $this->dispatch('toastr:info', message: 'Kategori berhasil diperbarui!');
        } else {
            InventoryCategoryModels::create([
                'category_code' => $this->category_code,
                'category_name' => $this->category_name,
                'description'   => $this->description,
                'is_active'     => $this->is_active,
            ]);

            $this->dispatch('toastr:success', message: 'Kategori berhasil ditambahkan!');
        }

        $this->loadCategories();
        $this->resetForm();
        $this->dispatch('hide-form');
        $this->dispatch('refresh-table');
    }

    public function categoryConfirm($id)
    {
        $this->id = $id;
        $this->dispatch('confirm-delete-category');
    }

    public function deleteCategory()
    {
        InventoryCategoryModels::findOrFail($this->id)->delete();

        $this->dispatch('toastr:error', message: 'Kategori berhasil dihapus!');
        $this->loadCategories();
        $this->dispatch('refresh-table');
    }

    private function resetForm()
    {
        $this->reset([
            'id',
            'category_code',
            'category_name',
            'description',
            'is_active',
        ]);

        $this->is_active = true;
        $this->isEdit = false;
    }
}
