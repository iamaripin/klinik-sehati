<div> 
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body pc-component"> 
                    <button type="button" class="btn btn-primary mb-2" wire:click="openCreateModal">
                        <i class="ti ti-user-plus"></i> Tambah Pasien Baru
                    </button>

                    <div class="form-group">
                            <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari Nama Pasien atau Nomor Rekam Medis" wire:model.live="searchQuery" wire:keydown.enter="search">
                            <button class="btn btn-outline-secondary" type="button" wire:click="search"><i class="ti ti-search"></i></button>
                        </div>
                    </div>
                    
                    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active text-uppercase" id="patient-tab" data-bs-toggle="tab" href="#patient" role="tab"
                                aria-controls="patient" aria-selected="true">Data Pasien</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-uppercase" id="relation-tab" data-bs-toggle="tab" href="#relation" role="tab"
                                aria-controls="relation" aria-selected="false">Data Relasi</a>
                        </li> 
                    </ul>
                    <div class="tab-content" id="myTabContent" style="{{ $hasSearched ? '' : 'display: none;' }}">
                        <div class="tab-pane fade show active" id="patient" role="tabpanel" aria-labelledby="patient-tab">
                            @if(count($searchResults) > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        {{-- @if(count($searchResults) > 1)
                                            <button class="btn btn-primary btn-sm" wire:click="editSelected" @if(!$selectedMrCode) disabled @endif>Edit Selected</button>
                                        @endif --}}
                                    </div>
                                    <div class="text-muted">{{ count($searchResults) }} pasien ditemukan.</div>
                                </div>

                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>MR Code</th>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Kontak</th>
                                            <th>Alamat</th>
                                            <th width="180px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($searchResults as $patient)
                                            <tr class="{{ $selectedMrCode == $patient->mr_code ? 'table-success' : '' }}">
                                                <td>{{ $patient->mr_code ?? '-' }}</td>
                                                <td>{{ $patient->patient_nik ?? '-' }}</td>
                                                <td>{{ $patient->patient_name ?? '-' }}</td>
                                                <td>{{ $patient->patient_contact ?? '-' }}</td>
                                                <td>{{ $patient->patient_address ?? '-' }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm" wire:click="editPatient('{{ $patient->mr_code }}')"><i class="ti ti-edit-circle"></i></button>
                                                    <button class="btn btn-outline-primary btn-sm ms-1" wire:click="selectPatient('{{ $patient->mr_code }}')">@if($selectedMrCode == $patient->mr_code) Selected @else Select @endif</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">@if($hasSearched && empty($searchQuery)) Masukkan kata kunci pencarian @else Tidak ada hasil pencarian @endif</p>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="relation" role="tabpanel" aria-labelledby="relation-tab">
                            @if(count($searchResults) > 0)
                                <button class="btn btn-success btn-sm mb-3" wire:click="createRelation('')">Tambah Relasi</button>
                            @endif
                            @if(count($relationResults) > 0)
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Telepon</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Golongan Darah</th>
                                            <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($relationResults as $relation)
                                            <tr>
                                                <td>{{ $relation->relation_nik ?? '-' }}</td>
                                                <td>{{ $relation->relation_name ?? '-' }}</td>
                                                <td>{{ $relation->relation_phone ?? '-' }}</td>
                                                <td>{{ $relation->relation_sex ?? '-' }}</td>
                                                <td>{{ $relation->relation_blood ?? '-' }}</td>
                                                <td>
                                                    <button class="btn btn-warning btn-sm" wire:click="editRelation({{ $relation->id }})"><i class="ti ti-edit-circle"></i></button>
                                                    <button class="btn btn-danger btn-sm" wire:click="deleteRelation({{ $relation->id }})"><i class="ti ti-trash"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">@if($hasSearched) Tidak ada data relasi untuk pasien ini @else Pilih pasien untuk melihat data relasi @endif</p>
                            @endif
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PATIENT MODAL --}}
    <div wire:ignore.self id="PatientModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Pasien' : 'Tambah Pasien' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="savePatient">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Nama Pasien</label>
                                <input type="text" class="form-control" wire:model="patientName">
                                @error('patientName') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>NIK</label>
                                <input type="text" class="form-control" wire:model="patientNik" placeholder="16 Digit NIK">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>MR Code</label>
                                <input type="text" class="form-control" wire:model="mrCode" @if(!$isEdit) readonly @endif>
                                @error('mrCode') <small class="text-danger">{{ $message }}</small> @enderror
                                @if(!$isEdit)
                                    <small class="text-muted">MR Code akan di-generate otomatis saat pembuatan</small>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Nomor Kartu</label>
                                <input type="text" class="form-control" wire:model="patientCardNumber">
                                @error('patientCardNumber') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Jenis Kelamin</label>
                                <select class="form-control" wire:model="patientSex">
                                    <option value="">- Pilih -</option>
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Tanggal Lahir</label>
                                <input type="date" class="form-control" wire:model="patientDob">
                            </div> 
                            <div class="col-md-6 mb-3">
                                <label>Telepon/WA</label>
                                <input type="text" class="form-control" wire:model="patientContact" placeholder="08123456789">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Golongan Darah</label>
                                <select class="form-control" wire:model="patientBlood">
                                    <option value="">- Pilih -</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea class="form-control" wire:model="patientAddress"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Agama</label>
                                <select class="form-control" wire:model="patientReligion">
                                    <option value="">- Pilih -</option>
                                    <option value="Islam">Islam</option>
                                    <option value="Protestan">Protestan</option>
                                    <option value="Katolik">Katolik</option>
                                    <option value="Hindu">Hindu</option>
                                    <option value="Buddha">Buddha</option>
                                    <option value="Konghucu">Konghucu</option>
                                </select>
                                {{-- <input type="text" class="form-control" wire:model="patientReligion"> --}}
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Pekerjaan</label>
                                <input type="text" class="form-control" wire:model="patientJob">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Status</label>
                                <select class="form-control" wire:model="patientStatus">
                                    <option value="">- Pilih -</option>
                                    <option value="Belum Kawin">Belum Kawin</option>
                                    <option value="Kawin">Kawin</option>
                                    <option value="Cerai Mati">Cerai Mati</option>
                                    <option value="Cerai Hidup">Cerai Hidup</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Kontak Darurat</label>
                                <input type="text" class="form-control" wire:model="patientEmergencyContact">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Alergi</label>
                                <input type="text" class="form-control" wire:model="patientAlergy">
                            </div> 
                        </div>

                        <div class="mb-3">
                            <label>Catatan</label>
                            <textarea class="form-control" wire:model="patientNotes"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- RELATION MODAL --}}
    <div wire:ignore.self id="RelationModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $editRelationId ? 'Edit Relasi' : 'Tambah Relasi' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveRelation">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Kode Relasi</label>
                                <input type="text" class="form-control" wire:model="relationCode" placeholder="MR Code" readonly>
                            </div>
                            @if($editRelationId)
                            <div class="col-md-6 mb-3" style="display: none">
                                <label>MR Code (Pasien)</label>
                                <input type="text" class="form-control" wire:model="relationMrCode" readonly placeholder="Auto dari pasien terpilih">
                                @error('relationMrCode') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            @endif
                            <div class="col-md-6 mb-3">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control" wire:model="relationName" placeholder="Cth. Bagoes (Suami)">
                                @error('relationName') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                            <label>Hubungan</label>
                            <select class="form-control" wire:model="relationStatus">
                                <option value="">- Pilih -</option>
                                <option value="Suami">Suami</option>
                                <option value="Istri">Istri</option>
                                <option value="Orang Tua">Orang Tua</option>
                                <option value="Saudara">Saudara</option>
                            </select>@error('relationStatus') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                            <div class="col-md-6 mb-3">
                                <label>NIK</label>
                                <input type="text" class="form-control" wire:model="relationNik" placeholder="16 Digit NIK">
                                @error('relationNik') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Jenis Kelamin</label>
                                <select class="form-control" wire:model="relationSex">
                                    <option value="">- Pilih -</option>
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>@error('relationSex') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Tanggal Lahir</label>
                                <input type="date" class="form-control" wire:model="relationDob">
                                @error('relationDob') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Golongan Darah</label>
                                <select class="form-control" wire:model="relationBlood">
                                    <option value="">- Pilih -</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="AB">AB</option>
                                    <option value="O">O</option>
                                </select>@error('relationBlood') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Telepon</label>
                                <input type="text" class="form-control" wire:model="relationPhone" placeholder="08123456789">
                                @error('relationPhone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Alamat</label>
                            <textarea class="form-control" wire:model="relationAddress"></textarea>
                            @error('relationAddress') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div> 

    {{-- JAVASCRIPT --}}
    <script>
        let patientModal = null;
        let relationModal = null;

        window.addEventListener('show-patient-modal', () => {
            const modalEl = document.getElementById('PatientModal');
            if (!patientModal) {
                patientModal = new bootstrap.Modal(modalEl);
            }
            patientModal.show();
        });

        window.addEventListener('hide-patient-modal', () => {
            if (patientModal) patientModal.hide();
            document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
        });

        window.addEventListener('show-relation-modal', () => {
            const modalEl = document.getElementById('RelationModal');
            if (!relationModal) {
                relationModal = new bootstrap.Modal(modalEl);
            }
            relationModal.show();
        });

        window.addEventListener('hide-relation-modal', () => {
            if (relationModal) relationModal.hide();
            document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
        });

        window.addEventListener('toastr:success', e => {
            toastr.success(e.detail.message);
        });

        window.addEventListener('toastr:info', e => {
            toastr.info(e.detail.message);
        });

        window.addEventListener('toastr:error', e => {
            toastr.error(e.detail.message || 'Terjadi kesalahan');
        });

        window.addEventListener('toastr:warning', e => {
            toastr.warning(e.detail.message);
        });
    </script>
</div> 
