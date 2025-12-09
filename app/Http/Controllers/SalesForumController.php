<?php

namespace App\Http\Controllers;

use App\Models\SalesForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SalesForumController extends Controller
{
    /**
     * Display a listing of the sales forum posts.
     */
    public function index(Request $request)
    {
        $query = SalesForumPost::active()->latest();

        // Filter by category if provided
        if ($request->has('category') && $request->category !== 'all') {
            $query->byCategory($request->category);
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $posts = $query->paginate(12);
        $categories = ['umum', 'produk', 'layanan', 'lowongan'];

        return view('sales_forum.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $categories = ['umum', 'produk', 'layanan', 'lowongan'];
        return view('sales_forum.create', compact('categories'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'price' => 'nullable|numeric|min:0',
            'category' => 'required|in:umum,produk,layanan,lowongan',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('sales_forum', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'active';

        SalesForumPost::create($validated);

        return redirect()->route('sales_forum.index')
                        ->with('success', 'Post penjualan berhasil dibuat! 🎉');
    }

    /**
     * Display the specified post.
     */
    public function show(SalesForumPost $salesForum)
    {
        // Increment views
        $salesForum->increment('views');

        return view('sales_forum.show', compact('salesForum'));
    }

    /**
     * Show the form for editing the post.
     */
    public function edit(SalesForumPost $salesForum)
    {
        // Check ownership
        if ($salesForum->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $categories = ['umum', 'produk', 'layanan', 'lowongan'];
        return view('sales_forum.edit', compact('salesForum', 'categories'));
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, SalesForumPost $salesForum)
    {
        // Check ownership
        if ($salesForum->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'price' => 'nullable|numeric|min:0',
            'category' => 'required|in:umum,produk,layanan,lowongan',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($salesForum->image) {
                Storage::disk('public')->delete($salesForum->image);
            }
            $imagePath = $request->file('image')->store('sales_forum', 'public');
            $validated['image'] = $imagePath;
        }

        $salesForum->update($validated);

        return redirect()->route('sales_forum.show', $salesForum)
                        ->with('success', 'Post berhasil diperbarui! ✏️');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(SalesForumPost $salesForum)
    {
        // Check ownership
        if ($salesForum->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Delete image if exists
        if ($salesForum->image) {
            Storage::disk('public')->delete($salesForum->image);
        }

        $salesForum->delete();

        return redirect()->route('sales_forum.index')
                        ->with('success', 'Post berhasil dihapus! 🗑️');
    }

    /**
     * Mark post as sold
     */
    public function markAsSold(SalesForumPost $salesForum)
    {
        if ($salesForum->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $salesForum->update(['status' => 'sold']);

        return redirect()->route('sales_forum.show', $salesForum)
                        ->with('success', 'Post ditandai sebagai TERJUAL');
    }
}
