<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Http\Resources\DesignResource;
use App\Models\Design;
use App\Repositories\Contracts\DesignInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $designs = $this->designs->all();
        return DesignResource::collection($designs);
    }

    public function update(Request $request, $id)
    {
        $design = Design::findOrFail($id);

        $this->authorize('update', $design);

        $this->validate($request, [
            'title' => ['required', 'unique:designs,title,' . $id],
            'description' => ['required', 'string', 'max:140'],
            'tags' => ['required']
        ]);


        $design->update([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => Str::slug($request->title),
            'is_live' => $design->upload_successful ? $request->is_live : false
        ]);

        $design->retag($request->tags);

        return new DesignResource($design);
    }

    public function destroy($id)
    {
        $design = Design::findOrFail($id);
        $this->authorize('delete', $design);

        foreach (['thumbnail', 'large', 'original'] as $size) {
            $imageWithPath = "uploads/designs/{$size}/" . $design->image;
            Storage::disk($design->disk)->exists($imageWithPath) ?
                Storage::disk($design->disk)->delete($imageWithPath) : false;
        }

        $design->delete();

        return response()->json(
            []
        );

    }
}
