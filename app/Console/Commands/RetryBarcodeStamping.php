<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Services\BarcodeService;
use Illuminate\Console\Command;

class RetryBarcodeStamping extends Command
{
    protected $signature = 'barcode:retry {document_id?}';
    protected $description = 'Retry barcode stamping for failed documents';

    public function handle()
    {
        $documentId = $this->argument('document_id');
        
        if ($documentId) {
            $documents = Document::where('id', $documentId)->get();
        } else {
            $documents = Document::where('status', 'complete')
                ->whereNull('barcode_serial')
                ->get();
        }
        
        $this->info("Found {$documents->count()} documents to process");
        
        $service = new BarcodeService();
        $success = 0;
        $failed = 0;
        
        foreach ($documents as $doc) {
            $this->info("Processing document {$doc->id}...");
            
            if ($service->stampDocument($doc)) {
                $this->info("✅ Success: Document {$doc->id} - Serial: {$doc->barcode_serial}");
                $success++;
            } else {
                $this->error("❌ Failed: Document {$doc->id}");
                $failed++;
            }
        }
        
        $this->info("\nResults: {$success} success, {$failed} failed");
        
        return $success > 0 ? 0 : 1;
    }
}
