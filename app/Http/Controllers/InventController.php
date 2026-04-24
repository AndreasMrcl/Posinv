<?php

namespace App\Http\Controllers;

use App\Models\Invent;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class InventController extends Controller
{
    public function index()
    {
        $userStore = Auth::user()->store;

        $cacheKey = "invents_{$userStore->id}";

        $invents = Cache::remember($cacheKey, 180, function () use ($userStore) {
            return $userStore->invents()->get();
        });

        return view('invent', compact('invents'));
    }

    public function store(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required',
            'stock' => 'required|integer',
            'min_stock' => 'nullable|integer|min:0',
            'unit' => 'required',
        ]);

        Invent::create([
            'name' => $data['name'],
            'stock' => $data['stock'],
            'min_stock' => $data['min_stock'] ?? 0,
            'unit' => $data['unit'],
            'store_id' => $userStore->id,
        ]);

        $this->clearCache($userStore->id);

        return redirect(route('invent'))->with('success', 'Invent Sukses Dibuat !');
    }

    public function update(Request $request, $id)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'name' => 'required',
            'stock' => 'required|integer',
            'min_stock' => 'nullable|integer|min:0',
            'unit' => 'required',
        ]);

        $invent = Invent::where('id', $id)
            ->where('store_id', $userStore->id)
            ->firstOrFail();

        $invent->update([
            'name' => $data['name'],
            'stock' => $data['stock'],
            'min_stock' => $data['min_stock'] ?? 0,
            'unit' => $data['unit'],
        ]);

        $this->clearCache($userStore->id);

        return redirect(route('invent'))->with('success', 'Invent Sukses Diupdate !');
    }

    public function receive(Request $request)
    {
        $userStore = Auth::user()->store;

        $data = $request->validate([
            'invent_id' => 'required|exists:invents,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $invent = Invent::where('id', $data['invent_id'])
            ->where('store_id', $userStore->id)
            ->firstOrFail();

        DB::transaction(function () use ($invent, $data, $userStore) {
            $invent->increment('stock', $data['quantity']);

            StockMovement::create([
                'store_id' => $userStore->id,
                'invent_id' => $invent->id,
                'user_id' => Auth::id(),
                'quantity' => $data['quantity'],
                'type' => 'receive',
                'notes' => $data['notes'] ?? "Penerimaan {$invent->name}",
            ]);
        });

        $this->clearCache($userStore->id);

        return redirect(route('invent'))->with('success', "Penerimaan {$data['quantity']} {$invent->unit} {$invent->name} berhasil dicatat!");
    }

    public function destroy($id)
    {
        $userStore = Auth::user()->store;

        $invent = Invent::where('id', $id)
            ->where('store_id', $userStore->id)
            ->first();

        if (! $invent) {
            return redirect(route('invent'))->withErrors(['msg' => 'Invent tidak ditemukan.']);
        }

        $invent->delete();

        $this->clearCache($userStore->id);

        return redirect(route('invent'))->with('success', 'Invent Berhasil Dihapus !');
    }

    private function clearCache($storeId)
    {
        Cache::forget("invents_{$storeId}");
    }
}
