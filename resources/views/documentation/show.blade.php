<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - XtraPay Virtual Account API Documentation</title>
    
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
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 30px;
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            transform: translateX(-5px);
        }
        
        .docs-header {
            text-align: center;
            margin-bottom: 50px;
            color: white;
        }
        
        .docs-header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 15px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .docs-content {
            background: white;
            border-radius: 20px;
            padding: 60px;
            box-shadow: 0 10px 50px rgba(0,0,0,0.15);
            margin-bottom: 40px;
        }
        
        .docs-content h1 {
            color: #2d3748;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #667eea;
        }
        
        .docs-content h2 {
            color: #2d3748;
            font-size: 2rem;
            font-weight: 700;
            margin-top: 50px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .docs-content h3 {
            color: #4a5568;
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 35px;
            margin-bottom: 15px;
        }
        
        .docs-content h4 {
            color: #4a5568;
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 25px;
            margin-bottom: 12px;
        }
        
        .docs-content h5 {
            color: #718096;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        .docs-content p {
            color: #4a5568;
            font-size: 1.1rem;
            line-height: 1.9;
            margin-bottom: 20px;
        }
        
        .docs-content ul,
        .docs-content ol {
            color: #4a5568;
            font-size: 1.1rem;
            line-height: 1.9;
            margin-left: 30px;
            margin-bottom: 25px;
        }
        
        .docs-content li {
            margin-bottom: 12px;
        }
        
        .docs-content code {
            background: #f7fafc;
            padding: 4px 10px;
            border-radius: 6px;
            font-family: 'Courier New', 'Monaco', 'Consolas', monospace;
            font-size: 0.95em;
            color: #e53e3e;
            border: 1px solid #e2e8f0;
        }
        
        .docs-content pre {
            background: #1a202c;
            padding: 25px;
            border-radius: 12px;
            overflow-x: auto;
            margin: 25px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        
        .docs-content pre code {
            background: none;
            padding: 0;
            color: #e2e8f0;
            border: none;
            font-size: 0.95em;
            line-height: 1.6;
        }
        
        .docs-content table {
            width: 100%;
            margin: 30px 0;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .docs-content table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .docs-content table th {
            padding: 18px;
            text-align: left;
            font-weight: 600;
            font-size: 1.05rem;
        }
        
        .docs-content table td {
            padding: 18px;
            border-bottom: 1px solid #e2e8f0;
            color: #4a5568;
            font-size: 1.05rem;
        }
        
        .docs-content table tbody tr:hover {
            background-color: #f7fafc;
        }
        
        .docs-content table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .docs-content a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        
        .docs-content a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .docs-content strong {
            color: #2d3748;
            font-weight: 600;
        }
        
        .docs-content blockquote {
            border-left: 4px solid #667eea;
            padding-left: 25px;
            margin: 25px 0;
            color: #718096;
            font-style: italic;
            background: #f7fafc;
            padding: 20px 25px;
            border-radius: 8px;
        }
        
        .docs-content hr {
            border: none;
            border-top: 2px solid #e2e8f0;
            margin: 40px 0;
        }
        
        .nav-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 50px rgba(0,0,0,0.15);
        }
        
        .nav-section h3 {
            color: #2d3748;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .nav-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .nav-btn {
            padding: 18px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.05rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 2px solid transparent;
        }
        
        .nav-btn.quick-start {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .nav-btn.integration {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .nav-btn.api {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .nav-btn.calculator {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
        
        .nav-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            color: white;
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .badge-success {
            background: #48bb78;
            color: white;
        }
        
        .badge-info {
            background: #4299e1;
            color: white;
        }
        
        .badge-warning {
            background: #ed8936;
            color: white;
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
            
            .docs-content {
                padding: 40px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 40px 20px;
            }
            
            .docs-header h1 {
                font-size: 2.5rem;
            }
            
            .docs-content {
                padding: 30px;
            }
            
            .docs-content h1 {
                font-size: 2rem;
            }
            
            .docs-content h2 {
                font-size: 1.75rem;
            }
            
            .nav-buttons {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            body {
                padding: 30px 15px;
            }
            
            .docs-header h1 {
                font-size: 2rem;
            }
            
            .docs-content {
                padding: 25px;
            }
            
            .docs-content h1 {
                font-size: 1.75rem;
            }
            
            .docs-content h2 {
                font-size: 1.5rem;
            }
            
            .docs-content p,
            .docs-content ul,
            .docs-content ol {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="docs-container">
        <!-- Back Button -->
        <a href="{{ route('documentation.index') }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Documentation
        </a>

        <!-- Header -->
        <div class="docs-header">
            <h1>{{ $title }}</h1>
        </div>

        <!-- Documentation Content -->
        <div class="docs-content">
            {!! $content !!}
        </div>

        <!-- Navigation -->
        <div class="nav-section">
            <h3>Explore More Documentation</h3>
            <div class="nav-buttons">
                <a href="{{ route('documentation.quick-start') }}" class="nav-btn quick-start">
                    <i class="fas fa-rocket"></i>
                    Quick Start Guide
                </a>
                <a href="{{ route('documentation.integration-guide') }}" class="nav-btn integration">
                    <i class="fas fa-code"></i>
                    Integration Guide
                </a>
                <a href="{{ route('documentation.api-docs') }}" class="nav-btn api">
                    <i class="fas fa-server"></i>
                    API Documentation
                </a>
                <a href="{{ route('documentation.fee-calculator') }}" class="nav-btn calculator">
                    <i class="fas fa-calculator"></i>
                    Fee Calculator
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
