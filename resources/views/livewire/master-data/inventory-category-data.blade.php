<div>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body pc-component">

                <button type="button" class="btn btn-primary mb-2" wire:click="openCreateModal">
                   <i class="ti ti-list-numbers"></i> Tambah Kategori
                </button>

                <table id="categoryTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th>Status</th>
                            <th width="150px">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $c)
                        <tr wire:key="row-{{ $c->id }}">
                            <td>{{ $c->category_code }}</td>
                            <td>{{ $c->category_name }}</td>
                            <td>{{ $c->description }}</td>
                            <td>
                                <span class="badge {{ $c->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $c->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm" wire:click="openEditModal({{ $c->id }})">
                                    <i class="ti ti-edit-circle"></i>
                                </button>

                                <button class="btn btn-danger btn-sm" wire:click="categoryConfirm({{ $c->id }})">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- MODAL --}}
                <div wire:ignore.self id="CategoryModal" class="modal fade" tabindex="-1" role="dialog"
                    aria-labelledby="CategoryModalLabel" aria-hidden="true">

                    <div class="modal-dialog" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    {{ $isEdit ? 'Edit Kategori' : 'Tambah Kategori' }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <form wire:submit.prevent="save">

                                    <div class="mb-2">
                                        <label>Kode Kategori</label>
                                        <input type="text" class="form-control" wire:model="category_code">
                                        @error('category_code') <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label>Nama Kategori</label>
                                        <input type="text" class="form-control" wire:model="category_name">
                                        @error('category_name') <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="mb-2">
                                        <label>Deskripsi</label>
                                        <textarea class="form-control" wire:model="description"></textarea>
                                    </div>

                                    <div class="mb-2">
                                        <label>Status</label>
                                        <select class="form-select" wire:model="is_active">
                                            <option value="1">Aktif</option>
                                            <option value="0">Nonaktif</option>
                                        </select>
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
        let modalCategory = null;
        let categoryTable = null;

        document.addEventListener('livewire:init', function () {

            initDataTable();

            Livewire.on('refresh-table', () => {
                if (categoryTable) {
                    categoryTable.destroy();
                }

                setTimeout(() => {
                    initDataTable();
                }, 100);
            });
        });

        function initDataTable() {
            categoryTable = $('#categoryTable').DataTable({
                responsive: true,
                destroy: true,
                pageLength: 10
            });
        }

        window.addEventListener('show-form', () => {
            const modalEl = document.getElementById('CategoryModal');

            if (!modalCategory) {
                modalCategory = new bootstrap.Modal(modalEl);
            }
            modalCategory.show();
        });

        window.addEventListener('hide-form', () => {
            if (modalCategory) modalCategory.hide();
            document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
        });

        window.addEventListener('confirm-delete-category', () => {
            if (confirm("Hapus kategori ini?")) {
                @this.call('deleteCategory');
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