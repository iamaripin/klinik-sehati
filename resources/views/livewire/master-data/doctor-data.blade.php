<div>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body pc-component">

                <button type="button" class="btn btn-primary mb-2" wire:click="openCreateModal">
                    <i class="ti ti-user-plus"></i> Tambah Dokter
                </button>

                <table id="doctorTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Dokter</th>
                            <th>Jenis Kelamin</th>
                            <th>Telepon</th>
                            <th>Spesialis</th>
                            <th>Status</th>
                            <th width="150px">#</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($data as $d)
                        <tr wire:key="doctor-{{ $d->id }}">
                            <td>{{ $d->doctor_code }}</td>
                            <td>{{ $d->doctor_name }}</td>
                            <td>{{ $d->doctor_sex == 'M' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td>{{ $d->doctor_phone }}</td>
                            <td>{{ $d->specialist ?? '-' }}</td>

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
                                    wire:click="doctorConfirm({{ $d->id }})">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>

                {{-- MODAL --}}
                <div wire:ignore.self id="DoctorModal" class="modal fade" tabindex="-1">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    {{ $isEdit ? 'Edit Data Dokter' : 'Tambah Data Dokter' }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <form wire:submit.prevent="save">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-2">
                                                <label>Kode Dokter</label>
                                                <input type="text" id="doctor_code" class="form-control" wire:model="doctor_code">
                                                @error('doctor_code') <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>NIK</label>
                                            <input type="text" class="form-control" wire:model="doctor_nik" placeholder="16 Digit">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Spesialis</label>
                                            <input type="text" class="form-control" wire:model="specialist" placeholder="Obgyn">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Gelar Depan (Prefix)</label>
                                            <input type="text" class="form-control" wire:model="doctor_prefix" placeholder="dr.">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Nama Dokter</label>
                                            <input type="text" class="form-control" wire:model="doctor_name" placeholder="Bagoes">
                                            @error('doctor_name') <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Gelar Belakang (Suffix)</label>
                                            <input type="text" class="form-control" wire:model="doctor_suffix" placeholder="SpOG">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Tanggal Lahir</label>
                                            <input type="date" class="form-control" wire:model="doctor_dob">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Jenis Kelamin</label>
                                            <select class="form-control" wire:model="doctor_sex">
                                                <option value="">- pilih -</option>
                                                <option value="M">Laki-laki</option>
                                                <option value="F">Perempuan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>No. Telepon</label>
                                            <input type="text" class="form-control" wire:model="doctor_phone" placeholder="08123456">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>Email</label>
                                            <input type="email" class="form-control" wire:model="doctor_email" placeholder="contoh@email.com">
                                            @error('doctor_email') <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div> 
                                        <div class="col-md-4 mb-2">
                                            <label>No. SIP</label>
                                            <input type="text" class="form-control" wire:model="sip_number" placeholder="Opsional">
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label>SIP Expired</label>
                                            <input type="date" class="form-control" wire:model="sip_expiry">
                                        </div> 
                                        <div class="col-md-12 mb-2">
                                            <label>Alamat</label>
                                            <textarea class="form-control" wire:model="doctor_address"></textarea>
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

            Livewire.on('refresh-table', () => {
                if (dataTable) dataTable.destroy();

                setTimeout(() => {
                    initDataTable();
                }, 100);
            });
        });

        function initDataTable() {
            $('#doctorTable').DataTable({
                responsive: true,
                destroy: true,
                pageLength: 10
            });
        }

        window.addEventListener('show-form', () => {
            const modal = document.getElementById('DoctorModal');
            if (!modalDoctor) modalDoctor = new bootstrap.Modal(modal);
            modalDoctor.show();
        });

        window.addEventListener('hide-form', () => {
            if (modalDoctor) modalDoctor.hide();
            document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
        });

        window.addEventListener('confirm-update-doctor', () => {
            if (confirm("Update status dokter?")) {
                @this.call('updateDoctorStatus');
            }
        });

        function autoCaps(element) {
            element.addEventListener("input", function () {
            this.value = this.value.toUpperCase();
            });
        }
        
        autoCaps(document.getElementById("doctor_code"));

        window.addEventListener('toastr:success', e => toastr.success(e.detail.message));
        window.addEventListener('toastr:error', e => toastr.error(e.detail.message));
        window.addEventListener('toastr:info', e => toastr.info(e.detail.message));
        window.addEventListener('toastr:warning', e => toastr.warning(e.detail.message));
    </script>

</div>