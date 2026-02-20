<div>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body pc-component">

                <button type="button" class="btn btn-primary mb-2" wire:click="openCreateModal">
                    Tambah Data
                </button>

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
                        <tr wire:key="row-{{ $d->id }}" @class([ 'table-danger'=> $d->minimal_stock < $d->
                                batches->sum('stock_qty'),
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
                                                <input type="text" class="form-control" wire:model="item_code"
                                                    @if($this->isEdit) readonly @endif>
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
                                                    <option value="{{ $c->category_code }}">{{ $c->category_name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('category') <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Generic Name</label>
                                            <input type="text" class="form-control" wire:model="generic_name"
                                                placeholder="Cth. Paracetamol">
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
                                            <input type="text" class="form-control" wire:model="dosage_form"
                                                placeholder="Cth. Tablet, Syrup, Injeksi">
                                            @error('dosage_form') <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Strength</label>
                                            <input type="text" class="form-control" wire:model="strength"
                                                placeholder="Cth. 500mg">
                                            @error('strength') <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Minimal Stock</label>
                                            <input type="number" class="form-control" wire:model="minimal_stock"
                                                placeholder="100">
                                            @error('minimal_stock') <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Satuan</label>
                                            <input type="text" class="form-control" wire:model="unit"
                                                placeholder="Cth. Strip, Vial">
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

            </div>
        </div>
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        let modalDoctor = null;
        let dataTable = null;

        document.addEventListener('livewire:init', function () {

            initDataTable();

            Livewire.on('itemTable', () => {
                if (dataTable) dataTable.destroy();

                setTimeout(() => {
                    initDataTable();
                }, 100);
            });
        });

        function initDataTable() {
            $('#itemTable').DataTable({
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

        window.addEventListener('hide-form', () => {
            if (modalDoctor) modalDoctor.hide();
            document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
        });

        window.addEventListener('confirm-update-data', () => {
            if (confirm("Update status?")) {
                @this.call('updateinventorystatus');
            }
        });

        window.addEventListener('toastr:success', e => toastr.success(e.detail.message));
        window.addEventListener('toastr:error', e => toastr.error(e.detail.message));
        window.addEventListener('toastr:info', e => toastr.info(e.detail.message));
        window.addEventListener('toastr:warning', e => toastr.warning(e.detail.message));
    </script>

</div>