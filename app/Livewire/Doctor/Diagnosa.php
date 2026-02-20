<?php

namespace App\Livewire\Doctor;

use Livewire\Component;
use Carbon\Carbon; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use App\Models\User as UserModel;
use App\Models\Doctor;
use App\Models\Nurse;
use App\Models\Diagnosa as DiagnosaModel;
use App\Models\Admission;
use App\Models\GeneralQueue;
use App\Models\Icd10;
use App\Models\PatientOrder;
use App\Models\PatientOrderDetail;
use App\Models\Discount;
use App\Models\InventoryItems;
use App\Models\InventoryBatch;
use App\Models\InventoryStockMovement;
use App\Models\Payment as PaymentModel; 

class Diagnosa extends Component
{
    public $users = [];

    public $adm_id;
    public $diagnosa_id;
    public $patient_name;
    public $patient_dob;
    public $umur;
    public $complaint;
    public $mr_code;
    public $visit_no;
    public $amount_discount;
    public $total_tagihan;

    public $anamnesa_dokter;
    public $pemeriksaan_fisik; 
    public $diagnosa;
    public $diagnosa_icd;
    public $tindakan_icd;
    public $rencana_tindak_lanjut;
    public $kontrol_kembali;
    public $recorded_by;

    public $nurse_id;
    public $bp_systolic;
    public $bp_diastolic;
    public $temperature;
    public $weight_kg;
    public $height_cm;
    public $bmi;
    public $anamnesa;

    public $isEdit = false;
    public $history = [];
    public $icd10List = [];
    // protected $listeners = ['openCreateModal', 'openEditModal'];

    public $search = '';

    // Patient Order Properties
    public $searchItemsInput = '';
    public $searchItemsResults = [];
    public $orderItems = [];
    public $selectedItemId = null;
    public $selectedItemName = '';
    public $selectedItemPrice = 0;
    public $selectedItemQty = 1;
    public $savedOrders = [];

    public $isPayment = false;

    protected $queryString = [
        'search' => ['except' => '']
    ];

    public function render()
    {
        $registrations = Admission::with(['patient', 'doctor', 'generalQueue'])
            ->where('visit_date', now()->format('Y-m-d'))
            ->when($this->search, function ($q) {
                $q->where('mr_code', 'like', "%{$this->search}%")
                    ->orWhere('visit_no', 'like', "%{$this->search}%")
                    ->orWhereHas('patient', function ($p) {
                        $p->where('name', 'like', "%{$this->search}%");
                    });
            })
            ->latest()
            ->get();
 
        return view('livewire.doctor.diagnosa', [
            'doctors' => Doctor::orderBy('doctor_code')->get(),
            'registrations' => $registrations,
            'icd10List' => $this->icd10List,
        ])->layout('layouts.app', [
            'title' => 'Antrian Pasien Rawat Jalan',
        ]);
    } 

    function umurLengkap($tanggalLahir)
    {
        $dob = Carbon::parse($tanggalLahir);
        $now = Carbon::now();

        return $dob->diff($now)->format('%y tahun %m bulan %d hari');
    }

