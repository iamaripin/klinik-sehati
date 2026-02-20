<div>
    <div class="card mb-4">
        <div class="card-body">
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
                            <th>Pembayaran</th>
                            <th></th>
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
                                <button class="btn btn-info btn-sm" wire:click="createPayment({{ $r->id }})"><i class="ti ti-page-break"></i> Proses Pasien</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
 
   <x-cw-modal id="paymentModal" title="Halaman Pembayaran : {{ $patient_name }}" width="800" height="600"
        position="center"> 
        <table class="table table-sm table-bordered mb-1">
            <tr>
                <td width="30%" style="background-color: #83b2fc"><b>Nama Pasien</b></td>
                <td style="background-color: #c0d7fc">{{ $patient_name }}</td>
            </tr>
            <tr>
                <td style="background-color: #83b2fc"><b>Nomor MR</b></td>
                <td style="background-color: #c0d7fc">{{ $mr_code }} | {{ $visit_no }}</td>
            </tr>
            <tr>
                <td style="background-color: #83b2fc"><b>Dokter</b></td>
                <td style="background-color: #c0d7fc">{{ $doctor ?? '-' }}</td>
            </tr> 
        </table>
        @if(!$showBill) 
                <table class="table table-sm">
                    <tr>
                        <td class="text-end">Subtotal Tagihan</td>
                        <td class="text-end" width="20%">Rp. {{ number_format($total_tagihan_awal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-end">Diskon Dokter</td>
                        <td class="text-end">Rp. {{ number_format($amount_discount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-end">Pajak</td>
                        <td class="text-end">Rp. 0</td>
                    </tr>
                    <tr>
                        <td class="text-end"><b>Total Tagihan</b></td>
                        <td class="text-end"><b>Rp. {{ number_format($total_tagihan_final, 0, ',', '.') }}</b></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end">
                            <button type="button" class="btn btn-primary" wire:click="createBillingRecord()">
                                <i class="ti ti-receipt-2"></i> Buat Billing</button>
                        </td>
                    </tr>
                </table> 
        @endif
        @if($showBill && $bill) 
        <table class="table table-sm">
            <tr>
                <td colspan="5" style="background-color: #83b2fc"><b>Rincian Tagihan</b></td>
            </tr>
            <tr>
                <td style="background-color: #83b2fc">Bill No</td>
                <td style="background-color: #83b2fc">Tipe Pembayaran</td>
                <td style="background-color: #83b2fc">Nomor Referensi</td>
                <td style="background-color: #83b2fc"></td>
            </tr>
            <tr>
                <td>{{ $bill->bill_no ?? '-' }}</td>
                <td>
                    <select id="payment_method" wire:model="payment_method" class="form-control form-control-sm @error('payment_method') is-invalid @enderror">
                        <option value="">Pilih Metode Pembayaran</option>
                        <option value="cash">Tunai</option>
                        <option value="transfer">Transfer BCA</option>
                    </select>
                    @error('payment_method') <small class="text-danger">{{ $message }}</small>@enderror
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm @error('reference_number') is-invalid @enderror" placeholder="Nomor Referensi (jika ada)" wire:model="reference_number">
                    @error('reference_number') <small class="text-danger">{{ $message }}</small>@enderror
                </td>
                <td>
                    @if($bill && !$isPayment)
                        <button class="btn btn-success btn-sm" wire:click="proceedToPayment()"><i class="ti ti-clipboard-check"></i> Lanjut ke Pembayaran</button>
                    @elseif ($bill && $isPayment)
                    <a href="{{ route('invoice.pdf', $bill->id) }}" class="btn btn-sm btn-primary" target="_blank">
                        Print PDF
                    </a> 
                    @endif
                </td>
            </tr>
        </table>
        <table class="table table-bordered table-sm">
           <tr>
                    <td>Item</td>
                    <td>Qty</td>
                    <td>Harga</td>
                    <td>Subtotal</td>
            </tr>
            @foreach($bill->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ number_format($item->price) }}</td>
                    <td>{{ number_format($item->subtotal) }}</td>
                </tr>
                @endforeach 
            <tr>
                <td colspan="3" class="text-end">Subtotal Tagihan</td> 
                <td>Rp. {{ number_format($total_tagihan_awal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-end">Diskon Dokter</td> 
                <td>Rp. {{ number_format($amount_discount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-end">Pajak</td> 
                <td>Rp. 0</td>
            </tr>
            <tr>
                <td colspan="3" class="text-end"><b>Total Tagihan</b></td> 
                <td><b>Rp. {{ number_format($total_tagihan_final, 0, ',', '.') }}</b></td>
            </tr>
        </table>
        @endif 
    </x-cw-modal>
</div>


<script>
    
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