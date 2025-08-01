@extends('layouts.guest')

@section('content')
<div class="bg-decoration"></div>

<div class="container-login100" style="max-width: 800px; width: 100%; margin: 0 auto; padding: 20px;">
    <div class="text-center mb-4">
        <div class="logo-container">
            <i class="fas fa-building fa-3x gradient-text"></i>
        </div>
        <div class="login100-form-title">
            XtraPay Business
        </div>
        <p class="txt1">Enterprise Payment Solutions</p>
    </div>

    <!-- Service Overview -->
    <div class="info-section">
        <h3 class="section-title">
            <i class="fas fa-rocket me-2"></i>What is XtraPay Business?
        </h3>
        <p class="section-text">
            XtraPay Business is a premium enterprise payment processing solution designed for high-volume businesses. 
            We provide secure, fast, and reliable transaction processing with custom integration options, 
            real-time analytics, and dedicated support for businesses that process significant transaction volumes.
        </p>
    </div>

    <!-- Who Should Apply -->
    <div class="info-section">
        <h3 class="section-title">
            <i class="fas fa-users me-2"></i>Who Should Apply?
        </h3>
        <div class="features-grid">
            <div class="feature-item">
                <i class="fas fa-check-circle feature-icon"></i>
                <div>
                    <strong>High-Volume Businesses</strong>
                    <small>Processing 400-1000+ transactions monthly</small>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-check-circle feature-icon"></i>
                <div>
                    <strong>Established Companies</strong>
                    <small>With proven business track record</small>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-check-circle feature-icon"></i>
                <div>
                    <strong>E-commerce Platforms</strong>
                    <small>Online stores and marketplaces</small>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-check-circle feature-icon"></i>
                <div>
                    <strong>SaaS Companies</strong>
                    <small>Subscription-based services</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Requirements -->
    <div class="info-section">
        <h3 class="section-title">
            <i class="fas fa-chart-line me-2"></i>Minimum Requirements
        </h3>
        <div class="alert-box">
            <i class="fas fa-exclamation-triangle alert-icon"></i>
            <div>
                <strong>Transaction Volume:</strong>
                <p>Your website must process <strong>400-1000 transactions per month</strong> to qualify for our service. 
                This ensures we can provide the level of support and infrastructure your business requires.</p>
            </div>
        </div>
    </div>

    <!-- Service Features -->
    <div class="info-section">
        <h3 class="section-title">
            <i class="fas fa-shield-alt me-2"></i>What You Get
        </h3>
        <div class="features-grid">
            <div class="feature-item">
                <i class="fas fa-bolt feature-icon"></i>
                <div>
                    <strong>Fast Processing</strong>
                    <small>Lightning-fast transaction processing with 99.9% uptime</small>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-lock feature-icon"></i>
                <div>
                    <strong>Bank-Level Security</strong>
                    <small>PCI DSS compliant with encryption</small>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-cogs feature-icon"></i>
                <div>
                    <strong>Custom Integration</strong>
                    <small>API integration tailored to your needs</small>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-chart-bar feature-icon"></i>
                <div>
                    <strong>Real-Time Analytics</strong>
                    <small>Detailed reporting and insights</small>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-headset feature-icon"></i>
                <div>
                    <strong>Dedicated Support</strong>
                    <small>24/7 technical support team</small>
                </div>
            </div>
            <div class="feature-item">
                <i class="fas fa-handshake feature-icon"></i>
                <div>
                    <strong>Transparent Pricing</strong>
                    <small>No hidden fees, clear cost structure</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing -->
    <div class="info-section">
        <h3 class="section-title">
            <i class="fas fa-dollar-sign me-2"></i>Pricing & Fees
        </h3>
        <div class="alert-box success">
            <i class="fas fa-check-circle alert-icon"></i>
            <div>
                <strong>Deployment Fee:</strong>
                <p>Deployment fees will be communicated via email after application review. 
                <strong>No hidden fees - just straight up charges.</strong> We provide transparent pricing 
                with no surprises or additional costs.</p>
            </div>
        </div>
    </div>

    <!-- Application Process -->
    <div class="info-section">
        <h3 class="section-title">
            <i class="fas fa-clipboard-check me-2"></i>Application Process
        </h3>
        <div class="process-steps">
            <div class="step-item">
                <span class="step-number">1</span>
                <div>
                    <strong>Submit Application</strong>
                    <small>Send email with business details</small>
                </div>
            </div>
            <div class="step-item">
                <span class="step-number">2</span>
                <div>
                    <strong>Review Process</strong>
                    <small>We evaluate your business needs</small>
                </div>
            </div>
            <div class="step-item">
                <span class="step-number">3</span>
                <div>
                    <strong>Custom Quote</strong>
                    <small>Receive personalized pricing</small>
                </div>
            </div>
            <div class="step-item">
                <span class="step-number">4</span>
                <div>
                    <strong>Integration</strong>
                    <small>Setup and go live</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Important Notice -->
    <div class="info-section">
        <h3 class="section-title">
            <i class="fas fa-gavel me-2"></i>Important Information
        </h3>
        <div class="alert-box info">
            <i class="fas fa-info-circle alert-icon"></i>
            <div>
                <strong>Service Eligibility:</strong>
                <p>We reserve the right to approve or disapprove your application based on our evaluation criteria. 
                Our service is not for everyone - it's a custom solution for businesses that need fast and secure transactions. 
                We carefully review each application to ensure we can provide the best service for your specific needs.</p>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="info-section">
        <h3 class="section-title">
            <i class="fas fa-envelope me-2"></i>Ready to Apply?
        </h3>
        <p class="section-text">
            If you meet our requirements and are ready to get started, please send us an email with your business details:
        </p>
        <div class="text-center">
            <a href="mailto:support@xtrapay.cash" class="txt2 apply-link">
                <i class="fas fa-envelope me-2"></i>support@xtrapay.cash
            </a>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="container-login100-form-btn">
        <a href="mailto:support@xtrapay.cash" class="login100-form-btn">
            <i class="fas fa-envelope me-2"></i>Apply Now
        </a>
    </div>

    <div class="txt1 text-center p-t-54 p-b-20">
        <span>Ready to create your account?</span>
    </div>

    <div class="flex-col-c p-t-155">
        <span class="txt1 p-b-17">Don't have an account?</span>
        <a href="{{ route('register') }}" class="txt2">Create Account</a>
    </div>

    <div class="txt1 text-center p-t-20">
        <span>Already have an account?</span>
    </div>

    <div class="flex-col-c p-t-20">
        <a href="{{ route('login') }}" class="txt2">Sign In</a>
    </div>