    public function openDiagnosaMdl($adm_id)
    {
        $this->resetForm();
        $this->isEdit = false;

        $adm = Admission::with(['patient', 'doctor', 'generalQueue'])
            ->where('id', $adm_id)
            ->first();

        $this->adm_id        = $adm->id;
        $this->patient_name   = $adm->patient->patient_name;
        $this->mr_code       = $adm->mr_code;
        $this->visit_no      = $adm->visit_no;
        $this->patient_dob    = $adm->patient->patient_dob->format('d-m-Y');
        $this->umur          = $this->umurLengkap($adm->patient->patient_dob);
        $this->complaint      = $adm->complaint;

        $admissions = Admission::with('doctor')
            ->where('mr_code', $adm->mr_code)
            ->orderBy('visit_date', 'desc')
            ->orderBy('visit_time', 'desc')
            ->get();

        $history = [];
        foreach ($admissions as $a) {
            $n = Nurse::where('mr_code', $a->mr_code)
                ->where('visit_no', $a->visit_no)
                ->orderBy('created_at', 'desc')
                ->first();

            $history[] = [
                'patient_name' => $a->patient->patient_name,
                'visit_date' => $a->visit_date ? $a->visit_date->format('d-m-Y') : null,
                'visit_time' => $a->visit_time ?? null,
                'doctor' => $a->doctor->doctor_prefix . ' ' . $a->doctor->doctor_name . ' ' . $a->doctor->doctor_suffix ?? ($a->doctor_code ?? '-'),
                'complaint' => $a->complaint ?? '-',

                'anamnesa_dokter' => $d->anamnesa_dokter ?? '-',
                'pemeriksaan_fisik' => $d->pemeriksaan_fisik ?? '-',
                'diagnosa' => $d->diagnosa ?? '-',
                'diagnosa_icd' => $d->diagnosa_icd ?? '-',
                'tindakan_icd' => $d->tindakan_icd ?? '-',
                'rencana_tindak_lanjut' => $d->rencana_tindak_lanjut ?? '-',
                'kontrol_kembali' => $d->kontrol_kembali ?? '-',

                'weight_kg' => $n->weight_kg ?? '-',
                'height_cm' => $n->height_cm ?? '-',
                'temperature' => $n->temperature ?? '-',
                'bp_systolic' => $n->bp_systolic ?? '-',
                'bp_diastolic' => $n->bp_diastolic ?? '-',
                'bmi' => $n->bmi ?? '-',
                'anamnesa' => $n->anamnesa ?? '-',
                'medication' => '-',
                'visit_no' => $a->visit_no,
            ];
        }

        $this->history = $history;

        $diagnosa = DiagnosaModel::where('mr_code', $adm->mr_code)->first();
        $nurse = Nurse::where('mr_code', $adm->mr_code)->first();
        $nurse_fisik = "Berat: " . ($nurse->weight_kg ?? '-') . " kg, Tinggi: " . ($nurse->height_cm ?? '-') . " cm, BMI: " . ($nurse->bmi ?? '-') . ", Tensi: " . ($nurse->bp_systolic ?? '-') . "/" . ($nurse->bp_diastolic ?? '-') . " mmHg, Temp: " . ($nurse->temperature ?? '-') . " °C";
        if ($diagnosa) {
            $this->isEdit = true;
            $this->diagnosa_id = $diagnosa->id;
            $this->anamnesa_dokter = $diagnosa->anamnesa_dokter;
            $this->pemeriksaan_fisik = empty($diagnosa->pemeriksaan_fisik) ? $nurse_fisik : $diagnosa->pemeriksaan_fisik;
            $this->diagnosa = $diagnosa->diagnosa;
            $this->diagnosa_icd = $diagnosa->diagnosa_icd;
            $this->tindakan_icd = $diagnosa->tindakan_icd;
            $this->rencana_tindak_lanjut = $diagnosa->rencana_tindak_lanjut;
            $this->kontrol_kembali = $diagnosa->kontrol_kembali ? Carbon::parse($diagnosa->kontrol_kembali)->format('Y-m-d') : null;
        }

        $payment = PaymentModel::where('visit_no', $this->visit_no)->exists();
        // dd($payment);
        if ($payment) {
            $this->isPayment = true;
            $this->dispatch('toastr:warning', message: 'Pembayaran sudah dilakukan untuk visit ini!');
        }

        // $this->dispatch('show-form');
        $this->dispatch('open-cw', 'riwayatMdl');
        $this->dispatch('open-cw', 'diagnosaMdl');
    }
 
