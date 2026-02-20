<?php

namespace App\Livewire\Admission;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Admission;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Payment as PaymentModel;
use App\Models\GeneralQueue; 
use App\Models\PatientOrder;
use App\Models\PatientOrderDetail;
use App\Models\Discount;

class Payment extends Component
{
    protected $listeners = ['createPayment'];

    public $adm_id;
    public $diagnosa_id;
    public $patient_name;
    public $patient_dob;
    public $umur;
    public $complaint;
    public $doctor;
    public $mr_code;
    public $visit_no;

    public $bill;
    public $isPayment = false;
    public $payment_method;
    public $reference_number;
    public $showBill = false;
    public $bill_no;
    public $total_tagihan_awal;
    public $total_tagihan_final;
    public $amount_discount;

    public function createPayment($adm_id)
    {
        $this->bill = null;
        $this->showBill = false;
        $this->isPayment = false;
        $this->payment_method = null;
        $this->reference_number = null;
        $this->total_tagihan_awal = 0;
        $this->total_tagihan_final = 0;
        $this->amount_discount = 0;

        $admission = Admission::with(['patient', 'doctor', 'generalQueue'])
            ->where('id', $adm_id)
            ->first();

        $this->adm_id      = $admission->id;
        $this->visit_no = $admission->visit_no;
        $this->patient_name = $admission->patient->patient_name ?? '';
        $this->patient_dob = $admission->patient->patient_dob ?? '';
        $this->umur = $admission->patient->umur ?? '';
        $this->complaint = $admission->complaint ?? '';
        $this->mr_code = $admission->patient->mr_code ?? '';
        $this->umur = $this->umurLengkap($admission->patient->patient_dob ?? '');
        $this->doctor = $admission->doctor->doctor_prefix . ' ' . $admission->doctor->doctor_name . ' ' . $admission->doctor->doctor_suffix ?? ($admission->doctor_code ?? '-');
   
        $payment = PaymentModel::where('visit_no', $this->visit_no)->first();
        if ($payment) {
            $this->isPayment = true;
            $this->payment_method = $payment->payment_method;
            $this->reference_number = $payment->reference_number;
        }
 
        $patientOrders = PatientOrder::where('visit_no', $this->visit_no)
            ->where('status', 'completed')
            ->get();
        if ($patientOrders->isEmpty()) {
            $this->dispatch('toastr:error', message: 'Pemakaian pasien tidak ditemukan!');
            // return;
        }else 
            $this->total_tagihan_awal = $patientOrders->sum('total_amount');

        $disc = Discount::where('visit_no', $this->visit_no)->first();
        if ($disc) {
            $this->amount_discount = $disc->amount_discount;
            $this->total_tagihan_final = $patientOrders->sum('total_amount') - $disc->amount_discount;
        }

        $bill = Bill::where('visit_no', $this->visit_no)->first();
        if ($bill) {
            $this->showBill = true;
            $this->bill = $bill->fresh('items');
        }

        $this->dispatch('open-cw', 'paymentModal');
    }

