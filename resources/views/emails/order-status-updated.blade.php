<!DOCTYPE html>
<html>
<head>
    <title>Your Order Status Has Been Updated</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }
        .email-body {
            padding: 30px;
        }
        .status-update {
            background: #f8f9ff;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 5px solid #4F46E5;
        }
        .status-change {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin: 15px 0;
        }
        .old-status {
            padding: 8px 15px;
            background: #e9ecef;
            border-radius: 20px;
            color: #6c757d;
        }
        .arrow {
            color: #6c757d;
            font-size: 20px;
        }
        .new-status {
            padding: 8px 15px;
            background: #4F46E5;
            color: white;
            border-radius: 20px;
            font-weight: bold;
        }
        .order-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 25px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #eee;
        }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>Order Status Updated</h1>
            <p>Your order status has been changed</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <h2>Hello {{ $order->name }},</h2>
            <p>We wanted to inform you that your order status has been updated:</p>
            
            <!-- Status Update Box -->
            <div class="status-update">
                <p style="margin-top: 0;"><strong>Order #{{ $order->order_number }}</strong></p>
                
                <div class="status-change">
                    <span class="old-status">{{ ucfirst($oldStatus) }}</span>
                    <span class="arrow">→</span>
                    <span class="new-status">{{ ucfirst($newStatus) }}</span>
                </div>
                
                <p style="text-align: center; margin-bottom: 0;">
                    @if($newStatus == 'processing')
                    Your order is now being processed.
                    @elseif($newStatus == 'completed')
                    Your order has been delivered successfully!
                    @elseif($newStatus == 'shipped')
                    Your order is on its way!
                    @elseif($newStatus == 'cancelled')
                    Your order has been cancelled.
                    @else
                    Your order status has been updated.
                    @endif
                </p>
            </div>
            
            <!-- Order Details -->
            <div class="order-info">
                <h3 style="margin-top: 0;">Order Details</h3>
                
                <div class="info-row">
                    <span><strong>Order Date:</strong></span>
                    <span>{{ $order->created_at->format('M d, Y') }}</span>
                </div>
                
                <div class="info-row">
                    <span><strong>Total Amount:</strong></span>
                    <span>{{ $order->currency ?? '$' }}{{ number_format($order->grand_total, 2) }}</span>
                </div>
                
                <div class="info-row">
                    <span><strong>Payment Method:</strong></span>
                    <span>{{ ucfirst($order->payment_method) }}</span>
                </div>
                
                <div class="info-row">
                    <span><strong>Shipping Address:</strong></span>
                    <span>{{ $order->city }}, {{ $order->country }}</span>
                </div>
            </div>
            
            <p style="margin-top: 25px;">
                If you have any questions, please contact our support team.
            </p>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p><strong>{{ config('app.name') }}</strong></p>
            <p>Contact: {{ config('mail.from.address') ?: 'support@example.com' }}</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p style="font-size: 12px; margin-top: 10px;">
                This email was sent to {{ $order->email }}
            </p>
        </div>
    </div>
</body>
</html>