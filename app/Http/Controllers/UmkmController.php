<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UmkmProduct;
use Illuminate\Support\Facades\Storage;

class UmkmController extends Controller
{
    public function index()
    {
        $products = UmkmProduct::where('is_active', true)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('umkm.index', compact('products'));
    }
    
    public function create()
    {
        if (!Auth::user()->isUMKM()) {
            abort(403, 'Hanya pedagang UMKM yang dapat menambah produk');
        }
        
        return view('umkm.create');
    }
    
    public function store(Request $request)
    {
        if (!Auth::user()->isUMKM()) {
            abort(403, 'Hanya pedagang UMKM yang dapat menambah produk');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:1000',
            'stock' => 'required|integer|min:0',
            'category' => 'required|string|max:100',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Handle image upload
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('umkm/products', 'public');
                $imagePaths[] = $path;
            }
        }
        
        UmkmProduct::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category' => $request->category,
            'images' => $imagePaths,
            'is_active' => true,
        ]);
        
        return redirect()->route('umkm.index')
            ->with('success', 'Produk berhasil ditambahkan');
    }
    
    public function show($id)
    {
        $product = UmkmProduct::with('user')->findOrFail($id);
        
        // Increment views
        $product->increment('views');
        
        // Related products
        $relatedProducts = UmkmProduct::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();
        
        return view('umkm.show', compact('product', 'relatedProducts'));
    }
}