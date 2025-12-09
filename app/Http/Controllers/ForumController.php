<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ForumPost;
use App\Models\ForumComment;

class ForumController extends Controller
{
    public function index()
    {
        $posts = ForumPost::with('user')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $categories = ForumPost::select('category')
            ->distinct()
            ->pluck('category');
        
        return view('forum.index', compact('posts', 'categories'));
    }
    
    public function create()
    {
        return view('forum.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
        ]);
        
        ForumPost::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'is_pinned' => false,
            'is_closed' => false,
        ]);
        
        return redirect()->route('forum.index')
            ->with('success', 'Diskusi berhasil dibuat');
    }
    
    public function show($id)
    {
        $post = ForumPost::with(['user', 'comments.user'])->findOrFail($id);
        
        // Increment views
        $post->increment('views');
        
        return view('forum.show', compact('post'));
    }
    
    public function comment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        
        $post = ForumPost::findOrFail($id);
        
        if ($post->is_closed) {
            return back()->withErrors(['error' => 'Diskusi telah ditutup']);
        }
        
        ForumComment::create([
            'post_id' => $id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'parent_id' => $request->parent_id,
        ]);
        
        // Update comment count
        $post->increment('comments_count');
        
        return back()->with('success', 'Komentar berhasil ditambahkan');
    }
}