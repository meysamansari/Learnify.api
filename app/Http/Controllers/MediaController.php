<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Image;
use App\Models\Video;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function uploadVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4,mov,avi|max:20000',
        ]);
        $video = new Video();
        $video->addMedia($request->file('video'))->toMediaCollection('videos');
        $video->save();
        return response()->json($video);
    }
    public function uploadImage(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimes:png,jpeg',
        ]);
        $image = new Image();
        $image->addMedia($request->file('image'))->toMediaCollection('images');
        $image->save();
        return response()->json($image);
    }
}
