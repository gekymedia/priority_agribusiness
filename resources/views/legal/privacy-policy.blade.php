@extends('layouts.guest')

@section('title', 'Privacy Policy - Priority Agriculture')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h1 class="h3 mb-0">Privacy Policy</h1>
                    <p class="mb-0 small">Last Updated: {{ date('F j, Y') }}</p>
                </div>
                <div class="card-body">
                    <p class="lead">At Priority Agriculture, we are committed to protecting the privacy and security of your information. This Privacy Policy explains how we collect, use, disclose, and safeguard information related to our agricultural operations including poultry, crop farming, and related business activities.</p>

                    <h2 class="h4 text-success mt-5 mb-3">1. Information We Collect</h2>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item"><strong>Farm Operations Data:</strong> Production records, inventory, sales, expenses, farm management information</li>
                        <li class="list-group-item"><strong>Financial Information:</strong> Sales transactions, expenses, payment records, pricing data</li>
                        <li class="list-group-item"><strong>Employee Data:</strong> Staff information, payroll, attendance, work records</li>
                        <li class="list-group-item"><strong>Customer Information:</strong> Buyer details, purchase history, contact information</li>
                        <li class="list-group-item"><strong>Business Records:</strong> Contracts, agreements, operational documentation</li>
                    </ul>

                    <h2 class="h4 text-success mt-5 mb-3">2. How We Use Your Information</h2>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="border p-3 rounded">
                                <h5 class="h6 text-success"><i class="fas fa-chart-line me-2"></i>Farm Management</h5>
                                <p class="small mb-0">Tracking production, inventory, and operational efficiency</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border p-3 rounded">
                                <h5 class="h6 text-success"><i class="fas fa-money-bill me-2"></i>Financial Management</h5>
                                <p class="small mb-0">Recording sales, expenses, and financial transactions</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border p-3 rounded">
                                <h5 class="h6 text-success"><i class="fas fa-users me-2"></i>Staff Management</h5>
                                <p class="small mb-0">Managing employees, payroll, and work schedules</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border p-3 rounded">
                                <h5 class="h6 text-success"><i class="fas fa-handshake me-2"></i>Customer Relations</h5>
                                <p class="small mb-0">Managing customer relationships and sales records</p>
                            </div>
                        </div>
                    </div>

                    <h2 class="h4 text-success mt-5 mb-3">3. Data Protection</h2>
                    <div class="alert alert-info">
                        <p class="mb-2"><strong>Security Measures:</strong></p>
                        <ul class="mb-0">
                            <li>256-bit SSL encryption for data transmissions</li>
                            <li>Secure storage with access controls</li>
                            <li>Regular security audits</li>
                            <li>Restricted access to authorized personnel only</li>
                        </ul>
                    </div>

                    <h2 class="h4 text-success mt-5 mb-3">4. Data Sharing</h2>
                    <p>We may share information with:</p>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item">Service providers (payment processors, cloud hosting)</li>
                        <li class="list-group-item">Business partners (suppliers, distributors)</li>
                        <li class="list-group-item">Legal authorities when required by law</li>
                    </ul>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>We never sell your business data to third parties.
                    </div>

                    <h2 class="h4 text-success mt-5 mb-3">5. Your Rights</h2>
                    <p>You have the right to access, correct, or request deletion of your data. Contact us at:</p>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-1"><strong>Email:</strong> <a href="mailto:privacy@priorityagriculture.com">privacy@priorityagriculture.com</a></p>
                        </div>
                    </div>

                    <h2 class="h4 text-success mt-5 mb-3">6. Data Retention</h2>
                    <p>We retain business and financial records according to legal requirements (typically 7 years for financial records).</p>

                    <h2 class="h4 text-success mt-5 mb-3">7. Contact Information</h2>
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

