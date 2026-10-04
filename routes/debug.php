<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug-reviews/{id}', function($id) {
    $document = \App\Models\Document::with(['project', 'requiredDocument', 'latestVersion', 'reviews.reviewer'])->findOrFail($id);
    return response()->json([
        'reviews_count' => $document->reviews->count(),
        'review_ids' => $document->reviews->pluck('id')->toArray(),
        'reviews_full' => $document->reviews->toArray(),
    ]);
});
