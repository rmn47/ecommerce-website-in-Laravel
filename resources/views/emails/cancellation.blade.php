<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME') }} - Order Cancellation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #555;
            line-height: 1.5;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background-color: #dc3545;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ translate('We are sorry to inform you') }}</h1>
        <p>{{ translate('Your order has been cancelled.') }}</p>
        <p>{{ translate('Order Code:') }} <strong>{{ $order->code }}</strong></p>
        <p>{{ translate('If you have any questions, please contact our support.') }}</p>
        <a class="btn" href="{{ env('APP_URL') }}">{{ translate('Go to the website') }}</a>
    </div>
</body>
</html> 