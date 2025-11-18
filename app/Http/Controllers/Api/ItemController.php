<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * GET /api/items
     * List semua item (dengan pagination).
     */
    public function index()
    {
        $items = Item::with('user')
            ->latest()
            ->paginate(10);

        return response()->json($items);
    }

    /**
     * POST /api/items
     * Tambah item baru (ADD).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:191',
            'price'  => 'required|numeric|min:0',
            'stock'  => 'nullable|integer|min:0',
            'category'   => 'nullable|string|max:100',
            'size'       => 'nullable|string|max:50',
            'condition'  => 'nullable|string|max:50',
            'image_url'  => 'nullable|string|max:255',
        ]);

        $item = Item::create([
            'user_id'     => $request->user()->id,  // dari token sanctum
            'name'        => $request->name,
            'description' => $request->description,
            'category'    => $request->category,
            'size'        => $request->size,
            'condition'   => $request->condition ?? 'used',
            'price'       => $request->price,
            'stock'       => $request->stock ?? 1,
            'image_url'   => $request->image_url,
        ]);

        return response()->json([
            'message' => 'Item berhasil dibuat',
            'data'    => $item,
        ], 201);
    }

    /**
     * GET /api/items/{id}
     * Detail item.
     */
    public function show(string $id)
    {
        $item = Item::with('user')->findOrFail($id);

        return response()->json($item);
    }

    /**
     * PUT/PATCH /api/items/{id}
     * Update item.
     */
    public function update(Request $request, string $id)
    {
        $item = Item::findOrFail($id);

        // opsional: hanya pemilik yang boleh update
        if ($item->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Tidak boleh mengubah item milik user lain',
            ], 403);
        }

        $request->validate([
            'name'   => 'sometimes|required|string|max:191',
            'price'  => 'sometimes|required|numeric|min:0',
            'stock'  => 'sometimes|integer|min:0',
            'category'   => 'sometimes|nullable|string|max:100',
            'size'       => 'sometimes|nullable|string|max:50',
            'condition'  => 'sometimes|nullable|string|max:50',
            'image_url'  => 'sometimes|nullable|string|max:255',
        ]);

        $item->update($request->only([
            'name',
            'description',
            'category',
            'size',
            'condition',
            'price',
            'stock',
            'image_url',
        ]));

        return response()->json([
            'message' => 'Item berhasil diupdate',
            'data'    => $item,
        ]);
    }

    /**
     * DELETE /api/items/{id}
     * Hapus item.
     */
    public function destroy(Request $request, string $id)
    {
        $item = Item::findOrFail($id);

        if ($item->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Tidak boleh menghapus item milik user lain',
            ], 403);
        }

        $item->delete();

        return response()->json([
            'message' => 'Item berhasil dihapus',
        ]);
    }
}
