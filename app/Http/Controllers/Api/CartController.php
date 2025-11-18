<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Item;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * GET /api/carts
     * List keranjang milik user yang sedang login.
     */
    public function index(Request $request)
    {
        $carts = Cart::with('item')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json($carts);
    }

    /**
     * POST /api/carts
     * Tambah item ke keranjang (atau update quantity kalau sudah ada).
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_id'  => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'notes'    => 'nullable|string|max:255',
        ]);

        $userId = $request->user()->id;

        // cek item ada
        $item = Item::findOrFail($request->item_id);

        // cari apakah sudah ada baris cart user+item ini
        $cart = Cart::where('user_id', $userId)
            ->where('item_id', $item->id)
            ->first();

        if ($cart) {
            // kalau sudah ada → update quantity (tambah)
            $cart->quantity += $request->quantity;
            $cart->notes = $request->notes ?? $cart->notes;
            $cart->save();
        } else {
            // kalau belum ada → buat baru
            $cart = Cart::create([
                'user_id'  => $userId,
                'item_id'  => $item->id,
                'quantity' => $request->quantity,
                'notes'    => $request->notes,
            ]);
        }

        return response()->json([
            'message' => 'Item berhasil ditambahkan ke keranjang',
            'data'    => $cart->load('item'),
        ], 201);
    }

    /**
     * GET /api/carts/{id}
     * Detail 1 item cart (cek kepemilikan).
     */
    public function show(Request $request, string $id)
    {
        $cart = Cart::with('item')->findOrFail($id);

        if ($cart->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Tidak boleh mengakses cart milik user lain',
            ], 403);
        }

        return response()->json($cart);
    }

    /**
     * PUT/PATCH /api/carts/{id}
     * Update quantity/notes di keranjang.
     */
    public function update(Request $request, string $id)
    {
        $cart = Cart::findOrFail($id);

        if ($cart->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Tidak boleh mengubah cart milik user lain',
            ], 403);
        }

        $request->validate([
            'quantity' => 'sometimes|required|integer|min:1',
            'notes'    => 'sometimes|nullable|string|max:255',
        ]);

        $cart->update($request->only(['quantity', 'notes']));

        return response()->json([
            'message' => 'Cart berhasil diupdate',
            'data'    => $cart->load('item'),
        ]);
    }

    /**
     * DELETE /api/carts/{id}
     * Hapus item dari keranjang.
     */
    public function destroy(Request $request, string $id)
    {
        $cart = Cart::findOrFail($id);

        if ($cart->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Tidak boleh menghapus cart milik user lain',
            ], 403);
        }

        $cart->delete();

        return response()->json([
            'message' => 'Item berhasil dihapus dari keranjang',
        ]);
    }
}
