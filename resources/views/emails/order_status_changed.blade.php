<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Update</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f4f7;
            padding: 20px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        .header {
            background-color: #1a73e8;
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-width: 120px;
            height: auto;
        }
        .content {
            padding: 25px;
        }
        h1 {
            font-size: 20px;
            color: #1a73e8;
            margin: 0 0 15px;
            font-weight: 600;
        }
        p {
            font-size: 15px;
            line-height: 1.5;
            margin: 0 0 15px;
        }
        .order-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .order-info span {
            font-weight: 600;
            color: #1a73e8;
        }
        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            text-transform: capitalize;
        }
        .status-pending { background: #ffe082; color: #e65100; }
        .status-processing { background: #bbdefb; color: #1976d2; }
        .status-completed { background: #c8e6c9; color: #2e7d32; }
        .status-canceled { background: #ffcdd2; color: #c62828; }
        .cta {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1a73e8;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            margin-top: 15px;
        }
        .ii a[href] {
            color: #ffffff;
        }
        .cta:hover {
            background-color: #1557b0;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666666;
            background: #fafafa;
        }
        .footer a {
            color: #1a73e8;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <table class="wrapper" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table class="container" cellpadding="0" cellspacing="0">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            {{-- <img src="https://via.placeholder.com/120x40?text=MyApp" alt="MyApp Logo"> --}}
                            MyApp
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td class="content">
                            <h1>Order Status Update</h1>
                            <p>Dear {{ $order->user->name }},</p>
                            <p>We’re pleased to inform you that your order has been updated. Here are the details:</p>
                            <div class="order-info">
                                <p>Order ID: <span>#{{ $order->id }}</span></p>
                                <p>Status: <span class="status status-{{ strtolower($order->state) }}">{{ $order->state }}</span></p>
                            </div>
                            <p>For more information, you can view your order details by clicking the button below.</p>
                            <a href="{{ url('/history') }}" class="cta">View Order Details</a>
                            <p>If you have any questions, feel free to reach out to our support team.</p>
                            <p>Thank you for choosing MyApp!</p>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <p>&copy; {{ date('Y') }} MyApp. All rights reserved.</p>
                            <p><a href="{{ url('/support') }}">Contact Support</a> | <a href="{{ url('/unsubscribe') }}">Unsubscribe</a></p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>