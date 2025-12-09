@extends('layouts.guest')

@section('content')
<div class="bg-decoration"></div>

<div class="container-login100" style="max-width: 1200px; width: 100%; margin: 0 auto; padding: 40px 20px;">
    <div class="text-center mb-5">
        <div class="logo-container">
            <i class="fas fa-book fa-3x gradient-text"></i>
        </div>
        <div class="login100-form-title">
            XtraPay Virtual Account API Documentation
        </div>
        <p class="txt1">Complete integration guide for developers</p>
    </div>

    <!-- Quick Links -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0" style="transition: transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-rocket fa-3x text-primary"></i>
                    </div>
                    <h5 class="card-title">Quick Start</h5>
                    <p class="card-text text-muted small">Get up and running in 5 minutes</p>
                    <a href="{{ route('documentation.quick-start') }}" class="btn btn-primary btn-sm">Get Started</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0" style="transition: transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-code fa-3x text-success"></i>
                    </div>
                    <h5 class="card-title">Integration Guide</h5>
                    <p class="card-text text-muted small">Complete step-by-step guide</p>
                    <a href="{{ route('documentation.integration-guide') }}" class="btn btn-success btn-sm">View Guide</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0" style="transition: transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-server fa-3x text-info"></i>
                    </div>
                    <h5 class="card-title">API Reference</h5>
                    <p class="card-text text-muted small">API endpoints and examples</p>
                    <a href="{{ route('documentation.api-docs') }}" class="btn btn-info btn-sm">View API</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0" style="transition: transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-calculator fa-3x text-warning"></i>
                    </div>
                    <h5 class="card-title">Fee Calculator</h5>
                    <p class="card-text text-muted small">Calculate transaction fees</p>
                    <a href="{{ route('documentation.fee-calculator') }}" class="btn btn-warning btn-sm">Calculate</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Section -->
    <div class="info-section mb-4">
        <h3 class="section-title">
            <i class="fas fa-info-circle me-2"></i>Overview
        </h3>
        <p class="section-text">
            XtraPay Virtual Accounts allow you to collect payments from customers via bank transfers. 
            When a customer wants to pay, you request a virtual account number from our API, display it to the customer, 
            and receive instant notifications when payment is received.
        </p>
    </div>

    <!-- Features -->
    <div class="info-section mb-4">
        <h3 class="section-title">
            <i class="fas fa-star me-2"></i>Features
        </h3>
        <ul class="section-text">
            <li><strong>Easy Integration:</strong> Simple REST API with clear documentation</li>
            <li><strong>Real-time Notifications:</strong> Receive webhooks when payments are received</li>
            <li><strong>Secure:</strong> API key authentication and transaction isolation</li>
            <li><strong>Reliable:</strong> 99.9% uptime with automatic failover</li>
            <li><strong>Transparent Fees:</strong> 1.5% + ₦100 per transaction</li>
        </ul>
    </div>

    <!-- Quick Start Steps -->
    <div class="info-section mb-4">
        <h3 class="section-title">
            <i class="fas fa-list-ol me-2"></i>Quick Start Steps
        </h3>
        <ol class="section-text">
            <li><strong>Get API Credentials:</strong> Log in to <a href="https://xtrapay.cash" target="_blank">xtrapay.cash</a> and get your API Code and API Key</li>
            <li><strong>Request Account:</strong> Call <code>POST /api/v1/virtual-accounts/request</code> with amount and customer details</li>
            <li><strong>Display to Customer:</strong> Show the account number and bank details</li>
            <li><strong>Receive Payment:</strong> Get webhook notification when customer pays</li>
            <li><strong>Update Order:</strong> Process the payment in your system</li>
        </ol>
    </div>

    <!-- API Endpoints -->
    <div class="info-section mb-4">
        <h3 class="section-title">
            <i class="fas fa-link me-2"></i>API Endpoints
        </h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th>Endpoint</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge bg-success">POST</span></td>
                        <td><code>/api/v1/virtual-accounts/request</code></td>
                        <td>Request a new virtual account number</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-success">POST</span></td>
                        <td><code>/api/v1/virtual-accounts/check-status</code></td>
                        <td>Check payment status for a transaction</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Fee Information -->
    <div class="info-section mb-4">
        <h3 class="section-title">
            <i class="fas fa-money-bill-wave me-2"></i>Transaction Fees
        </h3>
        <p class="section-text">
            <strong>Fee Structure:</strong> 1.5% of transaction amount + ₦100 flat fee
        </p>
        <p class="section-text">
            <strong>Example:</strong> Customer pays ₦10,000<br>
            Fees: ₦150 (1.5%) + ₦100 = ₦250<br>
            <strong>You receive: ₦9,750</strong>
        </p>
        <p class="section-text">
            Fees are automatically deducted when payment is received. 
            <a href="{{ route('documentation.fee-calculator') }}">View fee calculator</a> for detailed calculations.
        </p>
    </div>

    <!-- Support -->
    <div class="info-section mb-4">
        <h3 class="section-title">
            <i class="fas fa-life-ring me-2"></i>Need Help?
        </h3>
        <p class="section-text">
            If you need assistance with integration:
        </p>
        <ul class="section-text">
            <li>Check the <a href="{{ route('documentation.quick-start') }}">Quick Start Guide</a></li>
            <li>Review the <a href="{{ route('documentation.integration-guide') }}">Complete Integration Guide</a></li>
            <li>See the <a href="{{ route('documentation.api-docs') }}">API Documentation</a></li>
            <li>Contact support through your dashboard</li>
        </ul>
    </div>

    <!-- CTA -->
    <div class="text-center mt-5">
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-sign-in-alt me-2"></i>Log In to Get Started
        </a>
    </div>
</div>

<style>
.card:hover {
    transform: translateY(-5px);
}
.info-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.section-title {
    color: #333;
    margin-bottom: 15px;
    font-size: 1.3em;
}
.section-text {
    color: #666;
    line-height: 1.8;
}
code {
    background: #f4f4f4;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.9em;
}
pre {
    background: #f4f4f4;
    padding: 15px;
    border-radius: 5px;
    overflow-x: auto;
}
pre code {
    background: none;
    padding: 0;
}
</style>
@endsection

