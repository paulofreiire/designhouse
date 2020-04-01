<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use App\Jobs\UploadImage;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $this->validate($request, [
            'image' => ['required', 'mimes:jpeg,gif,bpm,png', 'max:2048']
        ]);

        //get the image
        $image = $request->file('image');
        $image_path = $image->getPathName();

        //timestamp()_image
        $filename = time() . "_" . preg_replace('/\s+/', '_', strtolower($image->getClientOriginalName()));

        //move image temp folder
        $tmp = $image->storeAs('uploads/original', $filename, 'tmp');

        //create the database record
        $design = auth()->user()->designs()->create([
            'image' => $filename,
            'disk' => config('site.upload_disk')
        ]);

        //dispatch a job
        $this->dispatch(new UploadImage($design));

        return response()->json($design, 200);


    }


}
