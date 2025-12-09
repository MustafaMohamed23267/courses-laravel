<?php

namespace App\Services;
use App\Models\User;
use App\Models\courses;
use App\Http\Requests\StorecoursesRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseServices
{

    public function createCourse(array $data, StorecoursesRequest $StorecourseRequest): courses
    {
         $data = $StorecourseRequest->validated();
        $data['user_id'] = Auth::id();
        $course = courses::create( $data);
        return $course;
    }
    public function getCourse($id){
        $Course = Courses::find($id);
        return $Course;
    }

}
