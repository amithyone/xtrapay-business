<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>XtraPay Virtual Account API Documentation</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 60px 40px;
            color: #333;
            line-height: 1.6;
        }
        
        .docs-container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .docs-header {
            text-align: center;
            margin-bottom: 60px;
            color: white;
        }
        
        .docs-header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .docs-header p {
            font-size: 1.5rem;
            opacity: 0.95;
            font-weight: 400;
        }
        
        .docs-header .icon {
            font-size: 5rem;
            margin-bottom: 25px;
            opacity: 0.9;
        }
        
        .quick-links {
            margin-bottom: 50px;
        }
        
        .doc-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            height: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .doc-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.15);
        }
        
        .doc-card .icon-wrapper {
            width: 100px;
            height: 100px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 3rem;
        }
        
        .doc-card.quick-start .icon-wrapper {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .doc-card.integration .icon-wrapper {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .doc-card.api .icon-wrapper {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .doc-card.calculator .icon-wrapper {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
        
        .doc-card h3 {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2d3748;
            text-align: center;
        }
        
        .doc-card p {
            color: #718096;
            text-align: center;
            margin-bottom: 25px;
            font-size: 1.05rem;
        }
        
        .doc-card .btn {
            width: 100%;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 1.05rem;
            border-radius: 10px;
            border: none;
            transition: all 0.3s ease;
        }
        
        .doc-card.quick-start .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .doc-card.integration .btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .doc-card.api .btn {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .doc-card.calculator .btn {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
        
        .doc-card .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .content-section {
            background: white;
            border-radius: 16px;
            padding: 50px;
            margin-bottom: 35px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .content-section h2 {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .content-section h2 i {
            color: #667eea;
            font-size: 2rem;
        }
        
        .content-section p {
            color: #4a5568;
            font-size: 1.15rem;
            line-height: 1.9;
            margin-bottom: 18px;
        }
        
        .content-section ul,
        .content-section ol {
            color: #4a5568;
            font-size: 1.15rem;
            line-height: 1.9;
            margin-left: 25px;
            margin-bottom: 25px;
        }
        
        .content-section li {
            margin-bottom: 10px;
        }
        
        .content-section strong {
            color: #2d3748;
            font-weight: 600;
        }
        
        .content-section code {
            background: #f7fafc;
            padding: 4px 10px;
            border-radius: 6px;
            font-family: 'Courier New', 'Monaco', 'Consolas', monospace;
            font-size: 0.95em;
            color: #e53e3e;
            border: 1px solid #e2e8f0;
        }
        
        .content-section pre {
            background: #1a202c;
            padding: 25px;
            border-radius: 12px;
            overflow-x: auto;
            margin: 25px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        
        .content-section pre code {
            background: none;
            padding: 0;
            color: #e2e8f0;
            border: none;
            font-size: 0.95em;
            line-height: 1.6;
        }
        
        .content-section a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        
        .content-section a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .table-responsive {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .table thead th {
            border: none;
            padding: 20px;
            font-weight: 600;
            font-size: 1.05rem;
        }
        
        .table tbody td {
            padding: 20px;
            border-color: #e2e8f0;
            vertical-align: middle;
            font-size: 1.05rem;
        }
        
        .table tbody tr:hover {
            background-color: #f7fafc;
        }
        
        .badge {
            padding: 6px 12px;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 6px;
        }
        
        .cta-section {
            text-align: center;
            margin-top: 50px;
        }
        
        .cta-btn {
            background: white;
            color: #667eea;
            padding: 20px 50px;
            font-size: 1.25rem;
            font-weight: 600;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
        
        .cta-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            color: #764ba2;
        }
        
        @media (max-width: 1200px) {
            .docs-container {
                max-width: 100%;
                padding: 0 30px;
            }
        }
        
        @media (max-width: 992px) {
            body {
                padding: 50px 30px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 40px 20px;
            }
            
            .docs-header h1 {
                font-size: 2.5rem;
            }
            
            .docs-header p {
                font-size: 1.25rem;
            }
            
            .content-section {
                padding: 35px;
            }
            
            .content-section h2 {
                font-size: 1.75rem;
            }
            
            .doc-card {
                padding: 30px;
            }
        }
        
        @media (max-width: 576px) {
            body {
                padding: 30px 15px;
            }
            
            .docs-header h1 {
                font-size: 2rem;
            }
            
            .docs-header .icon {
                font-size: 3.5rem;
            }
            
            .content-section {
                padding: 25px;
            }
            
            .content-section h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="docs-container">
        <!-- Header -->
        <div class="docs-header">
            <div class="icon">
                <i class="fas fa-book"></i>
            </div>
            <h1>XtraPay Virtual Account API</h1>
            <p>Complete integration guide for developers</p>
        </div>

        <!-- Quick Links -->
        <div class="quick-links">
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="doc-card quick-start">
                        <div class="icon-wrapper">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h3>Quick Start</h3>
                        <p>Get up and running in 5 minutes</p>
                        <a href="{{ route('documentation.quick-start') }}" class="btn">Get Started</a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="doc-card integration">
                        <div class="icon-wrapper">
                            <i class="fas fa-code"></i>
                        </div>
                        <h3>Integration Guide</h3>
                        <p>Complete step-by-step guide</p>
                        <a href="{{ route('documentation.integration-guide') }}" class="btn">View Guide</a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="doc-card api">
                        <div class="icon-wrapper">
                            <i class="fas fa-server"></i>
                        </div>
                        <h3>API Reference</h3>
                        <p>API endpoints and examples</p>
                        <a href="{{ route('documentation.api-docs') }}" class="btn">View API</a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="doc-card calculator">
                        <div class="icon-wrapper">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <h3>Fee Calculator</h3>
                        <p>Calculate transaction fees</p>
                        <a href="{{ route('documentation.fee-calculator') }}" class="btn">Calculate</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Section -->
        <div class="content-section">
            <h2><i class="fas fa-info-circle"></i> Overview</h2>
            <p>
                XtraPay Virtual Accounts allow you to collect payments from customers via bank transfers. 
                When a customer wants to pay, you request a virtual account number from our API, display it to the customer, 
                and receive instant notifications when payment is received.
            </p>
        </div>

        <!-- Features -->
        <div class="content-section">
            <h2><i class="fas fa-star"></i> Features</h2>
            <ul>
                <li><strong>Easy Integration:</strong> Simple REST API with clear documentation</li>
                <li><strong>Real-time Notifications:</strong> Receive webhooks when payments are received</li>
                <li><strong>Secure:</strong> API key authentication and transaction isolation</li>
                <li><strong>Reliable:</strong> 99.9% uptime with automatic failover</li>
                <li><strong>Transparent Fees:</strong> 1.5% + ₦100 per transaction</li>
            </ul>
        </div>

        <!-- Quick Start Steps -->
        <div class="content-section">
            <h2><i class="fas fa-list-ol"></i> Quick Start Steps</h2>
            <ol>
                <li><strong>Get API Credentials:</strong> Log in to <a href="https://xtrapay.cash" target="_blank">xtrapay.cash</a> and get your API Code and API Key</li>
                <li><strong>Request Account:</strong> Call <code>POST /api/v1/virtual-accounts/request</code> with amount and customer details</li>
                <li><strong>Display to Customer:</strong> Show the account number and bank details</li>
                <li><strong>Receive Payment:</strong> Get webhook notification when customer pays</li>
                <li><strong>Update Order:</strong> Process the payment in your system</li>
            </ol>
        </div>

        <!-- API Endpoints -->
        <div class="content-section">
            <h2><i class="fas fa-link"></i> API Endpoints</h2>
            <div class="table-responsive">
                <table class="table">
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
        <div class="content-section">
            <h2><i class="fas fa-money-bill-wave"></i> Transaction Fees</h2>
            <p>
                <strong>Fee Structure:</strong> 1.5% of transaction amount + ₦100 flat fee
            </p>
            <p>
                <strong>Example:</strong> Customer pays ₦10,000<br>
                Fees: ₦150 (1.5%) + ₦100 = ₦250<br>
                <strong>You receive: ₦9,750</strong>
            </p>
            <p>
                Fees are automatically deducted when payment is received. 
                <a href="{{ route('documentation.fee-calculator') }}">View fee calculator</a> for detailed calculations.
            </p>
        </div>

        <!-- Support -->
        <div class="content-section">
            <h2><i class="fas fa-life-ring"></i> Need Help?</h2>
            <p>If you need assistance with integration:</p>
            <ul>
                <li>Check the <a href="{{ route('documentation.quick-start') }}">Quick Start Guide</a></li>
                <li>Review the <a href="{{ route('documentation.integration-guide') }}">Complete Integration Guide</a></li>
                <li>See the <a href="{{ route('documentation.api-docs') }}">API Documentation</a></li>
                <li>Contact support through your dashboard</li>
            </ul>
        </div>

        <!-- CTA -->
        <div class="cta-section">
            <a href="{{ route('login') }}" class="cta-btn">
                <i class="fas fa-sign-in-alt me-2"></i>Log In to Get Started
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

