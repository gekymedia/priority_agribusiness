@extends('layouts.guest')

@section('title', 'Terms of Service - Priority Agriculture')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h1 class="h3 mb-0">Terms of Service</h1>
                    <p class="mb-0 small">Effective Date: {{ date('F j, Y') }}</p>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Important:</strong> Please read these Terms of Service carefully. By using Priority Agriculture services, you agree to these terms.
                    </div>

                    <h2 class="h4 text-success mt-5 mb-3">1. Acceptance of Terms</h2>
                    <p>Welcome to Priority Agriculture. These Terms govern your use of our agricultural management system for poultry, crop farming, and related operations.</p>

                    <h2 class="h4 text-success mt-5 mb-3">2. Services Description</h2>
                    <p>Priority Agriculture provides farm management services including:</p>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item">Poultry management (egg production, bird sales, inventory tracking)</li>
                        <li class="list-group-item">Crop farming management (planting, harvesting, sales)</li>
                        <li class="list-group-item">Financial record keeping (income, expenses, transactions)</li>
                        <li class="list-group-item">Inventory and supply management</li>
                        <li class="list-group-item">Staff and employee management</li>
                    </ul>

                    <h2 class="h4 text-success mt-5 mb-3">3. User Responsibilities</h2>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item">Provide accurate information for all records</li>
                        <li class="list-group-item">Maintain account security and confidentiality</li>
                        <li class="list-group-item">Use the system only for authorized purposes</li>
                        <li class="list-group-item">Comply with applicable laws and regulations</li>
                        <li class="list-group-item">Backup critical data independently</li>
                    </ul>

                    <h2 class="h4 text-success mt-5 mb-3">4. Data Accuracy</h2>
                    <p>While we strive for accuracy, users are responsible for:</p>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item">Verifying all entered data</li>
                        <li class="list-group-item">Reviewing records for errors</li>
                        <li class="list-group-item">Reporting discrepancies promptly</li>
                    </ul>
                    <div class="alert alert-warning">
                        Priority Agriculture is not liable for decisions made based on system data. Always verify critical information.
                    </div>

                    <h2 class="h4 text-success mt-5 mb-3">5. Service Availability</h2>
                    <p>We strive for high availability but cannot guarantee 100% uptime. Services may be interrupted for maintenance or technical issues.</p>

                    <h2 class="h4 text-success mt-5 mb-3">6. Limitation of Liability</h2>
                    <p>To the maximum extent permitted by law, Priority Agriculture's liability is limited to the value of services provided. We are not liable for indirect, consequential, or special damages.</p>

                    <h2 class="h4 text-success mt-5 mb-3">7. Intellectual Property</h2>
                    <p>All system software, features, and content are the property of Priority Agriculture. You may not reproduce, distribute, or create derivative works without permission.</p>

                    <h2 class="h4 text-success mt-5 mb-3">8. Termination</h2>
                    <p>We may suspend or terminate access for violations of these Terms or non-payment. Upon termination, data retention follows our Privacy Policy.</p>

                    <h2 class="h4 text-success mt-5 mb-3">9. Modifications</h2>
                    <p>We may update these Terms. Significant changes will be communicated. Continued use after changes constitutes acceptance.</p>

                    <h2 class="h4 text-success mt-5 mb-3">10. Contact Information</h2>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-1"><strong>Priority Agriculture</strong></p>
                            <p class="mb-1">Email: <a href="mailto:info@priorityagriculture.com">info@priorityagriculture.com</a></p>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted text-center">
                    <small>Document Version: 1.0 | Effective Date: {{ date('F j, Y') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

