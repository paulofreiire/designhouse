<?php

namespace App\Repositories\Eloquent;

use App\Models\Design;
use App\Repositories\Contracts\DesignInterface;

class DesignRepository extends BaseRepository implements DesignInterface
{
    public function model()
    {
        return Design::class;
    }

    public function applyTags($id, array $data)
    {
        $design = $this->find($id);
        $design->retag($data);
    }

    public function addComment($designId, array $data)
    {
        $design = $this->find($designId);
        $comment = $design->comments()->create($data);
        return $comment;
    }

    public function like($id)
    {
        $design = $this->model->findOrFail($id);
        $design->isLikedByUser(auth()->id()) ? $design->unlike() : $design->like();

    }

    public function isLikedByUser($id)
    {
        $design = $this->model->findOrFail($id);
        return $design->isLikedByUser(auth()->id());
    }


}
