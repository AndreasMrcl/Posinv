<?php

namespace App\Http\Controllers;

use App\Models\CartMenu;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private function clearCache()
    {
        Cache::forget('menus');

        Cache::forget('categories');

        Cache::forget('categories_with_menus');
    }

    public function index()
    {
        $category = Cache::remember(
            'categories',
            now()->addMinutes(60),
            fn () => Category::with(['menus'])->get()
        );

        return view('product', compact('category'));
    }

    public function create()
    {
        $category = Category::all();

        return view('addproduct', compact('category'));
    }

    public function store(Request $request)
    {
        $userStore = auth()->user()->store;

        $data = $request->validate([
            'name' => 'required',
            'price' => 'required',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'category_id' => 'required',
        ]);

        if ($request->hasFile('img')) {
            $uploadedImage = $request->file('img');
            $imageName = $uploadedImage->getClientOriginalName();
            $imagePath = $uploadedImage->storeAs('img', $imageName, 'public');
            $data['img'] = 'img/'.$imageName;
        }

        $data['store_id'] = $userStore->id;

        Menu::create($data);

        $this->clearCache();

        return redirect(route('product'))->with('success', 'Product Sukses Dibuat !');
    }

    public function show($id)
    {
        $menu = Cache::remember("menu_{$id}", now()->addMinutes(60), function () use ($id) {
            return Menu::find($id);
        });
        $discount = Cache::remember('discounts', now()->addMinutes(60), function () {
            return Discount::all();
        });

        return view('showproduct', compact('menu', 'discount'));
    }

    public function edit($id)
    {
        $category = Category::all();

        $menu = Menu::find($id);

        return view('editproduct', compact('menu', 'category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required',
            'category_id' => 'required',
        ]);

        $menuData = $request->only(['name', 'price', 'img', 'description', 'category_id']);

        if ($request->hasFile('img')) {
            $uploadedImage = $request->file('img');
            $imageName = $uploadedImage->getClientOriginalName();
            $imagePath = $uploadedImage->storeAs('img', $imageName, 'public');
            $menuData['img'] = 'img/'.$imageName;
        }

        Menu::where('id', $id)->update($menuData);

        $this->clearCache();

        return redirect(route('product'))->with('success', 'Product Sukses Diupdate !');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        // hapus relasi cart_menu
        CartMenu::where('menu_id', $id)->delete();

        // hapus file img dari storage
        if ($menu->img && Storage::disk('public')->exists($menu->img)) {
            Storage::disk('public')->delete($menu->img);
        }

        $menu->delete();
        $this->clearCache();

        return redirect()->route('product')->with('success', 'Product berhasil dihapus!');
    }
}
