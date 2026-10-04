<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Document Approval Certificate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0891b2;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #0891b2;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #666;
            margin: 5px 0 0 0;
        }
        .content {
            margin: 20px 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #0891b2;
            margin-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 30%;
            padding: 5px 10px 5px 0;
            color: #666;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
            font-weight: bold;
        }
        .document-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .document-table th {
            background-color: #f3f4f6;
            padding: 10px;
            text-align: left;
            border: 1px solid #e5e7eb;
            font-size: 12px;
        }
        .document-table td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .approver-item {
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9fafb;
            border-left: 4px solid #10b981;
        }
        .approver-name {
            font-weight: bold;
            font-size: 14px;
        }
        .approver-role {
            color: #666;
            font-size: 12px;
            text-transform: capitalize;
        }
        .approver-date {
            color: #999;
            font-size: 11px;
            margin-top: 3px;
        }
        .qr-section {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
        }
        .qr-section p {
            margin: 10px 0 0 0;
            color: #666;
            font-size: 12px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #999;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #10b981;
            color: white;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DOCUMENT APPROVAL CERTIFICATE</h1>
        <p>E-SIGN - Electronic Supplier Document Control System</p>
    </div>

    <div class="content">
        <div class="section">
            <div class="section-title">Project Information</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">Project Code:</div>
                    <div class="info-value">{{ $project->project_code }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Project Name:</div>
                    <div class="info-value">{{ $project->project_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Model:</div>
                    <div class="info-value">{{ $project->model }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Part Number:</div>
                    <div class="info-value">{{ $project->part_number }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Part Name:</div>
                    <div class="info-value">{{ $project->part_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Supplier:</div>
                    <div class="info-value">{{ $project->supplier->name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">PIC Development:</div>
                    <div class="info-value">{{ $project->pic->name }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Document Status</div>
            <table class="document-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Document Name</th>
                        <th>Type</th>
                        <th>Version</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($project->documents as $index => $document)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $document->requiredDocument->document_name }}</td>
                        <td>{{ $document->requiredDocument->document_type }}</td>
                        <td>v{{ $document->current_version }}</td>
                        <td><span class="badge">{{ strtoupper($document->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Approval Chain</div>
            @foreach($approvers as $approver)
            <div class="approver-item">
                <div class="approver-name">{{ $approver['name'] }}</div>
                <div class="approver-role">{{ str_replace('_', ' ', $approver['role']) }}</div>
                <div class="approver-date">Approved on: {{ $approver['reviewed_at'] }}</div>
            </div>
            @endforeach
        </div>

        <div class="qr-section">
            <div>{!! $qrCodeSvg !!}</div>
            <p>Scan QR code to verify this certificate online</p>
        </div>
    </div>

    <div class="footer">
        <p>This is an electronically generated certificate from E-SIGN Document Control System</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
