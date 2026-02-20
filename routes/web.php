<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientRelationController;

use App\Livewire\Dashboard;

// MASTER DATA
use App\Livewire\MasterData\User as UserMasterData;
use App\Livewire\MasterData\Patient as PatientMasterData;
use App\Livewire\MasterData\Doctor as DoctorMasterData;
use App\Livewire\MasterData\Suppliers as SupplierMasterData;
use App\Livewire\MasterData\InventoryItems as InventoryMasterData;
use App\Livewire\MasterData\InventoryBatch as InventoryBatchData;
use App\Livewire\MasterData\InventoryCategory as InventoryCategoryMasterData;
use Illuminate\Http\Request;
use App\Models\InventoryItems as InventoryItemsModel;

// DOKTER MODULE
use \App\Livewire\Doctor\Diagnosa;

// NURSE MODULE
use \App\Livewire\Nurse\Anamnesa;

use App\Livewire\Admission\RawatJalan;
use App\Livewire\Patient\MedicalRecord;
use App\Livewire\Admission\Registration as AdmissionRegistration;
use App\Livewire\Admission\Payment as AdmissionPayment;
use App\Http\Controllers\DisplayAntrian;
use App\Http\Controllers\PrintInvoice;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/insert', [PrintInvoice::class, 'store']);
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class);


    // search for inventory items
    Route::get('/api/inventory-items/search', function (Request $request) {
        $q = $request->get('q');
        $items = InventoryItemsModel::query()
            ->when($q, fn($qbl) => $qbl->where('item_name', 'like', '%' . $q . '%')->orWhere('item_code', 'like', '%' . $q . '%'))
            ->limit(20)
            ->get()
            ->map(fn($it) => ['id' => $it->id, 'text' => $it->item_code . ' - ' . $it->item_name]);

        return response()->json($items);
    })->name('inventory.items.search');

    // api antrian pasien
    Route::get('/api/queue/display', function () {

        $active = \App\Models\GeneralQueue::where('visit_date', date('Y-m-d'))
            ->where('queue_status', 'CALL')
            ->first();

        $next = \App\Models\GeneralQueue::where('visit_date', date('Y-m-d'))
            ->where('queue_status', 'WAITING')
            ->orderBy('queue_no')
            ->take(2)
            ->get();

        return response()->json([
            'active' => $active,
            'next' => $next
        ]);
    });
             
    // MASTER DATA
    Route::get('/master/users', UserMasterData::class);
    Route::get('/master/doctor', DoctorMasterData::class);
    Route::get('/master/patient', PatientMasterData::class); 
    Route::get('/master/suppliers', SupplierMasterData::class);
    Route::get('/master/inventory', InventoryMasterData::class);
    Route::get('/master/inventory/stock_in', InventoryBatchData::class);
    Route::get('/master/inventory-category', InventoryCategoryMasterData::class);

    Route::get('/admission/rawat-jalan', RawatJalan::class);
    Route::get('/admission/create', \App\Livewire\Admission\RawatJalan::class);
   
    Route::get('/resep', \App\Livewire\Profile::class);
    Route::get('/registration', AdmissionRegistration::class);
 
    Route::get('/patient/medical-record', MedicalRecord::class);
 
    // DOCTOR MODULE
    Route::get('/diagnosa', Diagnosa::class);

    // NURSE MODULE
    Route::get('/anamnesa', Anamnesa::class);
    // Route::post('/nurse/anamnesa/save', [Anamnesa::class, 'save'])->name('nurse.save-anamnesa');
 
    // PAYMENT MODULE
    Route::get('/payment', AdmissionPayment::class);

    // OTHER MODULES
    Route::get('/display-antrian', [DisplayAntrian::class, 'render']);

    // PDF GENERATION
    Route::get('/print-invoice/{billId}', [\App\Http\Controllers\PrintInvoice::class, 'generate'])->name('invoice.pdf');
    Route::get('/cek', [\App\Http\Controllers\PrintInvoice::class, 'cek']);

});



Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::get('/password', [AuthController::class, 'passwordPage'])->middleware('auth');
Route::post('/password', [AuthController::class, 'updatePassword'])->middleware('auth');