</div>

<style>
/* Mobile-first responsive design */
.container-login100 {
    max-width: 800px;
    width: 100%;
    margin: 0 auto;
    padding: 20px;
}

.info-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: rgba(28, 28, 30, 0.8);
    border: 1px solid rgba(60, 60, 62, 0.5);
    border-radius: 15px;
    backdrop-filter: blur(10px);
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.section-title i {
    color: var(--neon-red);
    margin-right: 0.5rem;
}

.section-text {
    color: var(--text-secondary);
    font-size: 0.95rem;
    line-height: 1.6;
    margin: 0;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    background: rgba(44, 44, 46, 0.5);
    border: 1px solid rgba(60, 60, 62, 0.3);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.feature-item:hover {
    border-color: var(--neon-red);
    transform: translateY(-2px);
}

.feature-icon {
    color: var(--neon-red);
    margin-right: 0.75rem;
    margin-top: 0.25rem;
    font-size: 1.1rem;
}

.feature-item div {
    flex: 1;
}

.feature-item strong {
    color: var(--text-primary);
    font-size: 0.95rem;
    font-weight: 500;
    display: block;
    margin-bottom: 0.25rem;
}

.feature-item small {
    color: var(--text-secondary);
    font-size: 0.85rem;
    line-height: 1.4;
}

.alert-box {
    display: flex;
    align-items: flex-start;
    padding: 1.5rem;
    background: rgba(44, 44, 46, 0.5);
    border: 1px solid rgba(60, 60, 62, 0.3);
    border-radius: 10px;
    margin: 0;
}

.alert-box.success {
    border-color: rgba(16, 185, 129, 0.3);
    background: rgba(16, 185, 129, 0.1);
}

.alert-box.info {
    border-color: rgba(59, 130, 246, 0.3);
    background: rgba(59, 130, 246, 0.1);
}

.alert-icon {
    color: var(--neon-red);
    margin-right: 1rem;
    margin-top: 0.25rem;
    font-size: 1.2rem;
}

.alert-box.success .alert-icon {
    color: #10b981;
}

.alert-box.info .alert-icon {
    color: #3b82f6;
}

.alert-box strong {
    color: var(--text-primary);
    font-size: 0.95rem;
    font-weight: 500;
    display: block;
    margin-bottom: 0.5rem;
}

.alert-box p {
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.6;
    margin: 0;
}

.process-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.step-item {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    background: rgba(44, 44, 46, 0.5);
    border: 1px solid rgba(60, 60, 62, 0.3);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.step-item:hover {
    border-color: var(--neon-red);
    transform: translateY(-2px);
}

.step-number {
    background: linear-gradient(135deg, var(--neon-red), var(--neon-white));
    color: white;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

.step-item div {
    flex: 1;
}

.step-item strong {
    color: var(--text-primary);
    font-size: 0.95rem;
    font-weight: 500;
    display: block;
    margin-bottom: 0.25rem;
}

.step-item small {
    color: var(--text-secondary);
    font-size: 0.85rem;
    line-height: 1.4;
}

.apply-link {
    font-size: 1.1rem;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    background: rgba(255, 59, 48, 0.1);
    border: 1px solid var(--neon-red);
    border-radius: 10px;
    display: inline-block;
    transition: all 0.3s ease;
}

.apply-link:hover {
    background: var(--neon-red);
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 59, 48, 0.4);
}

/* Mobile Responsive Design */
@media (max-width: 768px) {
    .container-login100 {
        max-width: 100% !important;
        padding: 15px;
        margin: 0;
    }
    
    .info-section {
        margin-bottom: 1.5rem;
        padding: 1.25rem;
        border-radius: 12px;
    }
    
    .section-title {
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .section-text {
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .process-steps {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .feature-item {
        padding: 0.875rem;
        border-radius: 8px;
    }
    
    .step-item {
        padding: 0.875rem;
        border-radius: 8px;
    }
    
    .alert-box {
        padding: 1.25rem;
        border-radius: 8px;
    }
    
    .feature-icon {
        font-size: 1rem;
        margin-right: 0.625rem;
    }
    
    .alert-icon {
        font-size: 1.1rem;
        margin-right: 0.875rem;
    }
    
    .step-number {
        width: 1.75rem;
        height: 1.75rem;
        font-size: 0.8rem;
        margin-right: 0.875rem;
    }
    
    .apply-link {
        font-size: 1rem;
        padding: 0.625rem 1.25rem;
    }
    
    .login100-form-title {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }
    
    .txt1 {
        font-size: 0.9rem;
    }
    
    .txt2 {
        font-size: 0.85rem;
    }
}

/* Extra small devices */
@media (max-width: 480px) {
    .container-login100 {
        padding: 12px;
    }
    
    .info-section {
        padding: 1rem;
        margin-bottom: 1.25rem;
    }
    
    .section-title {
        font-size: 0.95rem;
    }
    
    .section-text {
        font-size: 0.85rem;
    }
    
    .feature-item {
        padding: 0.75rem;
    }
    
    .step-item {
        padding: 0.75rem;
    }
    
    .alert-box {
        padding: 1rem;
    }
    
    .apply-link {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
    }
    
    .login100-form-title {
        font-size: 1.25rem;
    }
}
</style>
@endsection 