<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4"> 
                <div class="card-body">
                    <!-- Search Section -->
                    <div class="mb-2">
                        <!-- Search Section -->
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchQuery" wire:model.live="searchQuery" wire:keydown.enter="search"
                                placeholder="Cari berdasarkan nama, MR code, atau nomor telepon...">
                            <button class="btn btn-primary" type="button" wire:click="createNewPatient"><i class="ti ti-user-plus"></i> Buat Baru</button>
                        </div> 
                    <div class="list-group mt-2" style="max-height: 300px; overflow-y: auto;">
                        @foreach($searchResults as $patient)
                        <button type="button" class="list-group-item list-group-item-action"
                            wire:click="selectPatient('{{ $patient->mr_code }}')">
                            <div class="d-flex justify-content-between">
                                <strong>{{ strtoupper($patient->patient_name) }}</strong>
                                <small class="text-muted">{{ $patient->mr_code }}</small>
                            </div>
                            <small class="text-muted">{{ $patient->patient_dob->format('d-m-Y') ?? '-' }}</small><br>
                            <small class="text-muted">{{ $patient->patient_address ?? '-' }}</small>
                        </button>
                        @endforeach
                    </div>
                    {{-- @endif --}}
                    </div>
                    
                    @if($selectedPatient)
                    <!-- Selected Patient Info -->
                    <div class="alert alert-info" role="alert">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Pasien:</strong> {{ strtoupper($selectedPatient->patient_name) }}<br>
                                <strong>MR Code:</strong> {{ $selectedPatient->mr_code }}<br>
                                <strong>Alamat:</strong> {{ $selectedPatient->patient_address }}<br>
                                <strong>Visit No:</strong> {{ $visitNo }}
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deselectPatient">
                                Ganti Pasien
                            </button>
                        </div>
                    </div>

                        <!-- Registration Form -->
                        <form wire:submit.prevent="save">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3" id="datetimepicker1">
                                        <label for="visitDate" class="form-label">Tanggal Kunjungan *</label>
                                        <input type="date" 
                                            class="form-control @error('visitDate') is-invalid @enderror"
                                            id="visitDate"
                                            wire:model="visitDate">
                                        @error('visitDate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="visitTime" class="form-label">Waktu Kunjungan *</label>
                                        <input type="time" 
                                            class="form-control @error('visitTime') is-invalid @enderror"
                                            id="visitTime"
                                            wire:model="visitTime">
                                        @error('visitTime') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="poli" class="form-label">Poli *</label>
                                        <select class="form-select @error('poli') is-invalid @enderror" id="poli" wire:model="poli">
                                            <option value="POLI-OBGYN" selected>POLI OBGYN</option>
                                            <option value="POLI-UMUM" >POLI UMUM</option>
                                            <option value="POLI-ANAK">POLI ANAK</option>
                                            <option value="POLI-GIGI">POLI GIGI</option>
                                        </select>
                                        @error('poli') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="doctorCode" class="form-label">Dokter *</label>
                                        <input type="hidden"  wire:model="doctorCode">
                                        <input type="text" value="{{ $doctor->doctor_prefix . ' '.$doctor->doctor_name . ' '. $doctor->doctor_suffix }}" class="form-control @error('doctorCode') is-invalid @enderror" id="doctorCode" >
                                        @error('doctorCode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        {{-- <select id="doctorCode" class="form-select @error('doctorCode') is-invalid @enderror" wire:model="doctorCode">
                                        
                                        <option value="">-- Pilih Dokter --</option>
                                        
                                            @foreach ($doctors as $doctor)
                                            <option value="{{ $doctor->doctor_code }}" selected>
                                                {{ $doctor->doctor_prefix }} {{ $doctor->doctor_name }} {{ $doctor->doctor_suffix }}
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('doctorCode') <div class="invalid-feedback">{{ $message }}</div> @enderror --}}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="visitType" class="form-label">Jenis Kunjungan *</label>
                                        <select class="form-select @error('visitType') is-invalid @enderror"
                                            id="visitType"
                                            wire:model="visitType">
                                            <option value="POLI">POLI</option>
                                            <option value="IGD">IGD</option>
                                        </select>
                                        @error('visitType') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="paymentType" class="form-label">Tipe Pembayaran *</label>
                                        <select class="form-select @error('paymentType') is-invalid @enderror" id="paymentType" wire:model="paymentType">
                                            <option value="">-- Pilih Pembayaran --</option>
                                            <option value="PRIBADI">PRIBADI</option>
                                            <option value="CLAIM">CLAIM</option>
                                            {{-- <option value="ASURANSI">ASURANSI</option> --}}
                                        </select>
                                        @error('paymentType') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="complaint" class="form-label">Keluhan</label>
                                        <select class="form-select @error('complaint') is-invalid @enderror" id="complaint" wire:model="complaint">
                                            <option value="">-- Pilih Keluhan --</option>
                                            <option value="Periksa Kehamilan">Periksa Kehamilan</option>
                                            <option value="USG">USG</option>
                                            <option value="Papsmear">Papsmear</option>
                                            <option value="Vaksin">Vaksin</option>
                                            <option value="Konsultasi Reproduksi">Konsultasi Reproduksi</option>
                                            {{-- <option value="ASURANSI">ASURANSI</option> --}}
                                        </select>
                                        @error('complaint') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        {{-- <textarea class="form-control @error('complaint') is-invalid @enderror" id="complaint"
                                            wire:model="complaint" rows="3"></textarea>
                                        @error('complaint') <div class="invalid-feedback">{{ $message }}</div> @enderror --}}
                                    </div>
                                </div>
                            </div>
                                    {{-- <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="insurance_number_at_visit" class="form-label">Nomor Asuransi *</label>
                                                <input type="text"
                                                    class="form-control @error('insurance_number_at_visit') is-invalid @enderror"
                                                    id="insurance_number_at_visit"
                                                    wire:model="insurance_number_at_visit">
                                                @error('insurance_number_at_visit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="insurance_company_at_visit" class="form-label">Nama Perusahaan Asuransi *</label>
                                                <input type="text"
                                                    class="form-control @error('insurance_company_at_visit') is-invalid @enderror"
                                                    id="insurance_company_at_visit"
                                                    wire:model="insurance_company_at_visit">
                                                @error('insurance_company_at_visit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>  --}}
                                
                            {{-- <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="diagnosis" class="form-label">Diagnosis</label>
                                        <textarea class="form-control" id="diagnosis" wire:model="diagnosis" rows="3"></textarea>
                                    </div>
                                </div>
                                
                            </div> --}}
 
                            <div class="mb-3">
                                <label for="reservationCode" class="form-label">Kode Reservasi</label>
                                <input type="text" 
                                    class="form-control"
                                    id="reservationCode"
                                    wire:model="reservationCode"
                                    placeholder="Optional">
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-2"></i> Simpan Registrasi
                                </button>
                                <button type="button" class="btn btn-secondary" wire:click="deselectPatient">
                                    <i class="fa fa-times me-2"></i> Batal
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info">
                            Silakan cari dan pilih pasien untuk memulai registrasi.
                        </div>
                    @endif
 
                    <div class="mt-4">
                        <h5>Daftar Registrasi Pasien Tanggal {{ now()->format('d/m/Y') }}</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr> 
                                    <th>No Visit</th>
                                    <th>Nama Pasien</th>
                                    <th>Poli</th>
                                    <th>Dokter</th>
                                    <th>Keluhan</th>
                                    <th>Payment</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody> 
                                 @foreach ($registrations as $r)
                                <tr> 
                                    <td>{{ $r->visit_no ?? '-'}}</td>
                                    <td>{{ $r->patient->patient_name ?? '-'}}</td>
                                    <td>{{ $r->poli ?? '-' }}</td>
                                    <td>{{ $r->doctor->doctor_name ?? '-' }}</td>
                                    <td>{{ $r->complaint ?? '-' }}</td>
                                    <td>{{ $r->payment_type ?? '-' }}</td>
                                    <td> 
                                        <button class="btn btn-info btn-sm" wire:click="openEditModal({{ $r->id }})"><i class="ti ti-edit-circle"></i></button>
 
                                        <button class="btn btn-danger btn-sm" wire:click="deleteConfirm({{ $r->id }})"><i class="ti ti-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div wire:ignore.self id="registrationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="registrationModalLabel"
        aria-hidden="true">
    
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
    
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $isEdit ? 'Update Admission - ' . $visitNo : '' }}
                    </h5>
    
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
    
                <div class="modal-body">
                    <form wire:submit.prevent="save">
    
                       <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3" id="datetimepicker1">
                                <label for="visitDate" class="form-label">Tanggal Kunjungan *</label>
                                <input type="date" class="form-control @error('visitDate') is-invalid @enderror" id="visitDate"
                                    wire:model="visitDate" readonly>
                                @error('visitDate') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="visitTime" class="form-label">Waktu Kunjungan *</label>
                                <input type="time" class="form-control @error('visitTime') is-invalid @enderror" id="visitTime"
                                    wire:model="visitTime"readonly>
                                @error('visitTime') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="poli" class="form-label">Poli *</label>
                                <select class="form-select @error('poli') is-invalid @enderror" id="poli" wire:model="poli"readonly>
                                    <option value="POLI-UMUM" selected>POLI UMUM</option>
                                    <option value="POLI-ANAK">POLI ANAK</option>
                                    <option value="POLI-GIGI">POLI GIGI</option>
                                    <option value="POLI-OBGYN">POLI OBGYN</option>
                                </select>
                                @error('poli') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="doctorCode" class="form-label">Dokter *</label>
                                <input type="text" class="form-control @error('doctorCode') is-invalid @enderror" id="doctorCode"
                                    wire:model="doctorCode">
                            </div>
                        </div>
                    </div>
                    
                   <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="visitType" class="form-label">Jenis Kunjungan *</label>
                            <select class="form-select @error('visitType') is-invalid @enderror" id="visitType" wire:model="visitType">
                                <option value="POLI">POLI</option>
                                <option value="IGD">IGD</option>
                            </select>
                            @error('visitType') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="paymentType" class="form-label">Tipe Pembayaran *</label>
                            <select class="form-select @error('paymentType') is-invalid @enderror" id="paymentType"
                                wire:model="paymentType"> 
                                <option value="PRIBADI">PRIBADI</option>
                                <option value="CLAIM">CLAIM</option>
                                {{-- <option value="ASURANSI">ASURANSI</option> --}}
                            </select>
                            @error('paymentType') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="complaint" class="form-label">Keluhan</label>
                            <select class="form-select @error('complaint') is-invalid @enderror" id="complaint" wire:model="complaint">
                                <option value="">-- Pilih Keluhan --</option>
                                <option value="Periksa Kehamilan">Periksa Kehamilan</option>
                                <option value="USG">USG</option>
                                <option value="Papsmear">Papsmear</option>
                                <option value="Vaksin">Vaksin</option>
                                <option value="Konsultasi Reproduksi">Konsultasi Reproduksi</option>
                                {{-- <option value="ASURANSI">ASURANSI</option> --}}
                            </select>
                            @error('complaint') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            {{-- <textarea class="form-control @error('complaint') is-invalid @enderror" id="complaint"
                                wire:model="complaint" rows="3"></textarea>
                            @error('complaint') <div class="invalid-feedback">{{ $message }}</div> @enderror --}}
                        </div>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="insurance_number_at_visit" class="form-label">Nomor Asuransi *</label>
                            <input type="text" class="form-control @error('insurance_number_at_visit') is-invalid @enderror"
                                id="insurance_number_at_visit" wire:model="insurance_number_at_visit">
                            @error('insurance_number_at_visit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="insurance_company_at_visit" class="form-label">Nama Perusahaan Asuransi *</label>
                            <input type="text" class="form-control @error('insurance_company_at_visit') is-invalid @enderror"
                                id="insurance_company_at_visit" wire:model="insurance_company_at_visit">
                            @error('insurance_company_at_visit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div> --}}
                
                {{-- <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="diagnosis" class="form-label">Diagnosis</label>
                            <textarea class="form-control" id="diagnosis" wire:model="diagnosis" rows="3"></textarea>
                        </div>
                    </div>
                
                </div> --}}
                    
                    <div class="mb-3">
                        <label for="reservationCode" class="form-label">Kode Reservasi</label>
                        <input type="text" class="form-control" id="reservationCode" wire:model="reservationCode" placeholder="Optional">
                    </div>
    
                        <button class="btn btn-primary mt-3">Simpan</button>
                    </form>
                </div>
    
            </div>
        </div>
    </div>
    {{-- END MODAL --}}

    {{-- PATIENT MODAL --}}
    <div wire:ignore.self id="PatientModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
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
                                <input type="text" class="form-control" wire:model="patientNik">
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
                                <label>Email</label>
                                <input type="email" class="form-control" wire:model="patientEmail">
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>HP/WA</label>
                                <input type="text" class="form-control" wire:model="patientPhone">
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
                                <input type="text" class="form-control" wire:model="patientReligion">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Pekerjaan</label>
                                <input type="text" class="form-control" wire:model="patientJob">
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Status</label>
                                <input type="text" class="form-control" wire:model="patientStatus">
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
                            <div class="col-md-6 mb-3">
                                <label>Riwayat Khusus</label>
                                <input type="text" class="form-control" wire:model="patientSpecial">
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

    <script> 
        let patientModal = null;
        let modalUser;

        window.addEventListener('show-form', () => {
            const modalEl = document.getElementById('registrationModal');
            
            if (!modalUser) {
            modalUser = new bootstrap.Modal(modalEl);
            }
            modalUser.show();
        });
        window.addEventListener('hide-form', () => {
            if (modalUser) modalUser.hide();
            document.querySelectorAll('.modal-backdrop').forEach(e => e.remove());
        });
        
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

        window.addEventListener('confirm-delete-admission', () => {
            if (confirm("Hapus data?")) {
                @this.call('deleteAdmission');
            }
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
