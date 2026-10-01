<?php

namespace App\Http\Controllers;

use App\Models\VisionBlueprint;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Display the shopping cart / project checkout board.
     */
    public function index(Request $request): View
    {
        $cart = session()->get('neriah_cart', []);
        $items = [];
        $totalContract = 0;
        $totalDp = 0;

        foreach ($cart as $slug => $item) {
            $blueprint = VisionBlueprint::where('slug', $slug)->first();
            if ($blueprint) {
                $contractAmount = $item['contract_amount'] ?? 50000000;
                $dpAmount = $item['dp_amount'] ?? ($contractAmount * 0.50);

                $items[] = [
                    'blueprint' => $blueprint,
                    'slug' => $blueprint->slug,
                    'title' => $blueprint->nama_bisnis ?: $blueprint->client_name,
                    'client_name' => $blueprint->client_name,
                    'email' => $blueprint->email,
                    'target_waktu' => $blueprint->target_waktu ?: '30 Hari Kerja',
                    'contract_amount' => $contractAmount,
                    'dp_amount' => $dpAmount,
                    'added_at' => $item['added_at'] ?? now()->toIso8601String(),
                ];

                $totalContract += $contractAmount;
                $totalDp += $dpAmount;
            }
        }

        return view('cart.index', [
            'items' => $items,
            'totalContract' => $totalContract,
            'totalDp' => $totalDp,
        ]);
    }

    /**
     * Add a vision blueprint / project spec to cart.
     */
    public function add(Request $request, string $slug): RedirectResponse
    {
        $blueprint = VisionBlueprint::where('slug', $slug)->firstOrFail();

        $cart = session()->get('neriah_cart', []);

        $contractAmount = 50000000;
        $dpAmount = $contractAmount * 0.50;

        $cart[$slug] = [
            'slug' => $slug,
            'nama_bisnis' => $blueprint->nama_bisnis ?: $blueprint->client_name,
            'contract_amount' => $contractAmount,
            'dp_amount' => $dpAmount,
            'added_at' => now()->toIso8601String(),
        ];

        session()->put('neriah_cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Spesifikasi proyek "' . ($blueprint->nama_bisnis ?: $blueprint->client_name) . '" berhasil dimasukkan ke Cart!');
    }

    /**
     * Remove item from cart.
     */
    public function remove(Request $request, string $slug): RedirectResponse
    {
        $cart = session()->get('neriah_cart', []);

        if (isset($cart[$slug])) {
            unset($cart[$slug]);
            session()->put('neriah_cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Item berhasil dihapus dari Cart.');
    }

    /**
     * Clear all items in cart.
     */
    public function clear(): RedirectResponse
    {
        session()->forget('neriah_cart');
        return redirect()->route('cart.index')->with('success', 'Cart berhasil dikosongkan.');
    }
}
