<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UmkmProduct;

class UmkmProductController extends Controller
{
    public function index()
    {
        $products = UmkmProduct::where('user_id', auth()->id())->get();
        return view('umkm.products.index', compact('products'));
    }

    public function create()
    {
        return view('umkm.products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
        ]);
        $data['user_id'] = auth()->id();
        UmkmProduct::create($data);
        return redirect()->route('umkm.products.index')->with('success', 'Produk dibuat.');
    }

    public function edit(UmkmProduct $product)
    {
        $this->authorize('update', $product);
        return view('umkm.products.edit', compact('product'));
    }

    public function update(Request $request, UmkmProduct $product)
    {
        $this->authorize('update', $product);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
        ]);
        $product->update($data);
        return redirect()->route('umkm.products.index')->with('success', 'Produk diperbarui.');
    }

    public function destroy(UmkmProduct $product)
    {
        $this->authorize('delete', $product);
        $product->delete();
        return back()->with('success', 'Produk dihapus.');
    }

    public function show(UmkmProduct $product)
    {
        $this->authorize('view', $product);
        return view('umkm.products.show', compact('product'));
    }
}
