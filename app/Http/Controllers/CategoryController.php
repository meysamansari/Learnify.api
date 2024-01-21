<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Image;
use App\Models\Video;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        $categories = [];
        $categoriesWithCourseCount = Category::getAllCategoriesWithCourseCount();
        foreach ($categoriesWithCourseCount as $categoryItem) {
            $category = [
                "category_id" => $categoryItem->id,
                "category" => $categoryItem->name,
                "course_count" => $categoryItem->courses_count
            ];
            $categories[] = $category;
        }
        return response()->json(['categories' => $categories]);
    }
    public function show($category_id){
        $category = Category::with('courses.chapters.lessons')->findOrFail($category_id);
        $courses=$category->courses;
       foreach ($courses as $course) {
           $image_id = $course->image_id;
           $teaser_id = $course->teaser_id;
           $coursesImage = [];
           $coursesTeaser = [];
           if ($image_id) {
               $image = Image::find($image_id)->first();
               $courseImage = [
                   'course_id' => $course->id,
                   'course_image' => $image->getMedia('*')];
               $coursesImage[]=$courseImage;
           }
           if ($teaser_id) {
               $video = Video::find($teaser_id)->first();
               $courseTeaser = [
                   'course_id' => $course->id,
                   'course_teaser' => $video->getMedia('*')];
               $coursesTeaser[]=$courseTeaser;
           }
        $data=[$coursesImage,$coursesTeaser];
       }
        return response()->json(['categories' => $category,'data'=>$data]);
    }
}
