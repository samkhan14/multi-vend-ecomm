<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        .order-info {
            padding: 25px;
            border-bottom: 1px solid #eeeeee;
        }
        .order-number {
            font-size: 22px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
        }
        .order-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
        }
        .meta-item {
            flex: 1;
            min-width: 150px;
        }
        .meta-label {
            font-size: 12px;
            color: #718096;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .meta-value {
            font-size: 15px;
            font-weight: 500;
            color: #2d3748;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            background-color: #e6f7e6;
            color: #28a745;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .shipping-address {
            padding: 25px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin: 20px 25px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
        .address-details {
            line-height: 1.8;
        }
        .address-details strong {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
        }
        .order-items {
            padding: 0 25px 25px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .items-table th {
            background-color: #f8f9fa;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
            font-size: 14px;
        }
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }
        .product-name {
            font-weight: 500;
            color: #2d3748;
        }
        .variant-info {
            font-size: 13px;
            color: #6c757d;
            margin-top: 4px;
        }
        .price-cell {
            font-weight: 500;
            color: #2d3748;
            white-space: nowrap;
        }
        .order-summary {
            padding: 25px;
            background-color: #f8f9fa;
            margin: 20px 25px;
            border-radius: 8px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .summary-row:last-child {
            border-bottom: none;
        }
        .summary-label {
            color: #6c757d;
        }
        .summary-value {
            font-weight: 500;
            color: #2d3748;
        }
        .grand-total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            font-size: 18px;
            font-weight: 600;
            color: #2a4365;
        }
        .discount {
            color: #28a745 !important;
        }
        .email-footer {
            padding: 25px;
            text-align: center;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            margin-top: 30px;
        }
        .store-info {
            margin-bottom: 20px;
        }
        .store-name {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }
        .support-info {
            color: #6c757d;
            font-size: 14px;
            margin-top: 15px;
        }
        .copyright {
            color: #adb5bd;
            font-size: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        @media (max-width: 600px) {
            .email-wrapper {
                width: 100% !important;
            }
            .order-meta {
                flex-direction: column;
                gap: 10px;
            }
            .items-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    @php
        $currency = getUserCurrency();
    @endphp
    <div class="email-wrapper">
        <!-- Header -->
        <div class="email-header">
            <h1>Order Confirmation</h1>
            <p>Thank you for your purchase!</p>
        </div>
        
        <!-- Order Information -->
        <div class="order-info">  
            <div class="order-number">
                Order #{{ $order->order_number }}
            </div>
            
            <div class="order-meta">
                <div class="meta-item">
                    <div class="meta-label">Order Date</div>
                    <div class="meta-value">{{ $order->created_at->format('F d, Y') }}</div>
                </div>
                
                <div class="meta-item">
                    <div class="meta-label">Order Status</div>
                    <div class="meta-value">
                        <span class="status-badge">{{ ucfirst($order->status) }}</span>
                    </div>
                </div>
                
                <div class="meta-item">
                    <div class="meta-label">Payment Method</div>
                    <div class="meta-value">{{ strtoupper($order->payment_method) }}</div>
                </div>
            </div>
        </div>
        
        <!-- Shipping Address -->
        <div class="shipping-address">
            <div class="section-title">Shipping Details</div>
            <div class="address-details">
                <strong>{{ $order->name }}</strong>
                {{ $order->address }}<br>
                {{ $order->city }}, {{ $order->state }} {{ $order->pincode }}<br>
                {{ $order->country }}<br>
                📱 {{ $order->mobile }}<br>
                📧 {{ $order->email }}
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="order-items">
            <div class="section-title">Order Items</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: right;">Price</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div class="product-name">{{ $item->product_name }}</div>
                            @if($item->variant_name)
                                <div class="variant-info">
                                    Variant: {{ $item->variant_name }}
                                </div>
                            @endif
                        </td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;" class="price-cell">
                            {{ $currency }}{{ number_format($item->price, 2) }}
                        </td>
                        <td style="text-align: right;" class="price-cell">
                            {{ $currency }}{{ number_format($item->price * $item->quantity, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Order Summary -->
        <div class="order-summary">
            <div class="section-title">Order Summary</div>
            
            <div class="summary-row">
                <span class="summary-label">Subtotal</span>
                <span class="summary-value">
                    {{ $currency }}{{ number_format($order->subtotal, 2) }}
                </span>
            </div>
            
            <div class="summary-row">
                <span class="summary-label">Shipping Charges</span>
                <span class="summary-value">
                    @if($order->shipping_charges > 0)
                        {{ $currency }}{{ number_format($order->shipping_charges, 2) }}
                    @else
                        FREE
                    @endif
                </span>
            </div>
            
            @if($order->coupon_amount > 0)
            <div class="summary-row">
                <span class="summary-label">Discount ({{ $order->coupon_code }})</span>
                <span class="summary-value discount">
                    -{{ $currency }}{{ number_format($order->coupon_amount, 2) }}
                </span>
            </div>
            @endif
            
            <div class="summary-row grand-total">
                <span>Grand Total</span>
                <span>{{ $currency }}{{ number_format($order->grand_total, 2) }}</span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <div class="store-info">
                <div class="store-name">{{ config('app.name') }}</div>
                <p>We're happy to serve you!</p>
            </div>
            
            <div class="support-info">
                <p>If you have any questions about your order, please contact our customer support.</p>
                <p>📞 Support: {{ config('mail.from.address') ?: 'support@example.com' }}</p>
            </div>
            
            <div class="copyright">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                This email was sent to {{ $order->email }}
            </div>
        </div>
    </div>
</body>
</html>