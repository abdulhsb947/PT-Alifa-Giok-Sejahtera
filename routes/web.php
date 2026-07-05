<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\customer\DashboardController as CustomerDashboard;
use App\Http\Controllers\Direktur\DashboardController as DirekturDashboard;
use App\Http\Controllers\Admin\RentalController as AdminRental;
use App\Http\Controllers\Customer\RentalController as CustomerRental;
use App\Http\Controllers\Admin\OrderController as AdminOrder;
use App\Http\Controllers\Customer\OrderController as CustomerOrder;
use App\Http\Controllers\Admin\PaymentController as AdminPayment;
use App\Http\Controllers\Admin\AgreementController as AdminAgreement;
use App\Http\Controllers\Customer\PaymentController as CustomerPayment;
use App\Http\Controllers\Customer\AgreementController as CustomerAgreement;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Customer\PenaltyController;
use App\Http\Controllers\Admin\MaintenanceController as AdminMaintenance;
use App\Http\Controllers\Admin\LostProductController as AdminLostProduct;
use App\Http\Controllers\Admin\ProfileController as AdminProfile;
use App\Http\Controllers\Customer\ProfileController as CustomerProfile;
use App\Http\Controllers\Direktur\ProfileController as DirekturProfile;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Direktur\RentaltController as DirekturRental;
use App\Http\Controllers\Direktur\StockController;
use App\Http\Controllers\Direktur\MaintenanceController as DirekturMaintenance;
use App\Http\Controllers\Direktur\LostProductController as DirekturLostProduct;
use App\Http\Controllers\Direktur\OrderController as DirekturOrder;
use App\Http\Controllers\Direktur\PaymentController as Direkturpayment;
use App\Http\Controllers\Direktur\UserManagementController as UserController;
use App\Http\Controllers\Admin\OfflineOrderController;




/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::view('/', 'public.beranda');
Route::view('/tentang', 'public.tentang');
Route::get('/products', [ProductController::class, 'publicProducts']);
Route::view('/kontak', 'public.kontak');

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', fn() => view('auth.register'));
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

