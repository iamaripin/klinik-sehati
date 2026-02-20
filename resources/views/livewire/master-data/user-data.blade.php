<div>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body pc-component">
                <button type="button" class="btn btn-primary mb-2" wire:click="openCreateModal"><i class="ti ti-user-plus"></i> Tambah Data</button>

                <table id="userTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th width="150px">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $u)
                            <tr wire:key="row-{{ $u->id }}">
                                <td>{{ $u->username }}</td>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->role }}</td>
                                @if($u->status == 'active')
                                    <td><span class="badge bg-success">Active</span></td>
                                @else
                                    <td><span class="badge bg-secondary">Non Active</span></td>
                                @endif 
                                <td>
                                    @if(auth()->user()->username == $u->username || auth()->user()->role == 'dev')
                                        <button class="btn btn-info btn-sm" wire:click="openEditModal({{ $u->id }})"><i class="ti ti-edit-circle"></i></button>
                                    @endif
                                    @if(auth()->user()->role == 'dev')
                                        @if($u->status == 'active')
                                            <button class="btn btn-danger btn-sm" wire:click="userConfirm({{ $u->id }})"><i class="ti ti-trash"></i></button>
                                        @else
                                            <button class="btn btn-warning btn-sm" wire:click="userConfirm({{ $u->id }})"><i class="ti ti-trash"></i></button>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table> 

    {{-- MODAL --}}
    <div wire:ignore.self id="UserModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="UserModalLabel"
        aria-hidden="true">

        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $isEdit ? 'Edit User' : 'Tambah User' }}
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form wire:submit.prevent="save">

                        <div class="mb-2">
                            <label>Nama</label>
                            <input type="text" class="form-control" wire:model="name">
                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-2">
                            <label>Email</label>
                            <input type="email" class="form-control" wire:model="email">
                            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-2">
                            <label>Password @if($isEdit) <small>(isi kalau mau ubah)</small> @endif</label>
                            <input type="password" class="form-control" wire:model="password">
                            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-2">
                            <label>Username</label>
                            <input type="text" class="form-control" wire:model="username">
                            @error('username') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-2">
                            <label>Title</label>
                            <input type="text" class="form-control" wire:model="title">
                        </div>

                        <div class="mb-2">
                            <label>Suffix</label>
                            <input type="text" class="form-control" wire:model="suffix">
                        </div>

                        <div class="mb-2">
                            <label>Tanggal Lahir</label>
                            <input type="date" class="form-control" wire:model="user_dob">
                        </div>

                        <div class="mb-2">
                            <label>Gender</label>
                            <select class="form-control" wire:model="gender">
                                <option value="">- pilih -</option>
                                <option value="male">Laki-laki</option>
                                <option value="female">Perempuan</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>Role</label>
                            <select class="form-control" wire:model="role">
                                <option value="">- pilih -</option>
                                <option value="dev">dev</option>
                                <option value="admin">Admin</option>
                                <option value="doctor">Doctor</option>
                                <option value="nurse">Nurse</option>
                                <option value="pharmacy">pharmacy</option>
                            </select>
                            @error('role') <small class="text-danger">{{ $message }}</small> @enderror
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
        let modalUser = null;
        let dataTable = null;

        document.addEventListener('livewire:init', function () {

            initDataTable();

           Livewire.on('refresh-table', () => {
            if (dataTable) {
                dataTable.destroy();
            }
            
            setTimeout(() => {
                initDataTable();
                }, 100); // kasih jeda biar Livewire sempat render ulang
            });
        }); 
        function initDataTable() {
            $('#userTable').DataTable({
                responsive: true,
                destroy: true,
                pageLength: 10
            });
        }

        window.addEventListener('show-form', () => {
            const modalEl = document.getElementById('UserModal');

            if (!modalUser) {
                modalUser = new bootstrap.Modal(modalEl);
            }
            modalUser.show();
        });

        window.addEventListener('hide-form', () => {
            if (modalUser) modalUser.hide();
            document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
        });

        window.addEventListener('confirm-update-user', () => {
            if (confirm("Update data User?")) {
                @this.call('updateUserStatus');
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