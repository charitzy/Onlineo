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
        $categories = Category::with('products')->get();
        return response()->json($categories); // Return data as JSON
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|unique:category',
            'image_url' => 'nullable|url'
        ]);

        return Category::create($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::findOrFail($id)->with('product')->get();;

        return $category;
    }



    /**
     * Update the specified resource in storage.
     */
    public function search($category_id)
    {
        // Find the category by ID and load the products relationship
        $category = Category::with('products')->find($category_id);

        // Check if the category was found
        if (!$category) {
            // If no category is found, you can return a not found response
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Return the associated products with the category
        // Since you're using Eloquent's eager loading, the products are already loaded
        return response()->json($category->products);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 204);
    }
}