/*
|--------------------------------------------------------------------------
| FORGOT PASSWORD
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', fn() => view('auth.forgot-password'));
Route::post('/forgot-password', [AuthController::class, 'resetPassword']);

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->group(function () {

    Route::get('/',
        [AdminDashboard::class, 'index']
    )->name('admin.dashboard');

    Route::get('/lost-products', [AdminLostProduct::class, 'index']);

   
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/create', [ProductController::class, 'create']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}/edit', [ProductController::class, 'edit']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::get('/maintenance', fn() => view('admin.maintenance'));
    Route::post('/maintenance', [AdminMaintenance::class, 'store']);

    Route::get('/rentals', fn() => view('admin.rentals'));
    Route::get('/payments', fn() => view('admin.payments'));
    Route::get('/reports', fn() => view('admin.reports'));
    Route::get('/settings', fn() => view('admin.settings'));


    Route::get('/orders', [AdminOrder::class, 'index']);
    Route::get('/orders/{id}', [AdminOrder::class, 'show']);
    Route::post('/orders/{id}/review',[AdminOrder::class, 'reviewLapangan'])->name('admin.orders.review');
    Route::post('/orders/{id}/approve', [AdminOrder::class, 'approve']);
    Route::post('/orders/{id}/reject', [AdminOrder::class, 'reject']);
    Route::post('/chat/{orderId}', [ChatController::class, 'send']);

    Route::post('/chat/{orderId}', [ChatController::class, 'send'])->name('chat.send');

    Route::post('/orders/{id}/upload-agreement', [AdminAgreement::class, 'upload'])->name('admin.orders.uploadAgreement');
    Route::get('/agreements/{id}/download-final',[AdminAgreement::class, 'downloadFinal'])->name('admin.agreement.download-final');

    Route::get('/agreements/{agreement}/place-signature',[AdminAgreement::class,'placeSignature'])->name('admin.agreement.place-signature');
    Route::post(
    '/agreements/{agreement}/save-signature',
    [AdminAgreement::class, 'saveSignature']
)->name('admin.agreement.save-signature');
    Route::post('/agreements/{agreement}/generate-final',[AdminAgreement::class, 'generateFinalPdf'])->name('admin.agreement.generate-final');


    Route::get('/payments', [AdminPayment::class, 'index'])->name('admin.payments.index');
    Route::post('/orders/{id}/update-cost',[AdminOrder::class, 'updateCost'])->name('admin.orders.updateCost');
    Route::post('/payments/{id}/approve', [AdminPayment::class, 'approve']);
    Route::post('/payments/{id}/reject', [AdminPayment::class, 'reject']);
    Route::get('/payments/create', [AdminPayment::class, 'create'])->name('admin.payments.create');
    Route::post('/payments/store', [AdminPayment::class, 'store'])->name('admin.payments.store');
    Route::post('/payments/store', [AdminPayment::class, 'store'])->name('admin.payments.store');

    Route::get('/rentals', [AdminRental::class, 'index'])->name('admin.rentals');
    Route::post('/rentals/send/{id}', [AdminRental::class, 'send'])->name('admin.rental.send');
    Route::post('/rental/return/{id}', [AdminRental::class, 'return'])->name('admin.rental.return');
    Route::get('/rental/{id}/return', [AdminRental::class, 'returnForm'])->name('admin.rental.return.form');
    Route::post('/rental/{id}/return', [AdminRental::class, 'return'])->name('admin.rental.return');


    Route::get('/maintenance', [AdminMaintenance::class, 'index'])->name('admin.maintenance');
    Route::post('/maintenance', [AdminMaintenance::class, 'store'])->name('admin.maintenance.store');
    Route::post('/maintenance/{id}/selesai', [AdminMaintenance::class, 'selesai'])->name('admin.maintenance.selesai');
    Route::get('/admin/lost-products', [AdminLostProduct::class, 'index']);

    Route::get('/offline',[OfflineOrderController::class, 'index']);
    Route::resource('offline-orders',OfflineOrderController::class);
    Route::put('offline-orders/{id}/activate',[OfflineOrderController::class,'activateRental'])->name('offline-orders.activate');
    Route::put('offline-orders/{id}/finish',[OfflineOrderController::class,'finishRental'])->name('offline-orders.finish');
    Route::put('offline-orders/{id}/return',[OfflineOrderController::class,'processReturn'])->name('offline-orders.return');
    Route::get('offline-orders/{id}/check-return',[OfflineOrderController::class, 'checkReturn'])->name('offline-orders.check-return');
    Route::post('offline-orders/{id}/agreement',[OfflineOrderController::class,'storeAgreement'])->name('offline-orders.agreement');
    Route::post('offline-orders/{id}/final-payment',[OfflineOrderController::class,'finalPayment'])->name('offline-orders.final-payment');

    Route::get('/notifications/{id}/read',[AdminNotificationController::class, 'read'])->name('admin.notifications.read');
    Route::delete('/notifications/{id}',[AdminNotificationController::class, 'destroy'])->name('admin.notifications.delete');



    Route::get('/admin/settings', function () {
        return view('admin.settings');
    })->name('admin.settings');
    Route::post('/profile/update', [AdminProfile::class, 'updateProfile'])->name('admin.profile.update');
    Route::post('/password/update', [AdminProfile::class, 'updatePassword'])->name('admin.password.update');
});

/*
|--------------------------------------------------------------------------
| CUSTOMER
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('customer')->group(function () {

    Route::get('/', [CustomerDashboard::class, 'index'])
    ->name('customer.dashboard');

    Route::get('/products', [ProductController::class, 'customerProducts']);

    Route::get('/about', fn() => view('customer.about'));
    Route::get('/contact', fn() => view('customer.contact'));

    // 🔥 ORDERS
    Route::get('/orders', [CustomerOrder::class, 'index'])->name('customer.orders');
    Route::get('/orders/create', [CustomerOrder::class, 'create']);

    Route::post('/orders/step2', [CustomerOrder::class, 'step2']);
    Route::post('/orders/step3', [CustomerOrder::class, 'step3']);
    Route::post('/orders/step4', [CustomerOrder::class, 'step4']);


    Route::post('/orders/store', [CustomerOrder::class, 'store'])->name('customer.order.store');

    Route::get('/orders/{id}/edit', [CustomerOrder::class, 'edit']);
    Route::post('/orders/{id}/update', [CustomerOrder::class, 'update'])->name('customer.order.update');


    Route::get('/orders/{id}/edit', [CustomerOrder::class, 'edit'])
        ->name('customer.orders.edit');
    Route::post('/orders/{id}/edit/step2', [CustomerOrder::class, 'editStep2']);
    Route::get('/orders/step2', [CustomerOrder::class, 'step2View']);
    Route::post('/orders/{id}/edit/step3', [CustomerOrder::class, 'editStep3']);
    Route::post('/orders/{id}/edit/step4', [CustomerOrder::class, 'editStep4']);




    // 🔥 OTHER
    Route::get('/rentals', fn() => view('customer.rentals'));
    Route::get('/history', fn() => view('customer.history'));
    Route::get('/settings', fn() => view('customer.settings'));

    Route::get('/orders/{id}', [CustomerOrder::class, 'show']);

    Route::post('/orders/{id}/agreement', [CustomerAgreement::class, 'approveAgreement']);
    Route::get('/agreement/view/{id}', [CustomerAgreement::class, 'viewAgreement']);
    Route::post('/orders/{id}/approve-agreement', [CustomerAgreement::class, 'approveAgreement']);
    Route::post('/orders/{id}/reject-agreement', [CustomerAgreement::class, 'rejectAgreement']);


    Route::get('/payment/{id}', [CustomerPayment::class, 'paymentPage'])->name('customer.payment.page');
    Route::post('/payment/{id}', [CustomerPayment::class, 'uploadPayment'])->name('customer.payment.store');
    Route::get('/payments', [CustomerPayment::class, 'paymentList']);
    Route::get('/payments', [CustomerPayment::class, 'paymentList'])->name('customer.payment.list');
    Route::post('/payment/upload/{id}', [CustomerPayment::class, 'uploadProof'])->name('customer.payment.upload');
    Route::get('/payment/pelunasan/{order}', [CustomerPayment::class, 'pelunasanForm']);
    Route::post('/payment/pelunasan/{order}', [CustomerPayment::class, 'pelunasanStore']);
    Route::get('/penalty/{payment}',[App\Http\Controllers\Customer\PaymentController::class, 'penaltyDetail'])->name('customer.penalty.show');

    Route::post('/rental', [CustomerRental::class, 'store']);
    Route::get('/rental/return/{id}', [CustomerRental::class, 'return']);

    Route::get('/rentals', [CustomerRental::class, 'index'])->name('customer.rentals');
    Route::post('/rental/{id}/returned', [CustomerRental::class, 'markReturned'])->name('customer.rental.returned');

    Route::get('/penalty/{id}/pay', [PenaltyController::class, 'payForm'])->name('penalty.pay.form');
    Route::post('/penalty/{id}/pay', [PenaltyController::class, 'pay'])->name('penalty.pay');
    Route::get('/penalty/{payment}',[PenaltyController::class, 'penaltyDetail'])->name('customer.penalty.show');


    Route::get('/customer/settings', function () {
        return view('customer.settings');
    })->name('customer.settings');
    Route::post('/profile/update', [CustomerProfile::class, 'updateProfile'])->name('customer.profile.update');
    Route::post('/password/update', [CustomerProfile::class, 'updatePassword'])->name('customer.password.update');
});




/*
|--------------------------------------------------------------------------
| DIREKTUR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('direktur')->group(function () {

    Route::get('/',[DirekturDashboard::class, 'index'])->name('direktur.dashboard');

    Route::get('/security', function () {
        $logs = \App\Models\SecurityLog::latest()->get();
        return view('direktur.security', compact('logs'));
    });

    Route::get('/rental', [DirekturRental::class, 'index'])->name('direktur.rental.reports');
    Route::get('/stock', [StockController::class, 'index'])->name('direktur.stock.reports');
    Route::get('/maintenance', [DirekturMaintenance::class, 'index'])->name('direktur.maintenance.reports');
    Route::get('/lost-products',[DirekturLostProduct::class, 'index']);
    Route::get('/orders',[DirekturOrder::class, 'orders'])->name('direktur.orders');
    Route::get('/payments',[DirekturPayment::class, 'payments'])->name('direktur.payments');


    Route::get('/settings', function () {
        return view('direktur.settings');
    })->name('direktur.settings');
    Route::post('/profile/update', [DirekturProfile::class, 'updateProfile'])->name('direktur.profile.update');
    Route::post('/password/update', [DirekturProfile::class, 'updatePassword'])->name('direktur.password.update');

    Route::get('/users',[UserController::class, 'index'])->name('direktur.users');
    Route::get('/users/create',[UserController::class, 'create'])->name('direktur.users.create');
    Route::post('/users',[UserController::class, 'store'])->name('direktur.users.store');
});








Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
Route::get('/notifications/read/{id}', [NotificationController::class, 'read'])->name('notifications.read');
Route::delete('/notifications/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');
