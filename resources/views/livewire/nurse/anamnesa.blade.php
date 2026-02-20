<div>
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body pc-component">
                
                <div wire:poll.10s.keep-alive>
                
                    {{-- SEARCH --}}
                    <div class="mb-2">
                        <input type="text" wire:model.live="search" class="form-control form-control-sm"
                            placeholder="Search MR / Visit / Nama Pasien...">
                    </div>
                
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle text-nowrap small">
                            <thead class="table-light">
                                <tr>
                                    <th>MR Code</th>
                                    <th>No Visit</th>
                                    <th>Pasien</th>
                                    <th>Tgl Lahir</th>
                                    <th>Usia</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Keluhan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($registrations as $r)
                                <tr style="cursor:pointer" wire:key="row-{{ $r->id }}"
                                    wire:click="openAnamnesaMdl({{ $r->id }})" @class([ 'table-danger'=>
                                    str_ends_with($r->visit_no, '001'),
                                    'table-warning' => $r->status == 'REGISTERED',
                                    'table-success' => $r->status == 'FINISHED',
                                    ])
                                    >
                                    <td>{{ $r->mr_code }}</td>
                                    <td>{{ $r->visit_no }}</td>
                                    <td>{{ $r->patient->name ?? '-' }}</td>
                                    <td>{{ $r->patient->birth_date ?? '-' }}</td>
                                    <td>
                                        @if($r->patient?->birth_date)
                                        {{ \Carbon\Carbon::parse($r->patient->birth_date)->age }} th
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td>{{ $r->patient->gender ?? '-' }}</td>
                                    <td>{{ $r->complaint ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        Tidak ada data hari ini
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                
                </div> 
            </div>
        </div>
    </div>

    <div>
     {{-- <button onclick="CWModalDOM.open('anamnesaMdl')" class="btn btn-primary">
        Buka Modal A
    </button>
    
    <button onclick="CWModalDOM.open('riwayatMdl')" class="btn btn-success">
        Buka Modal B
    </button>
     --}}
    
    <x-cw-modal id="anamnesaMdl" title="Anamnesa Perawat" width="650" height="600" position="top-left">
        <form >
            <table class="table table-sm table-bordered mb-3">
                <tr>
                    <td width="30%" style="background-color: #83b2fc"><b>Nama Pasien</b></td>
                    <td style="background-color: #c0d7fc">{{ $patient_name }}</td>
                </tr>
                <tr>
                    <td style="background-color: #83b2fc"><b>Nomor MR</b></td>
                    <td style="background-color: #c0d7fc">{{ $mr_code }} | {{ $visit_no }}</td>
                </tr>
                <tr>
                    <td style="background-color: #83b2fc"><b>Tanggal Lahir</b></td>
                    <td style="background-color: #c0d7fc">{{ $patient_dob }} ({{ $umur }})</td>
                </tr>
                <tr>
                    <td style="background-color: #83b2fc"><b>Keluhan</b></td>
                    <td style="background-color: #c0d7fc">{{ $complaint }}</td>
                </tr>
            </table>
            <table class="table table-sm mb-1">
                <tr>
                    <td class="text-end">
                        <button type="button" class="btn btn-danger" wire:click="resetQueueStatus()">
                        <i class="ti ti-bell-off"></i> Batalkan Panggil Antrian</button>
                        <button type="button" class="btn btn-warning" wire:click="callPatient()">
                            <i class="ti ti-bell-ringing"></i> Panggil Pasien</button>
                       <button type="button" class="btn btn-success" wire:click="save"><i class="ti ti-device-floppy"></i> Update
                            Data</button> 
                    </td>
                </tr>
            </table>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label for="weight_kg" class="form-label">Berat Badan</label>
                            <input type="text" class="form-control form-control-sm @error('weight_kg') is-invalid @enderror" id="weight_kg"
                                wire:model="weight_kg" placeholder="kg">
                            @error('weight_kg') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="height_cm" class="form-label">Tinggi Badan</label>
                            <input type="text" class="form-control form-control-sm @error('height_cm') is-invalid @enderror" id="height_cm"
                                wire:model="height_cm" placeholder="cm">
                            @error('height_cm') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="bmi" class="form-label">BMI</label>
                            <input type="text" class="form-control form-control-sm @error('bmi') is-invalid @enderror" id="bmi" wire:model="bmi"
                                readonly>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label for="temperature" class="form-label">Suhu</label>
                            <input type="text" class="form-control form-control-sm @error('temperature') is-invalid @enderror" id="temperature"
                                wire:model="temperature" placeholder="&deg;C">
                            @error('temperature') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="bp_systolic" class="form-label">Tekanan Darah</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm @error('bp_systolic') is-invalid @enderror" id="bp_systolic"
                                    wire:model="bp_systolic" placeholder="Systolic">
                                <span class="input-group-text">/</span>
                                <input type="text" class="form-control form-control-sm @error('bp_diastolic') is-invalid @enderror" id="bp_diastolic"
                                    wire:model="bp_diastolic" placeholder="Diastolic">
                            </div>
                            @error('bp_systolic') <small class="text-danger">{{ $message }}</small>@enderror
                            @error('bp_diastolic') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="col-md-12 mb-2">
                            <label for="anamnesa" class="form-label">Catatan Perawat</label>
                            <textarea class="form-control form-control-sm @error('anamnesa') is-invalid @enderror" id="anamnesa" wire:model="anamnesa" rows="3"></textarea>
                            @error('anamnesa') <small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                 </form>
    </x-cw-modal>
    
    
    <x-cw-modal id="riwayatMdl" title="Riwayat Pasien" width="400" height="600" position="top-right">
             @if(!empty($history)) 
                    @foreach($history as $h)
                    <table class="table table-sm table-bordered mb-3">
                        <tr>
                            <td width="40%" style="background-color: #83b2fc"><b>Nama Pasien</b></td>
                            <td style="background-color: #c0d7fc">{{ $h['patient_name'] }}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #83b2fc"><b>Nomor Kunjungan</b></td>
                            <td style="background-color: #c0d7fc">{{ $h['visit_no'] }}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #83b2fc"><b>Tanggal</b></td>
                            <td style="background-color: #c0d7fc">{{ $h['visit_date'] }} {{ $h['visit_time'] ?? '' }}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #83b2fc"><b>Dokter</b></td>
                            <td style="background-color: #c0d7fc">{{ $h['doctor'] }}</td>
                        </tr><tr>
                            <td style="background-color: #83b2fc"><b>Keluhan</b></td>
                            <td style="background-color: #c0d7fc">{{ $h['complaint'] }}</td>
                        </tr>
                    </table>
                    <table class="table table-sm table-bordered mb-3">
                            <tr>
                                 <td colspan="4" style="background-color: #619cfa"><b>Catatan Perawat</b></td>
                            </tr>
                            <tr>
                                <td style="background-color: #83b2fc"><b>Berat</b></td>
                                <td style="background-color: #c0d7fc">{{ $h['weight_kg'] ?? '-' }} kg</td>
                                <td style="background-color: #83b2fc"><b>Tinggi</b></td>
                                <td style="background-color: #c0d7fc">{{ $h['height_cm'] ?? '-' }} cm</td>
                            </tr>
                            <tr>
                                <td style="background-color: #83b2fc"><b>Suhu</b></td>
                                <td style="background-color: #c0d7fc">{{ $h['temperature'] ?? '-' }} &deg;</td>
                                <td style="background-color: #83b2fc"><b>Tekanan Darah</b></td>
                                <td style="background-color: #c0d7fc">{{ $h['bp_systolic'] ?? '-' }}/{{ $h['bp_diastolic'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="background-color: #83b2fc"><b>Catatan Perawat</b></td>
                                <td colspan="3" style="background-color: #c0d7fc">{{ $h['anamnesa'] ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-sm table-bordered mb-3">
                        <tr>
                            <td colspan="4" style="background-color: #619cfa"><b>Catatan Dokter</b></td>
                        </tr><tr>
                            <td width="40%"style="background-color: #83b2fc"><b>Keluhan Utama</b></td>
                            <td style="background-color: #c0d7fc">{{ $h['complaint'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #83b2fc"><b>Diagnosa</b></td>
                            <td style="background-color: #c0d7fc">{{ $h['diagnosis'] ?? '-' }}</td> 
                        </tr>
                        <tr>
                            <td style="background-color: #83b2fc"><b>Tindakan</b></td>
                            <td style="background-color: #c0d7fc">{{ $h['treatment'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #83b2fc"><b>Pemakaian Obat</b></td>
                            <td style="background-color: #c0d7fc">{{ $h['medication'] ?? '-' }}</td>
                        </tr>
                        </tbody>
                    </table>
                    <hr>
                    @endforeach 
            @else
                <div class="text-muted">Tidak ada riwayat kunjungan</div>
            @endif
    </x-cw-modal>
 
    
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        let anamnesaMdl = null;
        let dataTable = null;
 
        document.addEventListener('livewire:init', function () {

           Livewire.on('openAnamnesaMdl', (event) => {
            openAnamnesaMdl(event.id);
            });
 
        }); 
        function initDataTable() {
            $('#patientTable').DataTable({
                responsive: true,
                destroy: true,
                pageLength: 10
            });
        }
 
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