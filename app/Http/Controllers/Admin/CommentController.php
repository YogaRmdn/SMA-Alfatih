<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::query()
            ->with('news')
            ->when(request('status') === 'pending', fn ($q) => $q->where('is_approved', false))
            ->when(request('status') === 'approved', fn ($q) => $q->where('is_approved', true))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.comments.index', compact('comments'));
    }

    public function approve(Request $request, Comment $comment)
    {
        $comment->update(['is_approved' => $request->boolean('approve', true)]);

        return back()->with('success', 'Komentar berhasil diubah.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
