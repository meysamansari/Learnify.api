<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BlogController extends Controller
{


    public function index(Request $request)
    {
        $query = $request->input('query');
        $blogs = Blog::where('title', 'like', "%$query%")
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
            'cover' => 'required|image|mimes:jpeg,png|max:2048',
        ]);
        $blog = Blog::create($request->only(['title', 'description']));
        $blog->addMediaFromRequest('cover')
            ->toMediaCollection('cover');
        return response()->json([
            'message' => 'Blog created successfully',
            'blog' => $blog,
        ]);
    }


    public function show($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $cover = $blog->getMedia('cover');
            return response()->json([
                'message' => 'Blog retrieved successfully',
                'blog' => $blog,
                'cover' => $cover,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Blog not found'], 404);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'cover' => 'sometimes|image|mimes:jpeg,png,gif|max:2048',
            ]);

            $blog = Blog::findOrFail($id);
            $blog->update($validatedData); // Use $validatedData directly

            if ($request->hasFile('cover')) {
                $blog->clearMediaCollection('cover');
                $blog->addMediaFromRequest('cover')
                    ->toMediaCollection('cover');
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
            $blog->clearMediaCollection('cover');
            $blog->delete();
            return response()->json([
                'message' => 'Blog deleted successfully',
                'blog' => $blog,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Blog not found'], 404);
        }
    }
}

