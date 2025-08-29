<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\Order;

class StripeCheckoutController extends Controller
{
    public function checkout(Request $request, Item $item)
    {
        $this->authorize('view', $item);

        $order = Order::create([
            'user_id'        => Auth::id(),
            'item_id'        => $item->id,
            'payment_method' => 'クレジットカード',
            'payment_status' => 'pending',
            'status'         => '決済待ち',
            'shipping_address' => Auth::user()->address ?? '未設定',
        ]);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'client_reference_id' => (string)$order->id,
            'metadata' => [
                'order_id' => (string)$order->id,
                'item_id'  => (string)$item->id,
                'user_id'  => (string)Auth::id(),
            ],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => $item->price * 100,
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('purchase.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('purchase.cancel'),
        ]);

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('items.index')->with('error', 'セッションが見つかりません。');
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $session = \Stripe\Checkout\Session::retrieve($sessionId);

        return redirect()->route('items.index')->with('success', '決済処理が完了しました（反映には数秒かかる場合があります）。');
    }

    public function cancel()
    {
        return redirect()->route('items.index')->with('info', '決済をキャンセルしました。');
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->server('HTTP_STRIPE_SIGNATURE');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response('', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            /** @var \Stripe\Checkout\Session $session */
            $session = $event->data->object;

            // 支払い成功のとき
            if ($session->payment_status === 'paid') {
                $orderId = $session->client_reference_id ?? ($session->metadata->order_id ?? null);
                $paymentIntentId = $session->payment_intent ?? null;

                if ($orderId) {
                    $order = Order::find($orderId);
                    if ($order) {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => '購入完了',
                            'stripe_payment_intent_id' => $paymentIntentId,
                        ]);

                        // 売却フラグがあるならON
                        if (method_exists($order, 'item') && $order->item && $order->item->isFillable('is_sold')) {
                            $order->item->update(['is_sold' => true]);
                        }
                    }
                }
            }
        }

        return response()->json(['received' => true]);
    }
}
