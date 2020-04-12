<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Repositories\Contracts\DesignInterface;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use App\Repositories\Eloquent\Criteria\IsLive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class DesignController extends Controller
{

    protected $designs;

    public function __construct(DesignInterface $designs)
    {
        $this->designs = $designs;
    }

    public function index()
    {
        $designs = $this->designs->withCriteria([
            new EagerLoad(['user', 'comments'])
        ])
            ->all();
        return DesignResource::collection($designs);
    }

    public function show($id)
    {
        $design = $this->designs->find($id);
        return new DesignResource($design);
    }

    public function update(Request $request, $id)
    {
        $design = $this->designs->find($id);

        $this->authorize('update', $design);

        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,' . $id],
            'description' => ['required', 'string', 'max:140'],
            'tags' => ['required'],
            'team' => ['required_if:assign_to_team,true']
        ]);


        $this->designs->update($id, [
            'team_id' => $request->team,
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => $design->upload_successful ? $request->is_live : false

        ]);

        $this->designs->applyTags($id, $request->tags);

        return new DesignResource($design);
    }

    public function destroy($id)
    {
        $design = $this->designs->find($id);
        $this->authorize('delete', $design);

        foreach (['thumbnail', 'large', 'original'] as $size) {
            $imageWithPath = "uploads/designs/{$size}/" . $design->image;
            Storage::disk($design->disk)->exists($imageWithPath) ?
                Storage::disk($design->disk)->delete($imageWithPath) : false;
        }

        $this->designs->delete($id);

        return response()->json([
            "message" => "Design deleted"
        ]);
    }

    public function findBySlug($slug)
    {
        $design = $this->designs->withCriteria([
            new IsLive(),
            new EagerLoad(['user', 'comments'])
        ])->findWhereFirst('slug', $slug);
        return new DesignResource($design);
    }

    public function like($id)
    {
        $this->designs->like($id);
        return response()->json([
            'message' => "Successful"
        ], 200);
    }

    public function checkIfUserHasLiked($id)
    {
        $isLiked = $this->designs->isLikedByUser($id);
        return response()->json([
            'liked' => $isLiked
        ]);
    }

    public function search(Request $request)
    {
        $designs = $this->designs->search($request);
        return DesignResource::collection($designs);
    }

    public function getForTeam($teamId)
    {
        $designs = $this->designs->withCriteria([
            new IsLive()
        ])->findWhere('team_id', $teamId);
        return DesignResource::collection($designs);
    }

    public function getForUSer($userId)
    {
        $designs = $this->designs->withCriteria([
            new IsLive()
        ])->findWhere('user_id', $userId);
        return DesignResource::collection($designs);
    }
}
