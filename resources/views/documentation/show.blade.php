@extends('layouts.guest')

@section('content')
<div class="bg-decoration"></div>

<div class="container-login100" style="max-width: 1200px; width: 100%; margin: 0 auto; padding: 40px 20px;">
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('documentation.index') }}" class="btn btn-outline-secondary mb-3">
            <i class="fas fa-arrow-left me-2"></i>Back to Documentation
        </a>
        <h1 class="login100-form-title">{{ $title }}</h1>
    </div>

    <!-- Documentation Content -->
    <div class="documentation-content">
        {!! $content !!}
    </div>

    <!-- Navigation -->
    <div class="mt-5 pt-4 border-top">
        <div class="row">
            <div class="col-md-6">
                <a href="{{ route('documentation.quick-start') }}" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fas fa-rocket me-2"></i>Quick Start Guide
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('documentation.integration-guide') }}" class="btn btn-outline-success w-100 mb-2">
                    <i class="fas fa-code me-2"></i>Integration Guide
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('documentation.api-docs') }}" class="btn btn-outline-info w-100 mb-2">
                    <i class="fas fa-server me-2"></i>API Documentation
                </a>
            </div>
            <div class="col-md-6">
                <a href="{{ route('documentation.fee-calculator') }}" class="btn btn-outline-warning w-100 mb-2">
                    <i class="fas fa-calculator me-2"></i>Fee Calculator
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.documentation-content {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    line-height: 1.8;
}

.documentation-content h1 {
    color: #333;
    font-size: 2em;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #007bff;
}

.documentation-content h2 {
    color: #444;
    font-size: 1.6em;
    margin-top: 30px;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e0e0e0;
}

.documentation-content h3 {
    color: #555;
    font-size: 1.3em;
    margin-top: 25px;
    margin-bottom: 12px;
}

.documentation-content h4 {
    color: #666;
    font-size: 1.1em;
    margin-top: 20px;
    margin-bottom: 10px;
}

.documentation-content p {
    margin-bottom: 15px;
    color: #666;
}

.documentation-content code {
    background: #f4f4f4;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.9em;
    color: #e83e8c;
}

.documentation-content pre {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    overflow-x: auto;
    border-left: 4px solid #007bff;
    margin: 15px 0;
}

.documentation-content pre code {
    background: none;
    padding: 0;
    color: #333;
}

.documentation-content ul,
.documentation-content ol {
    margin-bottom: 15px;
    padding-left: 30px;
}

.documentation-content li {
    margin-bottom: 8px;
}

.documentation-content table {
    width: 100%;
    margin: 20px 0;
    border-collapse: collapse;
}

.documentation-content table th,
.documentation-content table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}

.documentation-content table th {
    background: #f8f9fa;
    font-weight: bold;
}

.documentation-content a {
    color: #007bff;
    text-decoration: none;
}

.documentation-content a:hover {
    text-decoration: underline;
}

.documentation-content strong {
    color: #333;
    font-weight: 600;
}

.documentation-content blockquote {
    border-left: 4px solid #007bff;
    padding-left: 15px;
    margin: 15px 0;
    color: #666;
    font-style: italic;
}
</style>
@endsection

