<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - XtraPay Business</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .security {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>XtraPay Business</h1>
        <p>Reset Your Password</p>
    </div>
    
    <div class="content">
        <h2>Hello!</h2>
        
        <p>You are receiving this email because we received a password reset request for your account.</p>
        
        <div style="text-align: center;">
            <a href="{{ $url }}" class="button">Reset Password</a>
        </div>
        
        <p>If the button doesn't work, you can copy and paste the following link into your browser:</p>
        <p style="word-break: break-all; background: #f1f1f1; padding: 10px; border-radius: 5px;">
            {{ $url }}
        </p>
        
        <div class="warning">
            <strong>Important:</strong> This password reset link will expire in 60 minutes. If you don't reset your password within this time, you'll need to request a new reset link.
        </div>
        
        <div class="security">
            <strong>Security Notice:</strong> If you didn't request a password reset, please ignore this email. Your password will remain unchanged.
        </div>
        
        <p>Best regards,<br>The XtraPay Business Team</p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} XtraPay Business. All rights reserved.</p>
        <p>This email was sent to {{ $email }}</p>
    </div>
</body>
</html> 