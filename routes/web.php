<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\DocumentScannerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocumentStatusController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\QRScannerController;
use App\Http\Controllers\DocumentRoutingController;
use App\Http\Controllers\DocumentExportController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard - accessible by all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['verified'])->name('dashboard');
    
   // incoming documents route moved here -> mon
    Route::get('/documents/incoming', [DocumentController::class, 'incomingPage'])->name('documents.incoming.index');

    // outgoing documents route
    Route::get('/documents/outgoing', [DocumentController::class, 'outgoingPage'])->name('documents.outgoing.index');

});
    // Document Routes - with permission checks and document access middleware
    // Using Route::resource for standard CRUD operations
    Route::resource('documents', DocumentController::class)->middleware('document.access');
    Route::get('/documents/{document}/voucher', [DocumentController::class, 'printVoucher'])->name('documents.voucher')->middleware('document.access');

    // Custom Document Routes (remaining ones)
    Route::post('/documents/{document}/forward', [DocumentController::class, 'forward'])->name('documents.forward')->middleware('document.access');

    // Outgoing Documents Routes
    // Route::prefix('documents/outgoing')->name('documents.outgoing.')->group(function () {
    //     Route::get('/', [DocumentController::class, 'outgoing'])->name('index');
    // });

    // Document Routing & Forwarding Routes
    Route::prefix('documents/{document}/routing')->name('documents.routing.')->middleware('document.access')->group(function () {
        Route::get('/', [DocumentRoutingController::class, 'showRouting'])->name('show');
        Route::post('/department', [DocumentRoutingController::class, 'routeToDepartment'])->name('to-department');
        Route::post('/user', [DocumentRoutingController::class, 'forwardToUser'])->name('to-user');
        Route::get('/history', [DocumentRoutingController::class, 'getRoutingHistory'])->name('history');
    });

    // Bulk Routing
    Route::post('/documents/bulk-route', [DocumentRoutingController::class, 'bulkRoute'])->name('documents.bulk-route');
    Route::get('/routing-options', [DocumentRoutingController::class, 'getRoutingOptions'])->name('routing.options');

    // Department Routes - admin only
    Route::middleware(['permission:departments.view'])->group(function () {
        Route::resource('departments', DepartmentController::class);
    });

    // User Management Routes - admin only (Refactored)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index')->middleware('permission:users.view');
        Route::get('/create', [UserController::class, 'create'])->name('create')->middleware('permission:users.create');
        Route::post('/', [UserController::class, 'store'])->name('store')->middleware('permission:users.create');
        Route::get('/{user}', [UserController::class, 'show'])->name('show')->middleware('permission:users.view');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit')->middleware('permission:users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update')->middleware('permission:users.edit');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy')->middleware('permission:users.delete');
    });

    // Document Status Routes - with permission checks
    Route::middleware(['permission:document_statuses.view'])->group(function () {
        Route::resource('document-statuses', DocumentStatusController::class);
    });

    // Document Type Routes - with permission checks
    Route::middleware(['permission:document_types.view'])->group(function () {
        Route::resource('document-types', DocumentTypeController::class);
    });

    // QR Code routes - accessible by all authenticated users
    Route::prefix('qr-codes')->name('qr-codes.')->group(function () {
        Route::post('documents/{document}/generate', [QrCodeController::class, 'generateForDocument'])->name('generate')->middleware('document.access');
        Route::get('documents/{document}/download', [QrCodeController::class, 'download'])->name('download')->middleware('document.access');
        Route::get('documents/{document}/show', [QrCodeController::class, 'show'])->name('show')->middleware('document.access');
        Route::post('documents/{document}/print', [QrCodeController::class, 'generateForPrinting'])->name('print')->middleware('document.access');
    });

    // Document Scanner Routes - accessible by all authenticated users
    Route::prefix('scanner')->name('scanner.')->group(function () {
        Route::get('/', [QRScannerController::class, 'index'])->name('index');
        Route::post('/scan', [QRScannerController::class, 'scan'])->name('scan');
        Route::post('/document/{document}/status', [QRScannerController::class, 'updateStatus'])->name('update-status')->middleware('document.access');
        Route::get('/statuses', [QRScannerController::class, 'getStatuses'])->name('statuses');
    });

    // Document Export Routes
    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/documents/csv', [DocumentExportController::class, 'exportToCsv'])->name('documents.csv');
        Route::get('/documents/excel', [DocumentExportController::class, 'exportToExcel'])->name('documents.excel');
        Route::get('/documents/{document}/history/excel', [DocumentExportController::class, 'exportHistoryToExcel'])->name('document.history.excel')->middleware('document.access');
    });

    // Incoming Documents Route
//     Route::get('/documents/incoming', [DocumentController::class, 'incomingPage'])->name('documents.incoming.index');

// });

Route::fallback(function () { return redirect()->route('dashboard'); });

Route::get('/test-incoming', function() {
    return 'Test route works!';
});

require __DIR__.'/auth.php';
