<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Order;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook as StripeWebhook;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    /** 購入画面 */
    public function create(int $id)
    {
        $item = Item::findOrFail($id);
        $selected = session("purchase.$id.payment_method");
        return view('orders.create', compact('item', 'selected'));
    }

    /** 支払い方法の保存 */
    public function savePaymentMethod(Request $request, int $id)
    {
        $request->validate([
            'payment_method' => 'required|in:クレジットカード,コンビニ払い,銀行振込',
        ]);

        session(["purchase.$id.payment_method" => $request->payment_method]);

        return response()->json([
            'ok' => true,
            'payment_method' => $request->payment_method,
        ]);
    }

    /** 住所編集画面 */
    public function editAddress(int $id)
    {
        $item = Item::findOrFail($id);
        $user = Auth::user();
        return view('orders.address', compact('item', 'user'));
    }

    /** 住所更新 */
    public function updateAddress(Request $request, int $id)
    {
        $request->validate([
            'postal_code' => 'required|string|max:10',
            'address'     => 'required|string|max:255',
            'building'    => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $user->update([
            'postal_code' => $request->postal_code,
            'address'     => $request->address,
            'building'    => $request->building,
        ]);

        return redirect()
            ->route('purchase.create', ['id' => $id])
            ->with('status', '住所を更新しました');
    }

    public function store(Request $request, int $id)
{
    $validated = $request->validate([
        'payment_method' => 'required|in:クレジットカード,コンビニ払い',
    ]);

    DB::transaction(function () use ($id, $validated) {
        $item = Item::where('id', $id)->lockForUpdate()->firstOrFail();

        if (property_exists($item, 'is_sold') && (int) $item->is_sold === 1) {
            abort(409, 'この商品はすでに購入されています。');
        }

        Order::create([
            'user_id'        => Auth::id(),
            'item_id'        => $item->id,
            'total_amount'   => $item->price,
            'payment_method' => $validated['payment_method'],
            'shipping_address' => Auth::user()->address ?? null,
            'status'         => '購入完了',
        ]);

        // 売切フラグ
        if ($item->isFillable('is_sold')) {
            $item->update(['is_sold' => 1]);
        }

        session()->forget("purchase.{$id}.payment_method");
    });

    return redirect()->route('items.index')
        ->with('success', '購入が完了しました。');
}

    public function createPaymentIntent(Request $request, Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        Stripe::setApiKey(config('services.stripe.secret'));

        $types = $order->payment_method === 'コンビニ払い' ? ['konbini'] : ['card'];

        $pi = PaymentIntent::create([
            'amount' => $order->price,
            'currency' => 'jpy',
            'payment_method_types' => $types,
            'metadata' => [
                'order_id' => $order->id,
                'user_id'  => $order->user_id,
                'item_id'  => $order->item_id,
            ],
        ]);

        $order->stripe_payment_intent_id = $pi->id;
        $order->save();

        return response()->json(['ok' => true]);
    }

    public function webhook(Request $request)
{
    $secret   = config('services.stripe.webhook_secret');
    $payload  = $request->getContent();
    $sig      = $request->header('Stripe-Signature');

    try {
        if (empty($secret)) {
            Log::warning('stripe.webhook.missing_secret');
            return response('Missing secret', 400);
        }
        $event = StripeWebhook::constructEvent($payload, $sig, $secret);
    } catch (\UnexpectedValueException $e) {
        Log::warning('stripe.webhook.invalid_payload', ['msg'=>$e->getMessage()]);
        return response('Invalid payload', 400);
    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        Log::warning('stripe.webhook.invalid_signature', ['msg'=>$e->getMessage()]);
        return response('Invalid signature', 400);
    } catch (\Throwable $e) {
        Log::error('stripe.webhook.construct_error', ['msg'=>$e->getMessage()]);
        return response('Error', 400);
    }

    $type = $event->type ?? null;
    if (!in_array($type, [
        'payment_intent.succeeded',
        'payment_intent.payment_failed',
        'payment_intent.canceled',
        'payment_intent.processing',
    ], true)) {
        return response()->json(['ok'=>true]);
    }

    $piId = $event->data->object->id ?? null;
    if (!$piId) return response()->json(['ok'=>true]);

    $order = Order::where('stripe_payment_intent_id', $piId)->first();
    if (!$order) {
        Log::info('stripe.webhook.order_not_found', ['pi'=>$piId, 'evt'=>$event->id ?? null]);
        return response()->json(['ok'=>true]);
    }

    $map = [
        'payment_intent.succeeded'      => ['succeeded','購入完了'],
        'payment_intent.payment_failed' => ['failed','エラー'],
        'payment_intent.canceled'       => ['canceled','キャンセル'],
        'payment_intent.processing'     => ['processing','入金待ち'],
    ];
    [$ps,$disp] = $map[$type];

    if ($order->payment_status !== $ps || $order->status !== $disp) {
        $order->payment_status = $ps;
        $order->status         = $disp;
        $order->save();
        Log::info('stripe.webhook.order_updated', ['order_id'=>$order->id,'pi'=>$piId,'status'=>$ps]);
    }

    return response()->json(['ok'=>true]);
}
}