    public function createBillingRecord()
    {
        DB::beginTransaction();
        try { 
            DB::transaction(function () {

                $patientOrders = PatientOrder::where('visit_no', $this->visit_no)
                    ->where('status', 'completed')
                    ->with('details.item') // eager load
                    ->get();

                if ($patientOrders->isEmpty()) {
                    $this->dispatch('toastr:warning', message: 'Tidak ada order completed untuk visit ini!');
                    // throw new \Exception('Tidak ada order completed untuk visit ini.');
                }

                $existingBill = Bill::where('visit_no', $this->visit_no)->exists();
                if ($existingBill) {
                    $this->dispatch('toastr:warning', message: 'Bill sudah dibuat untuk visit ini!');
                    return;
                }

                $disc = Discount::where('visit_no', $this->visit_no)->first();
                if ($disc) {
                    $this->amount_discount = $disc->amount_discount;
                    $this->total_tagihan_final = $patientOrders->sum('total_amount') - $disc->amount_discount;
                }
                
                $bill = Bill::create([
                    'mr_code'     => $this->mr_code,
                    'visit_no'    => $this->visit_no,
                    'bill_no'     => 'BILL-' . strtoupper(uniqid()),
                    'bill_date'   => now(),
                    'subtotal'    => 0,
                    'discount'    => $this->amount_discount,
                    'tax'         => 0,
                    'grand_total' => 0,
                    'status'      => 'draft',
                    'user_id'     => auth()->id(),
                ]);

                $subtotal = 0;
 
                foreach ($patientOrders as $order) { 
                    foreach ($order->details as $detail) {

                        BillItem::create([
                            'bill_id'      => $bill->id,
                            'item_type'    => $detail->item->category ?? 'unknown',
                            'reference_id' => $detail->id,
                            'description'  => $detail->item->item_code. ' - '.$detail->item->item_name, // lebih benar
                            'qty'          => $detail->qty,
                            'price'        => $detail->sell_price,
                            'subtotal'     => $detail->subtotal,
                        ]);

                        $subtotal += $detail->subtotal;
                    }
                }
 
                $bill->update([
                    'subtotal'    => $subtotal,
                    'grand_total' => $subtotal - $bill->discount + $bill->tax,
                    'status'      => 'unpaid'
                ]);


                $this->showBill = true;
                $this->bill = $bill->fresh('items');
            }); 
            DB::commit();

            $this->dispatch('toastr:info', message: 'Billing berhasil dibuat! Silakan lanjutkan ke pembayaran.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating billing record: ' . $e->getMessage());
            $this->dispatch('toastr:error', message: 'Terjadi kesalahan saat menyelesaikan pembayaran!');
        }
    }

    public function proceedToPayment(){
            DB::beginTransaction();
            try {
                // Update status admission
                $admission = Admission::findOrFail($this->adm_id);
                $admission->update([
                    'status' => 'FINISHED',
                ]);

                // Update status general queue
                $generalQueue = GeneralQueue::where('visit_no', $this->visit_no)->first();
                if ($generalQueue) {
                    $generalQueue->update([
                        'queue_status' => 'DONE',
                    ]);
                }

                // $bill = Bill::where('visit_no', $this->visit_no)->firstOrFail();
                $bill = Bill::where('visit_no', $this->visit_no)
                    ->lockForUpdate()
                    ->firstOrFail();
 
                $bill->update([
                    'status' => 'paid', //'draft', 'unpaid', 'partial', 'paid', 'cancelled'
                ]);

                $payment = PaymentModel::where('visit_no', $this->visit_no)->first();
                if ($payment) {
                    $this->isPayment = true;
                    $this->dispatch('toastr:warning', message: 'Pembayaran sudah dilakukan untuk visit ini!');
                    return;
                }
                
                $rules = [
                    'payment_method' => 'required|string',
                    'reference_number' => 'nullable|string',
                ];
                $this->validate($rules);
                PaymentModel::create([
                    'payment_no'      => 'PAY-' . strtoupper(uniqid()),
                    'mr_code'         => $this->mr_code,
                    'visit_no'        => $this->visit_no,
                    'bill_id'         => $bill->id,
                    'amount_paid'     => $bill->grand_total,
                    'payment_method'   => $this->payment_method,
                    'reference_number'     => $this->reference_number,
                    'payment_date'    => now(),
                    'user_id'         => auth()->id(),
                ]);

                $this->isPayment = true;
                DB::commit();
    
                // $this->dispatch('close-cw', 'paymentModal'); 
                $this->dispatch('toastr:success', message: 'Pembayaran berhasil diselesaikan!');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error creating payment record: ' . $e->getMessage());
                $this->dispatch('toastr:error', message: 'Terjadi kesalahan saat menyelesaikan pembayaran!');
            }
    }

    public function render()
    {
        return view(
            'livewire.admission.payment',
            [
                'registrations' => Admission::with(['patient', 'doctor', 'generalQueue'])
                    ->where('visit_date', now()->format('Y-m-d'))
                    // ->where('status', '!=', 'SETTLE')
                    ->latest()
                    ->get(),
            ]
        )
            ->layout('layouts.app', [
                'title' => 'Pembayaran',
            ]);
    }
 
    function umurLengkap($tanggalLahir)
    {
        $dob = Carbon::parse($tanggalLahir);
        $now = Carbon::now();

        return $dob->diff($now)->format('%y tahun %m bulan %d hari');
    }
}
