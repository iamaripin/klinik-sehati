<div>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body pc-component">

                <button type="button" class="btn btn-primary mb-2" wire:click="openCreateModal">
                    <i class="ti ti-list-numbers"></i> Tambah Supplier
                </button>

                <table id="supplierTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Supplier</th>
                            <th>Kontak</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th width="150px">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $s)
                        <tr wire:key="row-{{ $s->id }}">
                            <td>{{ $s->supplier_code }}</td>
                            <td>{{ $s->supplier_name }}</td>
                            <td>{{ $s->contact_name }}</td>
                            <td>{{ $s->phone }}</td>
                            <td>{{ $s->email }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" wire:click="openEditModal({{ $s->id }})">
                                    <i class="ti ti-edit-circle"></i>
                                </button>

                                <button class="btn btn-danger btn-sm" wire:click="supplierConfirm({{ $s->id }})">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- MODAL --}}
                <div wire:ignore.self id="SupplierModal" class="modal fade" tabindex="-1" role="dialog"
                    aria-labelledby="SupplierModalLabel" aria-hidden="true">

                    <div class="modal-dialog" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    {{ $isEdit ? 'Edit Supplier' : 'Tambah Supplier' }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <form wire:submit.prevent="save">

                                    <div class="mb-2">
                                        <label>Kode Supplier</label>
                                        <input type="text" class="form-control" wire:model="supplier_code">
                                        @error('supplier_code') <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label>Nama Supplier</label>
                                        <input type="text" class="form-control" wire:model="supplier_name">
                                        @error('supplier_name') <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label>Nama Kontak</label>
                                        <input type="text" class="form-control" wire:model="contact_name">
                                    </div>

                                    <div class="mb-2">
                                        <label>Phone</label>
                                        <input type="text" class="form-control" wire:model="phone">
                                    </div>

                                    <div class="mb-2">
                                        <label>Email</label>
                                        <input type="email" class="form-control" wire:model="email">
                                    </div>

                                    <div class="mb-2">
                                        <label>Alamat</label>
                                        <textarea class="form-control" wire:model="address"></textarea>
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
        let modalSupplier = null;
        let supplierTable = null;

        document.addEventListener('livewire:init', function () {

            initDataTable();

            Livewire.on('refresh-table', () => {
                if (supplierTable) {
                    supplierTable.destroy();
                }

                setTimeout(() => {
                    initDataTable();
                }, 100);
            });
        });

        function initDataTable() {
            supplierTable = $('#supplierTable').DataTable({
                responsive: true,
                destroy: true,
                pageLength: 10
            });
        }

        window.addEventListener('show-form', () => {
            const modalEl = document.getElementById('SupplierModal');

            if (!modalSupplier) {
                modalSupplier = new bootstrap.Modal(modalEl);
            }
            modalSupplier.show();
        });

        window.addEventListener('hide-form', () => {
            if (modalSupplier) modalSupplier.hide();
            document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
        });

        window.addEventListener('confirm-delete-supplier', () => {
            if (confirm("Hapus supplier ini?")) {
                @this.call('deleteSupplier');
            }
        });

        window.addEventListener('toastr:success', e => {
            toastr.success(e.detail.message);
        });

        window.addEventListener('toastr:error', e => {
            toastr.error(e.detail.message || 'Terjadi kesalahan');
        });

        window.addEventListener('toastr:info', e => {
            toastr.info(e.detail.message);
        });

        window.addEventListener('toastr:warning', e => {
            toastr.warning(e.detail.message);
        });
    </script>

</div>