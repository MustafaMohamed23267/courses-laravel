<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::with('courses')->get();

        return response()->json(data:[
            "category"=> $categories]);
        
    }

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validate = $request->validate([
             'name'=>'required|min:2',
            'slug'=>'required|min:2',
        ]);
        return Category::create([
            'name'=>$validate['name'],
            'slug'=>$validate['slug']
            ]
        );
    }
   
     public function show(string $id)
    {
        $category = Category::find($id);
        
        return $category;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);
         $validate = $request->validate([
             'name'=>'required|min:2',
            'slug'=>'required|min:2',
        ]);
        return $category->update([
            'name'=>$validate['name'],
            'slug'=>$validate['slug']
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
         $category->delete();

    return response()->json([
        'message' => 'Category deleted successfully'
    ], 200);
    }
}
