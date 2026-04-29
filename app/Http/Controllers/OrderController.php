<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientStockException;
use App\Models\Cart;
use App\Models\History;
use App\Models\Order;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['cart.user', 'cart.chair', 'cart.cartMenus.menu'])->get();
        $statuses = [];

        foreach ($orders as $order) {
            try {
                if ($order->status === 'settlement' && $order->payment_type === 'cash') {
                    $statuses[$order->no_order] = (object) [
                        'status' => $order->status,
                        'bg_color' => 'text-white text-center bg-green-500 w-fit rounded-xl',
                    ];

                    continue; // Skip further processing for this order
                }

                if (! config('midtrans.server_key')) {
                    $statuses[$order->no_order] = (object) [
                        'status' => $order->status ?? 'pending',
                        'bg_color' => 'text-white text-center bg-gray-500 w-fit rounded-xl',
                    ];

                    continue;
                }

                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = true;

                $status = \Midtrans\Transaction::status($order->no_order);

                $order->update([
                    'status' => $status->transaction_status,
                    'payment_type' => $status->payment_type ?? null,
                ]);

                if ($status->transaction_status === 'settlement') {
                    app(InventoryService::class)->consumeForOrder($order);
                }

                if ($status->transaction_status === 'expire') {
                    $order->delete();

                    continue;
                }

                $statuses[$order->no_order] = (object) [
                    'status' => $status->transaction_status,
                    'bg_color' => $status->transaction_status === 'settlement' ? 'text-white text-center bg-green-500 w-fit rounded-xl' : 'text-white text-center bg-red-500 w-fit rounded-xl',
                ];
            } catch (\Exception $e) {
                $statuses[$order->no_order] = (object) [
                    'status' => 'Error: '.$e->getMessage(),
                    'bg_color' => 'bg-red-500 w-fit text-white text-center rounded-xl',
                ];
            }
        }

        return view('order', compact('orders', 'statuses'));
    }

    public function create()
    {
        $user = auth()->user();

        $cart = $user->carts()->latest()->first();

        if (! $cart) {
            $cart = $user->carts()->create(['store_id' => $user->store->id]);
        }

        return view('addorder', compact('cart'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'no_telpon' => 'required|string|max:15', // Adjust the validation rules as necessary
            'atas_nama' => 'required|string|max:255',
        ]);

        $cart = $user->carts()->with('user', 'cartMenus.menu')->latest()->first();

        $orderId = 'ORDER-'.strtoupper(substr(Uuid::uuid4()->toString(), 0, 8));

        $order = new Order;
        $order->store_id = $user->store->id;
        $order->cart_id = $cart->id;
        $order->no_order = $orderId;
        $order->atas_nama = $request->atas_nama;
        $order->no_telpon = $request->no_telpon;
        $order->save();

        $snapToken = null;

        if (config('midtrans.server_key')) {
            \Midtrans\Config::$serverKey = config('midtrans.server_key');
            \Midtrans\Config::$isProduction = true;
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $items = $cart->cartMenus->map(function ($cartMenu) {
                return [
                    'id' => $cartMenu->menu_id,
                    'price' => (int) $cartMenu->menu->price,
                    'quantity' => (int) $cartMenu->quantity,
                    'name' => $cartMenu->menu->name,
                ];
            })->toArray();

            $billing_address = [
                'first_name' => $request->atas_nama,
                'last_name' => '',
                'address' => $request->alamat ?? 'N/A',
                'city' => 'N/A',
                'postal_code' => 'N/A',
                'phone' => $request->no_telpon,
                'country_code' => 'IDN',
            ];

            $shipping_address = $billing_address;

            $customer_details = [
                'first_name' => $request->atas_nama,
                'last_name' => '',
                'email' => $user->email,
                'phone' => $request->no_telpon,
                'billing_address' => $billing_address,
                'shipping_address' => $shipping_address,
            ];

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $cart->total_amount + ($request->ongkir ?? 0),
                ],
                'item_details' => $items,
                'customer_details' => $customer_details,
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);
        }

        $user->carts()->create(['store_id' => $user->store->id]);

        return view('checkout', compact('snapToken', 'order'));
    }

    public function archive($orderId)
    {
        $order = Order::find($orderId);

        $user = auth()->user();

        $settlement = $user->settlements()->active()->first();

        if (! $settlement) {
            return redirect()->back()->with('error', 'Buka shift dulu sebelum mengarsipkan order.');
        }

        DB::transaction(function () use ($order, $settlement) {
            $history = new History;
            $history->id = $order->id; // Assuming you want to keep the same ID
            $history->store_id = $settlement->store_id;
            $history->no_order = $order->no_order;
            $history->akun = $order->cart->user->name ?? $order->cart->chair->name ?? '-';
            $history->name = $order->atas_nama;
            $orderDetails = '';

            foreach ($order->cart->cartMenus as $cartMenu) {
                $orderDetails .= $cartMenu->menu->name.' - '.$cartMenu->quantity.' - '.$cartMenu->notes.' - ';
            }

            $history->order = $orderDetails;
            $history->total_amount = $order->cart->total_amount;
            $history->status = $order->status;
            $history->payment_type = $order->payment_type;
            $history->settlement_id = $settlement->id; // Set the settlement_id

            $history->save();

            Cache::forget('history');
            Cache::remember('history', now()->addMinutes(60), function () {
                return History::all();
            });

            $totalHistoryAmount = $settlement->histories()->sum('total_amount');
            $settlement->expected = $totalHistoryAmount + $settlement->start_amount;
            $settlement->save();

            foreach ($order->cart->cartMenus as $cartMenu) {
                $cartMenu->delete();
            }

            $order->cart->delete();

            $order->delete();
        });

        return redirect()->back()->with('success', 'Order archived successfully');
    }

    public function destroy($id)
    {
        Order::destroy($id);

        return redirect(route('order'))->with('success', 'Order Berhasil Dihapus !');
    }

    public function cashpayment(Request $request)
    {
        $orderId = $request->input('order_id');
        $order = Order::find($orderId);

        if (! $order) {
            return redirect()->route('order')->with('error', 'Cash payment failed!');
        }

        try {
            DB::transaction(function () use ($order) {
                $order->status = 'settlement';
                $order->payment_type = 'cash';
                $order->save();

                app(InventoryService::class)->consumeForOrder($order, strict: true);

                $newCart = new Cart;
                $newCart->store_id = $order->store_id;
                $newCart->user_id = $order->cart->user_id;
                $newCart->save();
            });

            return redirect()->route('order')->with('success', 'Cash payment successful!');
        } catch (InsufficientStockException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
