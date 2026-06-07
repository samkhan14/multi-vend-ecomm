<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 11px;
            margin: 0;
            padding: 10px;
            line-height: 1.3;
        }
        .header-logo {
            max-height: 40px;
            width: auto;
            margin-bottom: 5px;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto;
            padding: 10px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e44d26;
        }
        .header h1 { 
            color: #e44d26; 
            margin: 0; 
            font-size: 22px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
            color: #333;
        }
        .invoice-info { 
            display: flex; 
            justify-content: space-between; 
            margin-bottom: 15px;
        }
        .customer-info, .store-info { 
            width: 48%; 
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .customer-info h3, .store-info h3 {
            color: #e44d26;
            margin: 0 0 8px 0;
            font-size: 12px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 10px 0;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px;
            text-align: left;
        }
        th { 
            background-color: #f2f2f2; 
            font-weight: bold;
        }
        .total-section { 
            margin-top: 15px; 
            text-align: right; 
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .total-section h3 {
            color: #e44d26;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            margin-top: 8px;
        }
        .footer { 
            margin-top: 20px; 
            text-align: center; 
            font-size: 10px; 
            color: #666; 
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .order-status {
            display: inline-block;
            padding: 3px 10px;
            background: #28a745;
            color: white;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            margin-left: 5px;
        }
        .product-name {
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
        }
        .variant-info {
            font-size: 9px;
            color: #666;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if($siteSetting && $siteSetting->website_logo)
                <div style="text-align: center; margin-bottom: 5px;">
                    <img src="{{ public_path('storage/' . $siteSetting->website_logo) }}" 
                         class="header-logo" 
                         alt="{{ config('app.name') }}">
                </div>
            @else
                <h1>{{ config('app.name') }}</h1>
            @endif
            <h2>INVOICE 
                <span class="order-status">{{ strtoupper($order->status) }}</span>
            </h2>
            <p><strong>Invoice #{{ $order->order_number }}</strong> | Date: {{ $order->created_at->format('F d, Y') }}</p>
        </div>
        
        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="customer-info">
                <h3>Bill To:</h3>
                <p><strong>{{ $order->name }}</strong></p>
                <p>{{ $order->address }}</p>
                <p>{{ $order->city }}, {{ $order->state }}, {{ $order->country }} - {{ $order->pincode }}</p>
                <p> {{ $order->mobile }} |  {{ $order->email }}</p>
            </div>
            
            <div class="store-info">
                <h3>From:</h3>
                <p><strong>{{ config('app.name') }}</strong></p>
                <p>{{ $genralsetting->address ?? 'Your Store Address' }}</p>
                <p>{{ $genralsetting->city ?? 'City' }}, {{ $genralsetting->state ?? 'State' }}, {{ $genralsetting->country ?? 'Country' }}</p>
                <p> {{ $genralsetting->phone ?? '+92 123 456 7890' }}</p>
                <p>{{ $genralsetting->email ?? 'info@example.com' }}</p>
            </div>
        </div>
        
        <!-- Order Items -->
        <h3 style="margin: 10px 0 5px;">ORDER ITEMS</h3>
        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="50%">Product Name</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="15%" class="text-right">Unit Price</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <span class="product-name">{{ $item->product_name }}</span>
                        @if($item->variant_name)
                            <span class="variant-info">Variant: {{ $item->variant_name }}</span>
                        @endif
                        @if($item->variant_attributes)
                            @php $attrs = json_decode($item->variant_attributes, true) @endphp
                            @if($attrs)
                                <span class="variant-info">
                                    @foreach($attrs as $key => $value)
                                        {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                                    @endforeach
                                </span>
                            @endif
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">
                        {{ getUserCurrency() }} {{ number_format($item->price, 2) }}
                    </td>
                    <td class="text-right">
                        {{ getUserCurrency() }} {{ number_format($item->subtotal, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="total-section">
            <div style="margin-bottom: 3px;">
                <span>Subtotal:</span>
                <span style="float: right;">
                    {{ getUserCurrency() }} {{ number_format($order->subtotal, 2) }}
                </span>
            </div>
            
            @if($order->shipping_charges > 0)
            <div style="margin-bottom: 3px;">
                <span>Shipping:</span>
                <span style="float: right;">
                    {{ getUserCurrency() }} {{ number_format($order->shipping_charges, 2) }}
                </span>
            </div>
            @else
            <div style="margin-bottom: 3px; color: #28a745;">
                <span>Shipping:</span>
                <span style="float: right; color: #28a745;">FREE</span>
            </div>
            @endif
            
            @if($order->coupon_amount > 0)
            <div style="margin-bottom: 3px; color: #28a745;">
                <span>Discount ({{ $order->coupon_code }}):</span>
                <span style="float: right; color: #28a745;">
                    -{{ getUserCurrency() }} {{ number_format($order->coupon_amount, 2) }}
                </span>
            </div>
            @endif
            
            <h3>
                <strong>GRAND TOTAL:</strong>
                <span style="float: right;">
                    {{ getUserCurrency() }} {{ number_format($order->grand_total, 2) }}
                </span>
            </h3>
            
            <div style="margin-top: 8px; padding-top: 5px; border-top: 1px dashed #ddd; font-size: 9px;">
                <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>
                <p><strong>Invoice Generated:</strong> {{ now()->format('F d, Y h:i A') }}</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p>This is a computer-generated invoice.</p>
            <p>For inquiries: {{ $siteSetting->email ?? 'support@example.com' }} | {{ $siteSetting->phone ?? '+92 123 456 7890' }}</p>
        </div>
    </div>
</body>
</html>