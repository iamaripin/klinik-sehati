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
                                    wire:click="openDiagnosaMdl({{ $r->id }})" @class([ 'table-danger'=>
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
        <x-cw-modal id="discountMdl" title="Diskon Pasien" width="400" height="200" position="center">
            <form>
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <h6>Total Tagihan Sementara : Rp {{ number_format($this->total_tagihan, 0, ',', '.') }}</h6>
                        <input type="text" class="form-control form-control-sm @error('amount_discount') is-invalid @enderror" id="amount_discount"
                            wire:model="amount_discount" autocomplete="off" placeholder="Masukkan jumlah diskon...">
                        @error('amount_discount') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-success" wire:click="createPatientDiscount"><i class="ti ti-currency-dollar"></i> Buat Diskon</button>
                        <button type="button" class="btn btn-danger" wire:click="resetPatientDiscount"><i class="ti ti-refresh"></i> Reset Diskon</button>
                    </div>
                </div>
            </form>
        </x-cw-modal>
        <x-cw-modal id="diagnosaMdl" title="Diagnosa Dokter" width="850" height="600" position="top-left">
            <form> 
                               
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
                            <button type="button" class="btn btn-primary" wire:click="setDiscount()"><i class="ti ti-discount-2"></i> Diskon</button>
                            <button type="button" class="btn btn-primary" wire:click="loadSavedOrders()"><i class="ti ti-vaccine"></i> Obat/Resep</button>
                            <button type="button" class="btn btn-success" wire:click="save"><i class="ti ti-device-floppy"></i> Update Data</button>
                            <button type="button" class="btn btn-warning" wire:click="closeConsultation()"><i class="ti ti-clipboard-check"></i> Tutup Konsultasi</button>
                        </td>
                    </tr>
                </table>

                <div class="row"> 
                    <div class="col-md-6 mb-2">
                        <label for="anamnesa_dokter" class="form-label"><b>Anamnesa Dokter</b></label>
                        <textarea class="form-control form-control-sm @error('anamnesa_dokter') is-invalid @enderror" id="anamnesa_dokter"
                            wire:model="anamnesa_dokter" rows="3"></textarea>
                        @error('anamnesa_dokter') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="pemeriksaan_fisik" class="form-label"><b>Pemeriksaan Fisik</b></label>
                        <textarea class="form-control form-control-sm @error('pemeriksaan_fisik') is-invalid @enderror" id="pemeriksaan_fisik"
                            wire:model="pemeriksaan_fisik" rows="3">
                        {{ $weight_kg }}
                        </textarea>
                        @error('pemeriksaan_fisik') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="diagnosa" class="form-label"><b>Diagnosa</b></label>
                        <textarea class="form-control form-control-sm @error('diagnosa') is-invalid @enderror" id="diagnosa"
                            wire:model="diagnosa" rows="3"></textarea>
                        @error('diagnosa') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-6 mb-2">
                        <label for="tindakan_icd" class="form-label"><b>Tindakan</b></label>
                        <textarea class="form-control form-control-sm @error('tindakan_icd') is-invalid @enderror" id="tindakan_icd"
                            wire:model="tindakan_icd" rows="3"></textarea>
                        @error('tindakan_icd') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="diagnosa_icd" class="form-label"><b>Diagnosa ICD</b></label>
                        <input type="text" class="form-control form-control-sm @error('diagnosa_icd') is-invalid @enderror" 
                            id="diagnosa_icd" wire:model="diagnosa_icd" autocomplete="off" 
                            list="icd10_list" placeholder="Cari atau pilih ICD10...">
                        @error('diagnosa_icd') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="rencana_tindak_lanjut" class="form-label"><b>Rencana Tindak Lanjut</b></label>
                        <input type="text" class="form-control form-control-sm @error('rencana_tindak_lanjut') is-invalid @enderror" id="rencana_tindak_lanjut"
                            wire:model="rencana_tindak_lanjut" placeholder="Rencana Tindak Lanjut">
                        @error('rencana_tindak_lanjut') <small class="text-danger">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-4 mb-2">
                        <label for="kontrol_kembali" class="form-label"><b>Kontrol Kembali</b></label>
                        <input type="text" class="form-control form-control-sm @error('kontrol_kembali') is-invalid @enderror" id="kontrol_kembali"
                            wire:model="kontrol_kembali" placeholder="Kontrol Kembali">
                        @error('kontrol_kembali') <small class="text-danger">{{ $message }}</small>@enderror
                    </div> 
                </div>
 
              </form>
        </x-cw-modal>

        <x-cw-modal id="riwayatMdl" title="Riwayat Pasien" width="400" height="600" position="top-right">
            @if(!empty($history))

            @foreach($history as $h)
            <table class="table table-sm table-bordered mb-3"> 
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
                </tr>
                <tr>
                    <td style="background-color: #83b2fc"><b>Keluhan</b></td>
                    <td style="background-color: #c0d7fc">{{ $h['complaint'] }}</td>
                </tr>
            </table>
            <table class="table table-sm table-bordered mb-3">
                <tr>
                    <td colspan="4" style="background-color: #619cfa"><b>Vital Sign</b></td>
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
                    <td style="background-color: #c0d7fc">{{ $h['bp_systolic'] ?? '-' }}/{{ $h['bp_diastolic'] ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="background-color: #83b2fc"><b>Catatan Perawat</b></td>
                    <td colspan="3" style="background-color: #c0d7fc">{{ $h['anamnesa'] ?? '-' }}</td>
                </tr>
                </tbody>
            </table>
            <table class="table table-sm table-bordered mb-3">
                <tr>
                    <td colspan="4" style="background-color: #619cfa"><b>Diagnosa Dokter</b></td>
                </tr>
                <tr>
                    <td width="40%" style="background-color: #83b2fc"><b>Keluhan Utama</b></td>
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
            </table><hr>
            @endforeach
            @else
            <div class="text-muted">Tidak ada riwayat kunjungan</div>
            @endif
        </x-cw-modal>

        <x-cw-modal id="patientOrderMdl" title="Input Obat/Resep" width="900" height="600" position="center">
            <div class="mb-0"> 
                <div class="input-group">
                    <input type="text" class="form-control form-control-sm" 
                        wire:model.live="searchItemsInput" 
                        placeholder="Ketik nama atau kode item...">
                </div>

                {{-- Search Results Dropdown --}}
                @if(!empty($searchItemsResults))
                <div class="list-group mt-2 mb-2" style="position: relative; z-index: 10;">
                    @foreach($searchItemsResults as $item)
                    <button type="button" class="list-group-item list-group-item-action text-start cursor-pointer small"
                        wire:click="selectItem({{ $item['id'] }})">
                        <div><strong>{{ $item['name'] }}</strong> ({{ $item['code'] }})</div>
                        <small class="text-muted">{{ $item['generic_name'] }} | Stok: {{ $item['stock'] }} {{ $item['unit'] }} | Rp. {{ number_format($item['price'], 0, ',', '.') }}</small>
                    </button>
                    @endforeach
                </div>
                @endif

                {{-- Selected Item Info --}}
                @if($selectedItemId)
                <div class="alert alert-info mt-2 mb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Item:</strong> {{ $selectedItemName }}</p>
                            <p class="mb-0"><strong>Harga:</strong> Rp. {{ number_format($selectedItemPrice, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <input type="number" min="1" class="form-control" 
                                    wire:model.live="selectedItemQty" placeholder="Qty">
                                <button type="button" class="btn btn-primary" 
                                    wire:click="addOrderItem()"><i class="ti ti-clipboard-check"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            {{-- Order Items Table --}}
            <div class="table-responsive">
                <table class="table table-sm table-bordered mb-3">
                    <thead class="table-light">
                        <tr>
                            <td style="background-color: #83b2fc" width="40%">Nama Item</th>
                            <td style="background-color: #83b2fc" width="15%" class="text-center">Qty</th>
                            <td style="background-color: #83b2fc" width="15%" class="text-end">Harga</th>
                            <td style="background-color: #83b2fc" width="15%" class="text-end">Sub Total</th>
                            <td style="background-color: #83b2fc" width="10%" class="text-center">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orderItems as $index => $item)
                        <tr>
                            <td>{{ $item['item_name'] }}</td>
                            <td>
                                <input type="number" min="1" class="form-control form-control-sm text-center"
                                    value="{{ $item['qty'] }}" wire:change="updateOrderItemQty({{ $index }}, $event.target.value)">
                            </td>
                            <td class="text-end">Rp. {{ number_format($item['price'], 0, ',', '.') }}</td>
                            <td class="text-end"><strong>Rp. {{ number_format($item['subtotal'], 0, ',', '.') }}</strong></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger" wire:click="removeOrderItem({{ $index }})"><i
                                        class="ti ti-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                Belum ada item. Cari dan tambahkan item terlebih dahulu.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Total and Save --}}
            <div class="row">
                <div class="col-md-8"></div>
                <div class="col-md-4">
                    <div class="alert alert-success mb-2">
                        <div class="row">
                            <div class="col-6">
                                <strong>Total:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <h5 class="mb-0">Rp. {{ number_format($this->getOrderTotal(), 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success w-100" wire:click="savePatientOrder()"><i class="ti ti-checks"></i>
                        Simpan Data</button>
                </div>
            </div>

            {{-- Saved Orders Section --}}
            @if(!empty($savedOrders))
            <div class="mb-4">
                <h6 class="mb-2"><i class="ti ti-history"></i> <b>Data yang Telah Disimpan</b></h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-2">
                        <thead class="table-light">
                            <tr> 
                                <td style="background-color: #83b2fc" width="8%" class="text-center">Status</td>
                                <td style="background-color: #83b2fc" width="12%">Tgl Pesanan</td>
                                <td style="background-color: #83b2fc" width="48%">Item</td>
                                <td style="background-color: #83b2fc" width="12%" class="text-end">Total</td> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($savedOrders as $order)
                            <tr @class(['table-danger' => $order['status'] == 'cancelled'])>
                                <td class="text-center">
                                    @if($order['status'] == 'cancelled')
                                    <span class="badge bg-danger">Dibatalkan</span>
                                    @else
                                    @if($order['status'] != 'cancelled')
                                    <button type="button" class="btn btn-sm btn-danger" wire:click="cancelPatientOrder({{ $order['id'] }})"
                                        onclick="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                        <i class="ti ti-repeat"></i>
                                    </button>
                                    @else
                                    <span class="text-muted small">-</span>
                                    @endif
                                    @endif
                                </td>
                                <td>{{ $order['order_date'] }}</td>
                                <td>
                                    @foreach($order['details'] as $detail)
                                        <div class="small">
                                            {{ $detail['item_name'] }} ({{ $detail['qty'] }} x Rp. {{ number_format($detail['sell_price'], 0, ',', '.') }})
                                        </div>
                                    @endforeach
                                </td>
                                <td class="text-end"><strong>Rp. {{ number_format($order['total_amount'], 0, ',', '.') }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="background-color: #83b2fc" colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                                <td style="background-color: #83b2fc" class="text-end"><strong>Rp. {{ number_format($this->getSavedOrdersGrandTotal(), 0, ',', '.') }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <hr>
            </div>
            @endif
            
        </x-cw-modal>

    </div>

    {{-- JAVASCRIPT --}}
    <script>
    
        document.addEventListener('livewire:init', function () {
            document.getElementById("amount_discount")
            .addEventListener("input", function () {
            
            let raw = this.value.replace(/\D/g, "");
            
            // update Livewire value (angka bersih)
            @this.set('amount_discount', raw);
            
            // format tampilan
            this.value = raw
            ? new Intl.NumberFormat("id-ID").format(raw)
            : "";
            });
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