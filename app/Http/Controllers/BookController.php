<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Courses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{ // $booking = Booking::with(['course','user'])->get();
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $course = Courses::with([
                'users:id,name'   // أسماء المستخدمين فقط
            ])
            ->withCount('users')   // عدد المستخدمين
            ->get();        $booking = Booking::with(['course','user'])->get();
        return response()->json([
            "courses"=>$course,
            "enroll"=>$booking
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
   // تأكد إن المستخدم مسجل دخول
   public function store(Request $request)
{
    // تأكد إن المستخدم مسجل دخول
    if (!Auth::check()) {
        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
    }

    // validation
    $data = $request->validate([
        'course_id' => 'required|exists:courses,id',
    ]);

    // منع التكرار
    if (Booking::where('user_id', Auth::id())
        ->where('course_id', $data['course_id'])
        ->exists()) {

        return response()->json([
            'message' => 'Already enrolled'
        ], 409);
    }

    $booking = Booking::create([
        'user_id' => Auth::id(),
        'course_id' => $data['course_id'],
    ]);

    return response()->json([
        'message' => 'Course enrolled successfully',
        'data' => $booking
    ], 201);
}


// BookController.php

public function check($courseId)
{
    if (!Auth::check()) {
        return response()->json([
            'enrolled' => false
        ]);
    }

    $enrolled = Booking::where('user_id', Auth::id())
        ->where('course_id', $courseId)
        ->exists();

    return response()->json([
        'enrolled' => $enrolled
    ]);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
