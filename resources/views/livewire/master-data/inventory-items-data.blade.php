<div>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body pc-component">

                <div class="d-flex gap-2 mb-2">
                    <button type="button" class="btn btn-primary" wire:click="openCreateModal">
                       <i class="ti ti-package"></i> Tambah Inventory Item
                    </button>

                    <button type="button" class="btn btn-primary" wire:click="openCreateBatchModal">
                       <i class="ti ti-stack"></i> Tambah Stock Batch
                    </button>
                </div>

                <ul class="nav nav-tabs" id="inventoryTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="items-tab" data-bs-toggle="tab" data-bs-target="#tab-items" type="button" role="tab" aria-controls="tab-items" aria-selected="true">Inventory Item</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="batches-tab" data-bs-toggle="tab" data-bs-target="#tab-batches" type="button" role="tab" aria-controls="tab-batches" aria-selected="false">Stock Batch</button>
                    </li>
                </ul>

                <div class="tab-content pt-3">
                    <div class="tab-pane fade show active" id="tab-items" role="tabpanel" aria-labelledby="items-tab">
                        <table id="itemTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Item</th>
                            <th>Kategori</th>
                            <th>Min Stock</th>
                            <th>Satuan</th>
                            <th>Status</th>
                            <th width="150px">#</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($data as $d)
                        <tr wire:key="row-{{ $d->id }}"
                             @class([
                                'table-danger' => $d->minimal_stock < $d->batches->sum('stock_qty'),
                            ])
                        >
                            <td>{{ $d->item_code }}</td>
                            <td>{{ $d->item_name }}</td>
                            <td>{{ $d->category }}</td>
                            {{-- <td>{{ $d->stock_qty }}</td> --}}
                            <td>{{ $d->minimal_stock }}</td>
                            <td>{{ $d->unit }}</td>

                            <td>
                                @if($d->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Non Active</span>
                                @endif
                            </td>

                            <td>
                                <button class="btn btn-info btn-sm" wire:click="openEditModal({{ $d->id }})">
                                    <i class="ti ti-edit-circle"></i>
                                </button>

                                <button class="btn {{ $d->is_active ? 'btn-danger' : 'btn-warning' }} btn-sm"
                                    wire:click="deleteConfirm({{ $d->id }})">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                        </table>
                    </div>

                    <div class="tab-pane fade" id="tab-batches" role="tabpanel" aria-labelledby="batches-tab">
                        <table id="batchTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nama Item</th>
                            <th>Nomor Batch</th>
                            <th>Tanggal Kadaluarsa</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th width="150px">#</th>
                        </tr>
                    </thead>
                
                            <tbody>
                                @foreach ($data as $d)
                                    @foreach ($d->batches as $b)
                                    <tr wire:key="batch-{{ $b->id }}">
                                        <td>{{ $b->item->item_name }}</td>
                                        <td>{{ $b->batch_number }}</td>
                                        <td>{{ $b->expired_date ? $b->expired_date->format('d-m-Y') : '-' }}</td>
                                        <td>{{ $b->purchase_price ? 'Rp '.number_format($b->purchase_price,0,',','.') : '-' }}</td>
                                        <td>{{ $b->sell_price ? 'Rp '.number_format($b->sell_price,0,',','.') : '-' }}</td>
                                        <td>{{ $b->stock_qty }}</td>
                                        <td>
                                            <button class="btn btn-info btn-sm" wire:click="openEditBatchModal({{ $b->id }})">
                                                <i class="ti ti-edit-circle"></i>
                                            </button>

                                            <button class="btn btn-danger btn-sm" wire:click="deleteBatchConfirm({{ $b->id }})">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

                {{-- MODAL --}}
                <div wire:ignore.self id="InventoryMdl" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    {{ $isEdit ? 'Edit Data' : 'Tambah Data' }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <form wire:submit.prevent="save">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-2">
                                                <label>Kode Item</label>
                                                <input type="text" id="item_code_input" class="form-control" wire:model="item_code" @if($this->isEdit) readonly @endif>
                                                @error('item_code') <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="mb-2">
                                                <label>Nama Item</label>
                                                <input type="text" class="form-control" wire:model="item_name">
                                                @error('item_name') <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-2">
                                                <label>Kategori</label>
                                                <select class="form-control" wire:model="category">
                                                    <option value="">Pilih Kategori</option>
                                                    @foreach($categories as $c)
                                                    <option value="{{ $c->category_code }}">{{ $c->category_name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('category') <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Generic Name</label>
                                            <input type="text" class="form-control" wire:model="generic_name" placeholder="Cth. Paracetamol">
                                            @error('generic_name') <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Brand Name</label>
                                            <input type="text" class="form-control" wire:model="brand_name">
                                            @error('brand_name') <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Dosage From</label>
                                            <input type="text" class="form-control" wire:model="dosage_form" placeholder="Cth. Tablet, Syrup, Injeksi">
                                            @error('dosage_form') <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Strength</label>
                                            <input type="text" class="form-control" wire:model="strength" placeholder="Cth. 500mg">
                                            @error('strength') <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Minimal Stock</label>
                                            <input type="number" class="form-control" wire:model="minimal_stock" placeholder="100">
                                            @error('minimal_stock') <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Satuan</label>
                                            <input type="text" class="form-control" wire:model="unit" placeholder="Cth. Strip, Vial">
                                            @error('unit') <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Status</label>
                                            <select class="form-control" wire:model="is_active">
                                                <option value="1">Aktif</option>
                                                <option value="0">Non Aktif</option>
                                            </select>
                                            @error('is_active') <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        
                                    </div> 
 
                                    <button class="btn btn-primary mt-3">Simpan</button>

                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- END MODAL --}}

                {{-- BATCH MODAL --}}
                <div wire:ignore.self id="InventoryBatchMdl" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    {{ $batch_id ? 'Edit Stock Batch' : 'Tambah Stock Batch' }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <form wire:submit.prevent="saveBatch">

                                    <div class="row">
                                        @if(!$this->isEdit)
                                            <div class="col-md-6 mb-2 position-relative">
                                                <label>Item</label>
                                                    <input id="item-search-input" type="text" class="form-control" placeholder="Ketikan minimal 3 huruf untuk mencari..." autocomplete="off">
                                                    <input type="hidden" wire:model="item_id" id="item-id-hidden">
                                                    @error('item_id') <small class="text-danger">{{ $message }}</small> @enderror

                                                    <div id="item-suggestions" class="list-group position-absolute" style="z-index:1050; width:100%; display:none"></div>

                                                    @if($selected_item_label)
                                                    <div class="small text-muted mt-1">Terpilih: {{ $selected_item_label }}</div>
                                                    @endif
                                            </div>
                                        @endif

                                        <div class="col-md-6 mb-2">
                                            <label>Batch Number</label>
                                            <input type="text" class="form-control" wire:model="batch_number">
                                            @error('batch_number') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <label>Expired Date</label>
                                            <input type="date" class="form-control" wire:model="expired_date">
                                            @error('expired_date') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <label>Purchase Price</label>
                                            <input type="number" step="0.01" class="form-control" wire:model="purchase_price">
                                            @error('purchase_price') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <label>Sell Price</label>
                                            <input type="number" step="0.01" class="form-control" wire:model="sell_price">
                                            @error('sell_price') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>

                                        <div class="col-md-4 mb-2">
                                            <label>Stock Qty</label>
                                            <input type="number" class="form-control" wire:model="stock_qty">
                                            @error('stock_qty') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>
                                    </div>

                                    <button class="btn btn-primary mt-3">Simpan</button>

                                </form>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- END BATCH MODAL --}}

            </div>
        </div>
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        let modalDoctor = null;
        let itemTableInstance = null;
        let batchTableInstance = null;
        let modalBatch = null;
 
        document.addEventListener('livewire:init', function () {

            // initialize only the visible (items) table on load
            initItemTable();

            // when switching tabs, initialize the target table if not already
            document.querySelectorAll('#inventoryTabs button[data-bs-toggle="tab"]').forEach(btn => {
                btn.addEventListener('shown.bs.tab', function (e) {
                    const target = e.target.getAttribute('data-bs-target');
                    if (target === '#tab-items') {
                        if (itemTableInstance) itemTableInstance.destroy();
                        setTimeout(() => initItemTable(), 50);
                    } else if (target === '#tab-batches') {
                        if (batchTableInstance) batchTableInstance.destroy();
                        setTimeout(() => initBatchTable(), 50);
                    }
                });
            });

            Livewire.on('itemTable', () => {
                if (itemTableInstance) itemTableInstance.destroy();
                setTimeout(() => initItemTable(), 100);
            });

            Livewire.on('batchTable', () => {
                if (batchTableInstance) batchTableInstance.destroy();
                setTimeout(() => initBatchTable(), 100);
            });
        });

        function initItemTable() {
            itemTableInstance = $('#itemTable').DataTable({
                responsive: true,
                destroy: true,
                pageLength: 10
            });
        }

        function initBatchTable() {
            batchTableInstance = $('#batchTable').DataTable({
                responsive: true,
                destroy: true,
                pageLength: 10
            });
        }

        window.addEventListener('show-form', () => {
            const modal = document.getElementById('InventoryMdl');
            if (!modalDoctor) modalDoctor = new bootstrap.Modal(modal);
            modalDoctor.show();
        });

        window.addEventListener('show-batch-form', () => {
            const modal = document.getElementById('InventoryBatchMdl');
            if (!modalBatch) modalBatch = new bootstrap.Modal(modal);
            modalBatch.show();
            initItemSearch();
            // focus input when modal is visible
            setTimeout(() => {
                const inp = document.getElementById('item-search-input');
                if (inp) inp.focus();
            }, 200);
        });

        function initItemSearch() {
            const input = document.getElementById('item-search-input');
            const suggestions = document.getElementById('item-suggestions');
            if (!input || !suggestions) return;

            let timer = null;

            input.addEventListener('input', function (e) {
                const v = e.target.value.trim();
                clearTimeout(timer);
                suggestions.style.display = 'none';
                suggestions.innerHTML = '';

                if (v.length < 3) return;

                timer = setTimeout(() => {
                    fetch(`{{ route('inventory.items.search') }}?q=${encodeURIComponent(v)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data || !data.length) {
                                suggestions.style.display = 'none';
                                return;
                            }

                            suggestions.innerHTML = '';
                            data.slice(0,10).forEach(item => {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'list-group-item list-group-item-action';
                                btn.textContent = item.text;
                                btn.dataset.id = item.id;
                                btn.addEventListener('click', function () {
                                    input.value = item.text;
                                    const hidden = document.getElementById('item-id-hidden');
                                    if (hidden) hidden.value = item.id;
                                    @this.call('selectItem', item.id);
                                    suggestions.style.display = 'none';
                                });
                                suggestions.appendChild(btn);
                            });
                            suggestions.style.display = 'block';
                        }).catch(err => {
                            console.error('search error', err);
                        });
                }, 250);
            });

            // close suggestions when clicking outside
            document.addEventListener('click', function (e) {
                if (!suggestions.contains(e.target) && e.target !== input) {
                    suggestions.style.display = 'none';
                }
            });
        }

        window.addEventListener('activate-batches-tab', () => {
            const el = document.querySelector('#batches-tab');
            if (el) {
                try {
                    const tab = new bootstrap.Tab(el);
                    tab.show();
                } catch (e) {
                    el.click();
                }
            }
        });

        window.addEventListener('hide-form', () => {
            if (modalDoctor) modalDoctor.hide();
            document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
        });

        window.addEventListener('hide-batch-form', () => {
            if (modalBatch) modalBatch.hide();
            document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
            // clear suggestions and input
            const suggestions = document.getElementById('item-suggestions');
            if (suggestions) {
                suggestions.innerHTML = '';
                suggestions.style.display = 'none';
            }
            const input = document.getElementById('item-search-input');
            if (input) input.value = '';
        });

        window.addEventListener('confirm-update-data', () => {
            if (confirm("Update status?")) {
                @this.call('updateinventorystatus');
            }
        });

        window.addEventListener('confirm-delete-batch', () => {
            if (confirm("Hapus batch ini?")) {
                @this.call('deleteBatch');
            }
        });

        function autoCaps(element) {
            element.addEventListener("input", function () {
            this.value = this.value.toUpperCase();
            });
        }
        
        autoCaps(document.getElementById("item_code_input"));

        window.addEventListener('toastr:success', e => toastr.success(e.detail.message));
        window.addEventListener('toastr:error', e => toastr.error(e.detail.message));
        window.addEventListener('toastr:info', e => toastr.info(e.detail.message));
        window.addEventListener('toastr:warning', e => toastr.warning(e.detail.message));
    </script>

</div>