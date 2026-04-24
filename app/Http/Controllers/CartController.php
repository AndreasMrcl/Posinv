<?php

namespace App\Http\Controllers;

use App\Models\CartMenu;
use App\Models\Discount;
use App\Models\Menu;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $menus = Menu::all();

        return view('addcart', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'discount_id' => 'nullable|exists:discounts,id',
        ]);

        $user = auth()->user();
        $storeId = $user->store->id;
        $cart = $user->carts()->latest()->first()
            ?? $user->carts()->create([
                'store_id' => $storeId,
                'total_amount' => 0,
            ]);

        $menu = Menu::findOrFail($request->menu_id);
        $quantity = (int) $request->quantity;

        $insufficient = app(InventoryService::class)->canFulfillCart($cart, $menu, $quantity);

        if (! empty($insufficient)) {
            return redirect()->back()->with('error', 'Stok bahan tidak cukup: '.implode(', ', $insufficient));
        }

        $subtotal = $menu->price * $quantity;

        // Hitung discount jika ada
        $discount = $request->discount_id
            ? Discount::find($request->discount_id)
            : null;

        if ($discount) {
            $discountAmount = $subtotal * ($discount->percentage / 100);
            $subtotal -= $discountAmount;
        }

        // Cari apakah item dengan kondisi sama sudah ada
        $existingCartMenu = CartMenu::where([
            'cart_id' => $cart->id,
            'menu_id' => $menu->id,
            'notes' => $request->notes,
            'discount_id' => $discount?->id,
        ])->first();

        if ($existingCartMenu) {
            $existingCartMenu->increment('quantity', $quantity);
            $existingCartMenu->increment('subtotal', $subtotal);
        } else {
            CartMenu::create([
                'store_id' => $storeId,
                'cart_id' => $cart->id,
                'menu_id' => $menu->id,
                'quantity' => $quantity,
                'notes' => $request->notes,
                'subtotal' => $subtotal,
                'discount_id' => $discount?->id,
            ]);
        }

        $cart->increment('total_amount', $subtotal);

        return redirect()->route('addorder');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $cart = $user->carts()->latest()->first();

        $cartMenu = CartMenu::where('id', $id)
            ->where('cart_id', $cart->id)
            ->firstOrFail();

        $subtotal = $cartMenu->subtotal;
        $cartMenu->delete();

        $cart->decrement('total_amount', $subtotal);

        return redirect()->route('addorder');
    }
}
