<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use App\Models\Order;
use App\Models\Profil;
use App\Models\Showcase;
use Illuminate\Support\Facades\Cache;

class PagesController extends Controller
{
    public function home()
    {
        $showcase = Cache::remember('showcase', now()->addMinutes(60), function () {
            return Showcase::select('id', 'img')->get();
        });

        $profil = Cache::remember('profil', now()->addMinutes(60), function () {
            return Profil::select('id', 'name', 'alamat')->get();
        });

        $menus = Cache::remember('menus', now()->addMinutes(60), function () {
            return Menu::select('id', 'name', 'price', 'img')->paginate(10); // Use pagination
        });

        return view('user.home', compact('profil', 'menus', 'showcase'));
    }

    public function antrian()
    {
        $orders = Order::with(['cart.user', 'cart.cartMenus.menu'])->get();
        $statuses = [];

        foreach ($orders as $order) {
            try {
                if ($order->status === 'settlement' && $order->payment_type === 'cash') {
                    $statuses[$order->no_order] = (object) [
                        'status' => $order->status,
                        'bg_color' => 'text-white text-center bg-green-500 w-fit rounded-xl'
                    ];
                    continue; // Skip further processing for this order
                }

                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = true;

                $status = \Midtrans\Transaction::status($order->no_order);

                $order->update([
                    'status' => $status->transaction_status,
                    'payment_type' => $status->payment_type ?? null,
                ]);

                if ($status->transaction_status === 'expire') {
                    $order->delete();
                    continue;
                }

                $statuses[$order->no_order] = (object) [
                    'status' => $status->transaction_status,
                    'bg_color' => $status->transaction_status === 'settlement' ? 'text-white text-center bg-green-500 w-fit rounded-xl' : 'text-white text-center bg-red-500 w-fit rounded-xl'
                ];
            } catch (\Exception $e) {
                $statuses[$order->no_order] = (object) [
                    'status' => 'Error: ' . $e->getMessage(),
                    'bg_color' => 'bg-red-500 w-fit text-white text-center rounded-xl'
                ];
            }
        }

        return view('user.antrian', compact('orders', 'statuses'));
    }

    public function akun()
    {
        $userId = auth()->id();

        $user = Cache::remember("akun_{$userId}", now()->addMinutes(60), function () use ($userId) {
            return User::find($userId); // Fetches the user from the database by ID
        });
        return view('user.akun', compact('user'));
    }
}
