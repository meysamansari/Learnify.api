<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Image;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BlogController extends Controller
{

    /**
     * @OA\Get(
     *      path="/projects",
     *      operationId="getProjectsList",
     *      tags={"Projects"},
     *      summary="Get list of projects",
     *      description="Returns list of projects",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */

    public function index(Request $request)
    {
        $query = $request->input('query');
        $blogs = Blog::with('image.media')
            ->where('title', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'message' => 'Blogs retrieved successfully',
            'blogs' => $blogs,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image_id' => 'nullable|exists:images,id',
        ]);

        $blogData = [
            'title' => $request->title,
            'description' => $request->description,
            'image_id' => $request->image_id,
        ];
        $blog = Blog::create($blogData);

        if ($request->image_id) {
            $image = Image::findOrFail($request->image_id);
            $image->update([
                'status' => 'use',
                'imageable_id' => $blog->id,
                'imageable_type' => Blog::class,
            ]);
        }

        return response()->json([
            'message' => 'Blog created successfully',
            'blog' => $blog,
        ]);
    }


    public function show($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $image = $blog->image;
            $imageData=[];
            if ($image){
            $imageData = $image->getMedia('*');}
            return response()->json([
                'message' => 'Blog retrieved successfully',
                'blog' => $blog,
                'image_data' => $imageData,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Blog not found'], 404);
        }
    }


    public function update(Request $request, $blog_id)
    {
        try {
            $request->validate([
                'title' => 'required',
                'description' => 'required',
                'image_id' => 'nullable|exists:images,id',
            ]);

            $blog = Blog::findOrFail($blog_id);

            $blogData = [
                'title' => $request->title,
                'description' => $request->description,
                'image_id' => $request->image_id,
            ];
            $blog->update($blogData);
            if ($request->image_id) {
                $image = Image::findOrFail($request->image_id);
                $image->update([
                    'status' => 'use',
                    'imageable_id' => $blog->id,
                    'imageable_type' => Blog::class,
                ]);
            }
            return response()->json([
                'message' => 'Blog updated successfully',
                'blog' => $blog,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Blog not found'], 404);
        }
    }


    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $blog->image->clearMediaCollection('*');
            $blog->image->delete();
            $blog->delete();
            return response()->json([
                'message' => 'Blog deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Blog not found'], 404);
        }
    }
}

