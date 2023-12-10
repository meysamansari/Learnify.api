<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
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
            && !is_null($user_record->educational_stage) && !is_null($user_record->country) && !is_null($user_record->state)) {
            $course = Course::create(['user_id' => $user->id, 'title' => $request->title, 'description' => $request->description, 'step' => 1]);
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
        } else {
            if ($step == 0) {
                $request->validate(['title' => 'required', 'description' => 'required']);
                $course = Course::updateOrCreate(['id' => $course->id, 'user_id' => $user->id], ['title' => $request->title, 'description' => $request->description]);
            } else if ($step == 1) {
                $request->validate(['image_id' => 'required', //exists:media,id
                    'teaser_id' => 'required'//exists:media,id
                ]);
                $course = Course::updateOrCreate(['id' => $course->id,], ['image_id' => $request->image_id, 'teaser_id' => $request->teaser_id,]);
            } else if ($step == 2) {
                $validated = $request->validate(['chapters' => 'required|array',]);
                foreach ($validated['chapters'] as $chapterData) {
                    $chapter = Chapter::updateOrCreate(['id' => $chapterData['id']], ['course_id' => $course->id, 'title' => $chapterData['title']]);

                    foreach ($chapterData['lessons'] as $lessonData) {
                        Lesson::updateOrCreate(['id' => $lessonData['id']], ['chapter_id' => $chapter->id, 'title' => $lessonData['title'], 'time' => $lessonData['time'], 'visibility' => $lessonData['visibility'], 'video_id' => $lessonData['video_id'],]);
                    }
                }
            } else if ($step == 3) {
                $request->validate(['category' => 'required', //exists:category,id
                    'price' => 'required|integer']);
                $course = Course::updateOrCreate(['id' => $course->id], ['category' => $request->category, 'price' => $request->price, 'status' => 'Pending']);
            }
            $course_step = $course->step;
            if ($step >= $course_step && $step != 4) {
                $course->update(['step' => $step + 1]);
            }
            return response()->json(['message' => 'course successfully updated', 'data' => $course]);
        }
    }

    public function show($course_id)
    {
        $course = Course::with('chapters.lessons')->findOrFail($course_id);
        $mentor = User::findOrFail($course->user_id);
        $resume=[];
        $averageRate = $course->comments->avg('rate');
        $countComments = $course->comments->count();
        $comments=$course->comments;
        if ($mentor->resume&&!is_null($mentor->resume->description))
        {
            $resume=$mentor->resume->description;
        }
            $image_id = $course->image_id;
            $teaser_id = $course->teaser_id;
            $courseImage = [];
            $courseVideo = [];
            if ($image_id) {
                $image = Image::find($image_id)->first();
                $courseImage = $image->getMedia('*');
            }
            if ($teaser_id) {
                $video = Video::find($teaser_id)->first();
                $courseVideo = $video->getMedia('*');
            }
            $chapterCount = $course->chapters->count();
            $lessonMedia = [];
            $lessonCount = 0;
        foreach ($course['chapters'] as $chapterData) {
                $lessonCount += $chapterData->lessons->count();
                foreach ($chapterData['lessons'] as $lessonData) {
                    $video_id = $lessonData['video_id'];
                    if ($video_id) {
                        $video = Video::find($video_id)->first();
                        $media = $video->getMedia('*');
                        $lessonMedia[] = [
                            'lesson_id' => $lessonData->id,
                            'media' => $media,
                            'visibility' => $lessonData->visibility,
                        ];
                    }
                }
            }
            $Data = [
                'mentor' => $mentor,
                'mentor_resume'=>$resume,
                'chapter_count'=>$chapterCount,
                'lesson_count'=>$lessonCount,
                'Course_image' => $courseImage,
                'Course_teaser' => $courseVideo,
                'Lesson_media' => $lessonMedia,
            ];
            $commentsData=[
                'averageRate' => $averageRate,
                'countComments'=>$countComments,
                'comments'=>$comments,
            ];
            return response()->json(['course' => $course, 'data' => $Data,'Comments data' =>$commentsData]);
    }
}
