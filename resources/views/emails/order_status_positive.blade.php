<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .header {
            background-color: #f8f8f8;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .content {
            padding: 20px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        .order-details {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .order-details p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{{ $subject }}</h2>
        </div>
        <div class="content">
            <p>Dear {{ $customer_name ?? 'Customer' }},</p>
            
            @if(in_array($status, ['delivered', 'on_the_way', 'paid', 'unpaid', 'ready_for_pickup', 'confirmed']))
                <p>Thank you for choosing Online eccomerce We’re pleased to update you on your order status.</p>
            @endif

            <div class="order-details">
                <h3>Order Details:</h3>
                <p><strong>Order Code:</strong> 20250305-20304167</p>
                <p><strong>Order Date:</strong> 05-03-2025</p>
                <p><strong>Total Amount:</strong> ₹258.58</p>
            </div>

            <p>If you have any questions or need further assistance, please don’t hesitate to contact us at <a href="mailto:yourmail@email.com">yourmail@email.com</a>.</p>
        </div>
        <div class="footer">
            <p>Best regards,<br>Team</p>
        </div>
    </div>
</body>
</html>