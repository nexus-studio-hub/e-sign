<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class CertificateController extends Controller
{
    public function verify($token)
    {
        $project = Project::where('certificate_token', $token)->firstOrFail();
        $project->load(['supplier', 'pic', 'documents.requiredDocument', 'documents.reviews.reviewer']);
        
        $approvers = $project->getAllApprovers();
        
        return Inertia::render('Certificate/Show', [
            'project' => $project,
            'approvers' => $approvers,
            'isFullyCompleted' => $project->isFullyCompleted(),
        ]);
    }

    public function generateQrCode($token)
    {
        $project = Project::where('certificate_token', $token)->firstOrFail();
        $url = route('certificate.verify', $token);
        
        $qrCode = QrCode::format('svg')
            ->size(300)
            ->generate($url);
        
        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml');
    }

    public function downloadPdf($token)
    {
        $project = Project::where('certificate_token', $token)->firstOrFail();
        $project->load(['supplier', 'pic', 'documents.requiredDocument']);
        
        $approvers = $project->getAllApprovers();
        $qrCodeUrl = route('certificate.verify', $token);
        
        $qrCodeSvg = QrCode::format('svg')
            ->size(200)
            ->generate($qrCodeUrl);
        
        $pdf = Pdf::loadView('certificate.pdf', [
            'project' => $project,
            'approvers' => $approvers,
            'qrCodeSvg' => $qrCodeSvg,
        ]);
        
        return $pdf->download('certificate-' . $project->project_code . '.pdf');
    }
}
