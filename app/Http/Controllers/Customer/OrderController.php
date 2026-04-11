<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function postorder(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'no_telpon' => 'required|string|max:15',
            'atas_nama' => 'required|string|max:255',
            'cabang' => 'required|string|max:255',
            'alamat' => 'nullable',
            'ongkir' => 'nullable',
        ]);

        $cart = $user->carts()->with('cartMenus.menu')->latest()->first();

        if (!$cart || $cart->cartMenus->isEmpty()) {
            return redirect()->route('user-cart')->with('error', 'Your cart is empty.');
        }

        $order = Order::where('cart_id', $cart->id)->first();

        if (!$order) {
            return redirect()->route('user-home');
        }

        $orderId = 'ORDER-' . strtoupper(substr(Uuid::uuid4()->toString(), 0, 8));

        $order->update([
            'no_order' => $orderId,
            'atas_nama' => $request->atas_nama,
            'no_telpon' => $request->no_telpon,
        ]);

        $cart->update(['total_amount' => $cart->total_amount + ($order->ongkir ?? 0)]);

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = true;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $params = array(
            'transaction_details' => array(
                'order_id' => $orderId,
                'gross_amount' => $order->cart->total_amount,
            )
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        $user->carts()->create();

        return view('user.checkout', compact('order', 'snapToken'));
    }

    public function payment(Request $request)
    {
        $user = auth()->user();

        $cart = $user->carts()->with('cartMenus.menu')->latest()->first();

        $order = Order::where('cart_id', $cart->id)->first();

        return view('user.payment', compact('order', 'cart'));
    }
}
