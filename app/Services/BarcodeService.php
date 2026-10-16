<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use setasign\Fpdi\Fpdi;

class BarcodeService
{
    public function stampDocument(Document $document): bool
    {
        try {
            $serial = $this->generateSerial();
            $qrPath = $this->generateQRCode($serial);
            
            $latestVersion = $document->latestVersion;
            if (!$latestVersion) {
                return false;
            }
            
            $originalPath = storage_path('app/public/' . $latestVersion->file_path);
            if (!file_exists($originalPath)) {
                return false;
            }
            
            $stampedPath = $this->addQRToAllPages($originalPath, $qrPath, $serial);
            
            $relativePath = str_replace(storage_path('app/public/'), '', $stampedPath);
            
            $document->update([
                'barcode_serial' => $serial,
                'barcode_stamped_at' => now(),
                'stamped_file_path' => $relativePath,
            ]);
            
            @unlink($qrPath);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Barcode stamping failed: ' . $e->getMessage());
            return false;
        }
    }
    
    private function generateSerial(): string
    {
        do {
            $serial = strtoupper(Str::random(10));
        } while (Document::where('barcode_serial', $serial)->exists());
        
        return $serial;
    }
    
    private function generateQRCode(string $serial): string
    {
        $url = url('/verify/' . $serial);
        $pngPath = storage_path('app/temp_qr_' . $serial . '.png');
        
        // Generate real QR code using endroid/qr-code
        $qrCode = \Endroid\QrCode\QrCode::create($url)
            ->setSize(300)
            ->setMargin(10);
        
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);
        $result->saveToFile($pngPath);
        
        return $pngPath;
    }
    
    private function addQRToAllPages(string $pdfPath, string $qrPath, string $serial): string
    {
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($pdfPath);
        
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
            
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
            
            $qrX = $size['width'] - 35;
            $qrY = 5;
            $pdf->Image($qrPath, $qrX, $qrY, 30, 30);
            
            $pdf->SetFont('Arial', '', 6);
            $pdf->SetXY($qrX, $qrY + 31);
            $pdf->Cell(30, 3, $serial, 0, 0, 'C');
        }
        
        $stampedPath = str_replace('.pdf', '_stamped.pdf', $pdfPath);
        $pdf->Output('F', $stampedPath);
        
        return $stampedPath;
    }
}
