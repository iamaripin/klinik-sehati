<?php 
namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;

use Mpdf\Mpdf;

class PrintInvoice extends Controller
{ 
    public $grouped;

    public function cek(){

        $billId = 21;
        $bill = Bill::with('items')->findOrFail($billId);
        dd($bill);
    }
    public function generate($billId)
    {
        ini_set('memory_limit', '512M'); 
        $bill = Bill::with('items')->findOrFail($billId);

        // Group berdasarkan item_type
        $groupedItems = $bill->items->groupBy('item_type');

        $subtotalPerCategory = [];
        $grandSubtotal = 0;

        foreach ($groupedItems as $type => $items) {
            $subtotal = $items->sum('subtotal');
            $subtotalPerCategory[$type] = $subtotal;
            $grandSubtotal += $subtotal;
        }

        $discount = $bill->discount;
        $tax = $bill->tax;

        $grandTotal = $grandSubtotal - $discount + $tax;

        $html = view('pdf.invoice', compact(
            'bill',
            'groupedItems',
            'subtotalPerCategory',
            'grandSubtotal',
            'discount',
            'tax',
            'grandTotal'
        ))->render();

        $mpdf = new Mpdf([
            'format' => 'A4',
            'margin_top' => 20,
            'margin_bottom' => 20,
            'margin_left' => 15,
            'margin_right' => 15,
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('Billings-' . $bill->bill_no . '.pdf', 'I');
    }
}
 
