<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ServeController extends Controller
{
    public function serve()
    {
        return view('user.serve');
    }

    public function locate()
    {
        $user = auth()->user();

        $cart = $user->carts()->latest()->first();

        $order = Order::where('cart_id', $cart->id)->first();

        return view('user.locate', compact('order'));
    }

    public function postDineIn()
    {
        $user = auth()->user();
        $cabang = auth()->user()->store->location;
        $cart = $user->carts()->with('user')->latest()->first();

        if (!$cart) {
            return redirect()->back()->with('error', 'No cart found.');
        }

        $order = Order::where('cart_id', $cart->id)->first();

        if ($order) {
            $order->layanan = 'dineIn';
            $order->alamat = null;
            $order->ongkir = null;
            $order->save();
        } else {
            $order = new Order;
            $order->store_id = $user->store_id;
            $order->cart_id = $cart->id;
            $order->cabang = $cabang;
            $order->layanan = 'dineIn';
            $order->alamat = null;
            $order->ongkir = null;
            $order->save();
        }

        return redirect()->route('user-payment');
    }

    public function postDelivery(Request $request)
    {
        $user = auth()->user();
        $cabang = auth()->user()->store->location;
        $cart = $user->carts()->with('user')->latest()->first();

        if (! $cart) {
            return redirect()->back()->with('error', 'No cart found.');
        }

        $order = Order::where('cart_id', $cart->id)->first();

        if ($order) {
            $order->layanan = 'delivery';
            $order->save();
        } else {
            $order = new Order;
            $order->store_id = $user->store_id;
            $order->cart_id = $cart->id;
            $order->cabang = $cabang;
            $order->layanan = 'delivery';
            $order->save();
        }

        return redirect()->route('user-locate');
    }

    public function ongkir(Request $request)
    {
        $user = auth()->user();
        $cart = $user->carts()->with('user')->latest()->first();

        $order = Order::where('cart_id', $cart->id)->first();

        if (!$order) {
            return redirect()->back()->with('error', 'No order found for the cart.');
        }

        $order->alamat = $request->input('alamat');
        $order->ongkir = $request->input('ongkir');
        $order->save();

        return redirect()->route('user-payment');
    }
}
