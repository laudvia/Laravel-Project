<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ModeratorCommentController extends Controller
{
    public function index()
    {
        $q = Comment::query()->orderByDesc('created_at');

        // pending only
        if (Schema::hasColumn('comments', 'is_approved')) {
            $q->where('is_approved', false);
        } elseif (Schema::hasColumn('comments', 'approved')) {
            $q->where('approved', false);
        } elseif (Schema::hasColumn('comments', 'status')) {
            $q->where('status', 'pending');
        }

        $comments = $q->paginate(20);

        return view('moderator.comments.index', compact('comments'));
    }

    public function approve(Comment $comment)
    {
        if (Schema::hasColumn('comments', 'is_approved')) {
            $comment->is_approved = true;
        }
        if (Schema::hasColumn('comments', 'approved')) {
            $comment->approved = true;
        }
        if (Schema::hasColumn('comments', 'status')) {
            $comment->status = 'approved';
        }

        $comment->save();

        return back()->with('success', 'Комментарий одобрен.');
    }

    public function reject(Comment $comment)
    {
        // проще всего удалить отклонённый
        $comment->delete();

        return back()->with('success', 'Комментарий отклонён (удалён).');
    }
}
