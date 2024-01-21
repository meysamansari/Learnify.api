<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseStatusRequest;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Image;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{

    public function create(Request $request)
    {
        $user = Auth::user();
        $user_record = User::find($user->id);
        if ($user_record->resume && !is_null($user_record->resume->description) && !is_null($user_record->name) && !is_null($user_record->family)
            && !is_null($user_record->email) && !is_null($user_record->university) && !is_null($user_record->field_of_study)
            && !is_null($user_record->educational_stage) && !is_null($user_record->state) && !is_null($user_record->city)) {
            $course = Course::create(['user_id' => $user->id, 'title' => $request->title, 'description' => $request->description, 'step' => 1, 'status' => 'Pending']);
            return response()->json(['message' => 'course successfully created', 'data' => $course]);
        } else {
            return response()->json(['message' => 'mentor should filled personal profile'], 401);
        }
    }

    public function update(Request $request, $course_id, $step)
    {
        $user = Auth::user();
        $course = $user->courses()->find($course_id);

        if (!$course) {
            return response()->json(['error' => 'Course not found or does not belong to this user'], 404);
        }

        if ($step == 0) {
            $request->validate(['title' => 'required', 'description' => 'required']);
            $course->update(['title' => $request->title, 'description' => $request->description]);
        } else if ($step == 1) {
            $request->validate([
                'image_id' => 'required|exists:images,id',
                'teaser_id' => 'required|exists:videos,id'
            ]);

            $course->update(['image_id' => $request->image_id, 'teaser_id' => $request->teaser_id]);

            $image = Image::findOrFail($request->image_id);
            $image->update([
                'status' => 'use',
                'imageable_id' => $course->id,
                'imageable_type' => Course::class,
            ]);

            $video = Video::findOrFail($request->teaser_id);
            $video->update([
                'status' => 'use',
                'videoable_id' => $course->id,
                'videoable_type' => Course::class,
            ]);
        } else if ($step == 2) {
            $validated = $request->validate(['chapters' => 'required|array']);

            foreach ($validated['chapters'] as $chapterData) {
                $chapter = Chapter::updateOrCreate(['id' => $chapterData['id']], ['course_id' => $course->id, 'title' => $chapterData['title']]);

                foreach ($chapterData['lessons'] as $lessonData) {
                    Lesson::updateOrCreate(['id' => $lessonData['id']], [
                        'chapter_id' => $chapter->id,
                        'title' => $lessonData['title'],
                        'time' => $lessonData['time'],
                        'visibility' => $lessonData['visibility'],
                        'video_id' => $lessonData['video_id'],
                    ]);

                    $video = Video::findOrFail($lessonData['video_id']);
                    $video->update([
                        'status' => 'use',
                        'videoable_id' => $lessonData['id'],
                        'videoable_type' => Lesson::class,
                    ]);
                }
            }
        } else if ($step == 3) {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|integer'
            ]);

            $course->update([
                'category_id' => $request->category_id,
                'price' => $request->price,
                'status' => 'in_progress'
            ]);
        }

        $course_step = $course->step;
        if ($step >= $course_step && $step != 4) {
            $course->update(['step' => $step + 1]);
        }

        return response()->json(['message' => 'course successfully updated', 'data' => $course]);
    }
    public function show($course_id)
    {
        $course = Course::with([
            'chapters.lessons.video.media',
            'image.media',
            'video.media'
        ])->findOrFail($course_id);
        $user = auth()->user();
        $user_buy_course = false;
        if ($user) {
            $user_buy_course = $user->orders()->where('status', 'paid')->whereHas('courses', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })->exists();
        }
        $course->forceFill(['view_count' => $course->view_count + 1])->save();
        $mentor = User::findOrFail($course->user_id);
        if($course->category_id){
        $category=Category::findOrFail($course->category_id);}
        else{
            $category=null;
        }
        $averageRate = $course->comments->avg('rate');
        $countComments = $course->comments->count();
        $comments = Comment::where('course_id', $course_id)->orderBy('updated_at', 'desc')->paginate(20);
            $chapterCount = $course->chapters->count();
            $lessonCount = 0;
        foreach ($course['chapters'] as $chapterData) {
                $lessonCount += $chapterData->lessons->count();
            }
            $data = [
                'user_buy_course'=>$user_buy_course,
                'course'=>$course,
                'category' =>$category,
                'mentor' => $mentor,
                'chapter_count'=>$chapterCount,
                'lesson_count'=>$lessonCount,
                'averageRate' => $averageRate,
                'countComments'=>$countComments,
                'comments'=>$comments,

            ];
            return response()->json(['data' => $data]);
    }
    public function courseStatus(CourseStatusRequest $request,$course_id){
        $course = Course::findOrFail($course_id);
        $course->update([
            'status' => $request->status
        ]);
        return response()->json(['message' => 'course status changed successfully']);
    }
    public function Indexlatest()
    {
        $courses = Course::with([
            'chapters.lessons.video.media',
            'image.media',
            'video.media'
        ])->orderBy('created_at', 'desc')->paginate(20);
        return response()->json(['data' => $courses]);
    }
    public function IndexPopularFree()
    {
        $courses = Course::with([
            'chapters.lessons.video.media',
            'image.media',
            'video.media'
        ])->where('price',0)->orderBy('view_count', 'desc')->paginate(20);
        return response()->json(['data' => $courses]);
    }
}