    public function loadSavedOrders()
    {
        $this->dispatch('open-cw', 'patientOrderMdl');
        $this->savedOrders = PatientOrder::with(['details.item', 'details.batch'])
            ->where('mr_code', $this->mr_code)
            ->where('visit_no', $this->visit_no)
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'order_date' => $order->order_date->format('d-m-Y H:i'),
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'details' => $order->details->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'item_name' => $detail->item->item_name,
                            'qty' => $detail->qty,
                            'sell_price' => $detail->sell_price,
                            'subtotal' => $detail->subtotal,
                        ];
                    })->toArray(),
                ];
            })
            ->toArray();
    }

    public function save()
    {
        DB::beginTransaction();
        try {
            $rules = [
                'anamnesa_dokter' => 'required|string',
                'pemeriksaan_fisik' => 'required|string',
                'diagnosa' => 'required|string',
                'diagnosa_icd' => 'nullable|string|max:50',
                'tindakan_icd' => 'nullable|string|max:50',
                'rencana_tindak_lanjut' => 'nullable|string',
                'kontrol_kembali' => 'nullable|date',
            ];

            $this->validate($rules);
            DiagnosaModel::updateOrCreate(
                ['visit_no' => $this->visit_no],
                [
                    'admission_id' => $this->adm_id,
                    'mr_code' => $this->mr_code,
                    'visit_no' => $this->visit_no,
                    'recorded_by' => auth()->id(),
                    'recorded_at' => now(),

                    'anamnesa_dokter' => $this->anamnesa_dokter,
                    'pemeriksaan_fisik' => $this->pemeriksaan_fisik,
                    'diagnosa' => $this->diagnosa,
                    'diagnosa_icd' => $this->diagnosa_icd,
                    'tindakan_icd' => $this->tindakan_icd,
                    'rencana_tindak_lanjut' => $this->rencana_tindak_lanjut,
                    'kontrol_kembali' => $this->kontrol_kembali,
                ]
            );  
            GeneralQueue::where('mr_code', $this->mr_code)
                ->where('visit_no', $this->visit_no)
                ->update(['queue_status' => 'CALL']);
 
            $this->dispatch('hide-form');
            $this->dispatch('refresh-table');
            DB::commit();
            $this->dispatch('toastr:success', message: 'Data diagnosa berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating diagnosa record: ' . $e->getMessage());
            $this->dispatch('toastr:error', message: 'Terjadi kesalahan saat menyimpan data diagnosa!');
        }
        
    }

    protected function resetForm()
    {
        $this->reset([
            'diagnosa_id',
        ]);

        $this->isEdit = false;
    }

    public function updated($property)
    {
        if (in_array($property, ['weight_kg', 'height_cm'])) {
            $this->calculateBmi();
        }
        
        if ($property === 'searchItemsInput') {
            $this->searchInventoryItems();
        }
    }

    public function searchInventoryItems()
    {
        if (strlen($this->searchItemsInput) < 2) {
            $this->searchItemsResults = [];
            return;
        }

        $this->searchItemsResults = InventoryItems::where('is_active', true)
            ->where(function ($q) {
                $q->where('item_name', 'like', "%{$this->searchItemsInput}%")
                    ->orWhere('item_code', 'like', "%{$this->searchItemsInput}%")
                    ->orWhere('generic_name', 'like', "%{$this->searchItemsInput}%");
            })
            ->with('batches')
            ->get()
            ->map(function ($item) {
                $batch = $item->batches()->where('stock_qty', '>', 0)->first();
                return [
                    'id' => $item->id,
                    'code' => $item->item_code,
                    'name' => $item->item_name,
                    'generic_name' => $item->generic_name,
                    'unit' => $item->unit,
                    'batch_id' => $batch?->id,
                    'price' => $batch?->sell_price ?? 0,
                    'stock' => $batch?->stock_qty ?? 0,
                ];
            })
            ->toArray();
    }

    public function selectItem($itemId)
    {
        $item = InventoryItems::with('batches')->findOrFail($itemId);
        $batch = $item->batches()->where('stock_qty', '>', 0)->first();

        $this->selectedItemId = $itemId;
        $this->selectedItemName = $item->item_name;
        $this->selectedItemPrice = $batch?->sell_price ?? 0;
        $this->searchItemsInput = '';
        $this->searchItemsResults = [];
    }

    public function addOrderItem()
    {
        if (!$this->selectedItemId || $this->selectedItemQty <= 0) {
            $this->dispatch('toastr:warning', message: 'Pilih item dan masukkan qty terlebih dahulu');
            return;
        }

        $existingIndex = array_search($this->selectedItemId, array_column($this->orderItems, 'item_id'));

        if ($existingIndex !== false) {
            $this->orderItems[$existingIndex]['qty'] += $this->selectedItemQty;
            $this->orderItems[$existingIndex]['subtotal'] = 
                $this->orderItems[$existingIndex]['qty'] * $this->orderItems[$existingIndex]['price'];
        } else {
            $this->orderItems[] = [
                'item_id' => $this->selectedItemId,
                'item_name' => $this->selectedItemName,
                'qty' => $this->selectedItemQty,
                'price' => $this->selectedItemPrice,
                'subtotal' => $this->selectedItemQty * $this->selectedItemPrice,
            ];
        }

        $this->selectedItemId = null;
        $this->selectedItemName = '';
        $this->selectedItemPrice = 0;
        $this->selectedItemQty = 1;
    }

    public function removeOrderItem($index)
    {
        unset($this->orderItems[$index]);
        $this->orderItems = array_values($this->orderItems);
    }

    public function updateOrderItemQty($index, $qty)
    {
        if ($qty <= 0) {
            unset($this->orderItems[$index]);
            $this->orderItems = array_values($this->orderItems);
        } else {
            $this->orderItems[$index]['qty'] = $qty;
            $this->orderItems[$index]['subtotal'] = $qty * $this->orderItems[$index]['price'];
        }
    }

    public function getOrderTotal()
    {
        return array_sum(array_column($this->orderItems, 'subtotal'));
    }

    public function savePatientOrder()
    {
        if (empty($this->orderItems)) {
            $this->dispatch('toastr:warning', message: 'Tambahkan item terlebih dahulu');
            return;
        }

        try {
            DB::transaction(function () {
                $totalAmount = $this->getOrderTotal();

                // Create Order
                $order = PatientOrder::create([
                    'order_no' => 'ORD-' . date('YmdHis'),
                    'mr_code' => $this->mr_code,
                    'visit_no' => $this->visit_no,
                    'order_date' => now(),
                    'total_amount' => $totalAmount,
                    'status' => 'draft',
                    'user_id' => auth()->id(),
                ]);

                // Create Order Details & Update Stock
                foreach ($this->orderItems as $item) {
                    $batch = InventoryBatch::where('item_id', $item['item_id'])
                        ->where('stock_qty', '>', 0)
                        ->lockForUpdate()
                        ->first();

                    if (!$batch) {
                        throw new \Exception('Stok tidak tersedia untuk item: ' . $item['item_name']);
                    }

                    if ($batch->stock_qty < $item['qty']) {
                        throw new \Exception('Stok tidak cukup untuk item: ' . $item['item_name']);
                    }

                    // Create Order Detail
                    PatientOrderDetail::create([
                        'patient_order_id' => $order->id,
                        'item_id' => $item['item_id'],
                        'batch_id' => $batch->id,
                        'qty' => $item['qty'],
                        'sell_price' => $item['price'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    // Kurangi stock di batch
                    $batch->decrement('stock_qty', $item['qty']);

                    // Insert ke stock movements
                    InventoryStockMovement::create([
                        'item_id' => $item['item_id'],
                        'batch_id' => $batch->id,
                        'type' => 'OUT',
                        'qty' => $item['qty'],
                        'reference_number' => $order->order_no,
                        'notes' => 'Pesanan obat untuk pasien MR: ' . $this->mr_code . ' Visit: ' . $this->visit_no,
                        'user_id' => auth()->id(),
                    ]);
                }
            });

            $this->orderItems = [];
            $this->loadSavedOrders();
            $this->dispatch('toastr:success', message: 'Pesanan obat berhasil disimpan!');
        } catch (\Exception $e) {
            $this->dispatch('toastr:error', message: 'Gagal menyimpan pesanan: ' . $e->getMessage());
        }
    }
 
    public function getSavedOrdersGrandTotal()
    {
        $completedOrders = array_filter($this->savedOrders, function ($order) {
            return $order['status'] === 'completed' || $order['status'] === 'draft';
        });
        return array_sum(array_column($completedOrders, 'total_amount'));
    }

    public function cancelPatientOrder($orderId)
    {
        try {
            DB::transaction(function () use ($orderId) {
                $order = PatientOrder::with('details.batch')->findOrFail($orderId);

                // Restore stock untuk setiap detail
                foreach ($order->details as $detail) {
                    if ($detail->batch) {
                        $detail->batch->increment('stock_qty', $detail->qty);

                        // Insert ke stock movements untuk cancel
                        InventoryStockMovement::create([
                            'item_id' => $detail->item_id,
                            'batch_id' => $detail->batch_id,
                            'type' => 'ADJUSTMENT',
                            'qty' => $detail->qty,
                            'reference_number' => $order->order_no,
                            'notes' => 'Pembatalan pesanan obat untuk pasien MR: ' . $order->mr_code . ' Visit: ' . $order->visit_no,
                            'user_id' => auth()->id(),
                        ]);
                    }
                }

                // Update status order menjadi cancelled
                $order->update(['status' => 'cancelled']);
            });

            $this->loadSavedOrders();
            $this->dispatch('toastr:success', message: 'Pesanan berhasil dibatalkan!');
        } catch (\Exception $e) {
            $this->dispatch('toastr:error', message: 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }

    public function closeConsultation()
    {
        PatientOrder::where('visit_no', $this->visit_no)->where('status', '!=', 'cancelled')->update(['status' => 'completed']);

        $this->dispatch('toastr:success', message: 'Konsultasi berhasil ditutup!');

        $this->dispatch('open-cw', 'riwayatMdl');
        $this->dispatch('open-cw', 'diagnosaMdl');
    }
 
    public function setDiscount()
    {
        $adm = Admission::with(['patient', 'doctor', 'generalQueue'])
            ->where('visit_no', $this->visit_no)
            ->first();
        $disc = Discount::where('visit_no', $this->visit_no)->first();
        $patientOrders = PatientOrder::where('visit_no', $this->visit_no)->get();
        $this->total_tagihan = $patientOrders->sum('total_amount');

        $this->mr_code = $adm->mr_code;
        $this->visit_no = $adm->visit_no;
        $this->amount_discount = $disc ? $disc->amount_discount : 0;

        $this->dispatch('open-cw', 'discountMdl');
    }

    public function resetPatientDiscount()
    {
        $disc = Discount::where('visit_no', $this->visit_no)->first();
        if ($disc) {
            $disc->update(['amount_discount' => 0]);
        }
        $this->amount_discount = 0;
        $this->dispatch('toastr:success', message: 'Diskon berhasil direset!');
    }
 
    public function createPatientDiscount(){
        $disc = Discount::where('visit_no', $this->visit_no)->first();
        if($disc){
            $disc->update([
                'amount_discount' => $this->amount_discount,
                'user_id' => auth()->id(),
            ]);
        } else {
            Discount::create([
                'discount_no' => 'DISC-' . date('YmdHis'),
                'admission_id' => $this->adm_id,
                'mr_code' => $this->mr_code,
                'visit_no' => $this->visit_no,
                'amount_discount' => $this->amount_discount,
                'user_id' => auth()->id(),
            ]);
        }

        $this->dispatch('toastr:success', message: 'Diskon berhasil disimpan!');
        $this->dispatch('close-cw', 'discountMdl');
    }
}
