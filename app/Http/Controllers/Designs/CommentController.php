<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Repositories\Contracts\CommentInterface;
use App\Repositories\Contracts\DesignInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    protected $comments;
    protected $designs;

    public function __construct(CommentInterface $comments, DesignInterface $designs)
    {
        $this->comments = $comments;
        $this->designs = $designs;
    }

    public function store(Request $request, $designId)
    {
        $this->validate($request, [
            'body' => ['required']
        ]);

        $comment = $this->designs->addComment($designId, [
            'body' => $request->body,
            'user_id' => auth()->id()
        ]);

        return new CommentResource($comment);
    }

    public function update(Request $request, $id)
    {
        $comment = $this->comments->find($id);

        $this->authorize('update', $comment);

        Log::info("XDDDDDDDDDDDDDDDDD");

        $this->validate($request, [
            'body' => ['required']
        ]);

        $comment = $this->comments->update($id, [
            'body' => $request->body
        ]);

        return new CommentResource($comment);

    }

    public function destroy(Request $request, $id)
    {
        $comment = $this->comments->find($id);
        $this->authorize('delete', $comment);
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted'
        ], 200);
    }
}
