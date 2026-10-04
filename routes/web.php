<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ActivityLogController;

Route::get('/', fn() => redirect('/login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public Certificate Routes (No Authentication Required)
Route::get('/certificate/verify/{token}', [CertificateController::class, 'verify'])->name('certificate.verify');
Route::get('/certificate/{token}/qr', [CertificateController::class, 'generateQrCode'])->name('certificate.qr');
Route::get('/certificate/{token}/download', [CertificateController::class, 'downloadPdf'])->name('certificate.download');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Settings
    Route::get('/settings', function () {
        return inertia('Settings/Index');
    })->name('settings');
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    
    // Activity Logs
    Route::get('/activities', [ActivityLogController::class, 'index'])->name('activities.index');
    
    // SuperAdmin Routes
    Route::middleware('role:superadmin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('admin.dashboard');
        
        // User Management
        Route::resource('users', UserController::class);
        
        // Supplier Management
        Route::resource('suppliers', SupplierController::class);
    });
    
    // Projects
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create')->middleware('role:pic_development');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store')->middleware('role:pic_development');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit')->middleware('role:pic_development');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update')->middleware('role:pic_development');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy')->middleware('role:pic_development');
    
    // Documents
    Route::post('/documents/{id}/upload', [DocumentController::class, 'upload'])->name('documents.upload')->middleware('role:supplier');
    Route::post('/documents/{id}/submit', [DocumentController::class, 'submit'])->name('documents.submit')->middleware('role:supplier');
    Route::get('/documents/versions/{versionId}/download', [DocumentController::class, 'download'])->name('documents.download');
    
    // Reviews
    Route::get('/reviews/{id}', [ReviewController::class, 'show'])->name('reviews.show')->middleware('role:pic_development,group_leader,manager');
    Route::post('/reviews/{id}/approve', [ReviewController::class, 'approve'])->name('reviews.approve')->middleware('role:pic_development,group_leader,manager');
    Route::post('/reviews/{id}/reject', [ReviewController::class, 'reject'])->name('reviews.reject')->middleware('role:pic_development,group_leader,manager');
});


// Debug route - remove after testing
Route::get('/debug-reviews/{id}', function($id) {
    $document = \App\Models\Document::with(['project', 'requiredDocument', 'latestVersion', 'reviews.reviewer'])->findOrFail($id);
    return response()->json([
        'reviews_count' => $document->reviews->count(),
        'review_ids' => $document->reviews->pluck('id')->toArray(),
        'reviews' => $document->reviews->toArray(),
    ]);
});

// Debug Inertia response
Route::get('/debug-inertia-reviews/{id}', function($id) {
    $document = \App\Models\Document::with(['project', 'requiredDocument', 'latestVersion', 'reviews.reviewer'])->findOrFail($id);
    return \Inertia\Inertia::render('DebugReviews', [
        'reviews_count' => $document->reviews->count(),
        'reviews' => $document->reviews,
        'document' => $document,
    ]);
});
