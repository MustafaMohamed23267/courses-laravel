<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CoursesController as ApiCoursesController;

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GuestCoursesController;
use App\Models\Category;
use App\Models\Courses;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user()->load('enrolledCourses');
})->middleware('auth:sanctum');

Route::get('/allusers', function () {
    $users = User::where('role','student')->with("enrolledCourses:id,title")->get();
    $instructor = User::where('role','instructor')
    ->with('courses:id,title,instructor_id')
    ->get();
    return response()->json([
        "users"=>$users,
        "instructor"=>$instructor
    ]);
});

Route::get('/allcourses', [GuestCoursesController::class , 'index']);
Route::get('/allcourses/{id}', [GuestCoursesController::class , 'show']);


Route::controller(CategoryController::class)->group(function(){
Route::get('/category','index');
Route::post('/category','store');
Route::get('/category/{id}','show');

Route::put('/category/{id}','update');
Route::delete('/category/{id}','destroy');
})->middleware('auth:sanctum');

//Route::apiResource('booking',BookingController::class)->middleware('auth:sanctum');

// Route::middleware('auth:sanctum')
//     ->post('/booking', [BookingController::class, 'store']);

Route::get('/book',[BookController::class,'index']);
Route::post('/book',[BookController::class,'store'])->middleware('auth:sanctum');
Route::get('/book/check/{courseId}',[BookController::class,'check'])->middleware('auth:sanctum');


// Route::get('/category',[CoursesController::class ,'category']);

Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::post('register', [AuthController::class, 'register'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    //Route::apiResource('bookings', BookingController::class);
    Route::apiResource('courses', ApiCoursesController::class);
});
