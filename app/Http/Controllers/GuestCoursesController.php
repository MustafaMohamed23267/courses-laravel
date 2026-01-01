<?php

namespace App\Http\Controllers;

use App\Models\Courses;
use Illuminate\Http\Request;

class GuestCoursesController extends Controller
{
    
     
    public function index()
    {
      return Courses::with(['category', 'instructor'])->get();

    }

    
   

    public function show( $id)
    {
        return Courses::with(['instructor', 'category'])->find($id);


    }

    
}
