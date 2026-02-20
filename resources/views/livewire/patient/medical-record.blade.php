<div> 
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body pc-component">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari Nama Pasien atau Nomor Rekam Medis"
                                wire:model.live="searchQuery" wire:keydown.enter="search">
                            <button class="btn btn-outline-secondary" type="button" wire:click="search"><i
                                    class="ti ti-search"></i></button>
                        </div>
                    </div>
                    @if ($searchResults !== null)
                        <div class="mt-4">
                            <h5>{{ count($searchResults) }} pasien ditemukan.</h5>
                            @if (count($searchResults) > 0)
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>MR Code</th>
                                            <th>Nama</th>
                                            <th>Tanggal Lahir</th>
                                            <th>Alamat</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($searchResults as $patient)
                                            <tr wire:key="patient-{{ $patient->id }}">
                                                <td>{{ $patient->mr_code }}</td>
                                                <td>{{ $patient->patient_name }}</td>
                                                <td>{{ $patient->patient_dob }}</td>
                                                <td>{{ $patient->patient_address }}</td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm"
                                                        wire:click="openDetailPatient({{ $patient->mr_code }})">Lihat
                                                        Detail</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="alert alert-info">Tidak ada pasien ditemukan.</div>
                            @endif 
                        </div>
                    @endif
                </div> 
            </div>
        </div>
    </div>
</div>
