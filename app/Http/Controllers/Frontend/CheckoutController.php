<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\SiteSetting;
use App\Services\Checkout\CheckoutService;
use App\Services\Payment\PaymentGatewayManager;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use InvalidArgumentException;

if (! function_exists('getUserCurrency')) {
    require_once app_path('Helpers/FrontendHelper.php');
}

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService,
        private PaymentGatewayManager $paymentGatewayManager,
    ) {}

    public function index()
    {
        $genralsetting = GeneralSetting::first();
        $cartData = $this->checkoutService->getCartData();

        if ($cartData['count'] == 0) {
            return redirect()->route('cart.page')
                ->with('error', 'Your cart is empty!');
        }

        $enabledGateways = $this->paymentGatewayManager->enabled();

        return view('frontend.checkout', compact('genralsetting', 'enabledGateways'));
    }

    public function placeOrder(Request $request)
    {
        try {
            $paymentResult = $this->checkoutService->placeOrder($request);
        } catch (InvalidArgumentException $exception) {
            return redirect()->back()
                ->withErrors(['checkout' => $exception->getMessage()])
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Order failed: '.$e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to place order: '.$e->getMessage());
        }

        if ($paymentResult->type === 'redirect' && $paymentResult->redirectUrl) {
            return redirect()->away($paymentResult->redirectUrl);
        }

        return redirect()->route('checkout.thankyou', $paymentResult->orderNumber)
            ->with('success', 'Order placed successfully!');
    }

    public function thankyou(string $orderNumber)
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        $genralsetting = GeneralSetting::first();

        return view('frontend.order.thankyou', compact('order', 'genralsetting'));
    }

    public function paymentCancel(string $orderNumber)
    {
        $order = Order::query()->where('order_number', $orderNumber)->firstOrFail();

        return redirect()->route('checkout.thankyou', $order->order_number)
            ->with('error', 'Crypto payment was cancelled. Your order is saved as unpaid — you can contact support to complete payment.');
    }

    public function downloadInvoice(string $orderNumber)
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        $genralsetting = GeneralSetting::first();
        $siteSetting = SiteSetting::first();

        $pdf = PDF::loadView('frontend.order.invoice-pdf', compact('order', 'genralsetting', 'siteSetting'));
        $pdf->setPaper('A4');

        return $pdf->download('Invoice-'.$order->order_number.'.pdf');
    }

    public function getCheckoutCart()
    {
        $cartData = $this->checkoutService->getCartData();

        return response()->json([
            'success' => true,
            'count' => $cartData['count'],
            'total' => $cartData['total'],
            'items' => $cartData['items'],
        ]);
    }
}
