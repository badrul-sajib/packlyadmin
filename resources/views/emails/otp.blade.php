<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One-Time Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f9f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 25px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 100, 0, 0.1);
        }
        .header {
            color: #2e7d32;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .otp-code {
            font-size: 28px;
            font-weight: bold;
            color: #2e7d32;
            text-align: center;
            margin: 25px 0;
            padding: 15px;
            background-color: #e8f5e9;
            border-radius: 5px;
            letter-spacing: 3px;
        }
        .footer {
            margin-top: 25px;
            font-size: 12px;
            color: #777;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            padding-top: 15px;
        }
        .button {
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Your One-Time Password</h2>
        </div>
        <p>Hello,</p>
        <p>You recently requested a One-Time Password for your account. Please use the following OTP to complete your action:</p>
        
        <div class="otp-code">{{ $otp }}</div>
        
        <p>This OTP is valid for 2 minutes. For your security, please don't share this code with anyone.</p>
        <p>If you didn't request this OTP, you can safely ignore this email.</p>
        
        <p>Best regards,</p>
        <p><strong>{{ config('app.name') }} Team</strong></p>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>