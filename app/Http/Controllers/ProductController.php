<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->query('user_id');
        if ($userId) {
            $products = Product::where('user_id', $userId)->get();
        } else {
            $products = Product::all();
        }
        return response()->json($products);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prod_name' => 'required',
            'prod_description' => 'nullable',
            'prod_price' => 'required|numeric',
            'prod_stock' => 'required|integer',
            'prod_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:category,id', // Assuming your table name is 'categories'

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = auth()->user();
        $productData = $validator->validated();
        $productData['user_id'] = $user->id;

        // $productData = $request->all();


        // // Handle image upload
        // if ($request->hasFile('prod_image')) {
        //     $image = $request->file('prod_image');
        //     $imageName = $image->getClientOriginalName();

        //     // Validate the image again using Laravel's built-in validation
        //     $validator = Validator::make(['prod_image' => $image], [
        //         'prod_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        //     ]);

        //     if ($validator->fails()) {
        //         return response()->json(['error' => $validator->errors()], 400);
        //     }

        //     // Store the image in the storage/public/images directory
        //     Storage::disk('public')->put("images/{$imageName}", file_get_contents($image));

        //     // Save the image path in the database
        //     $productData['prod_image'] = "images/{$imageName}";
        // }
        // if ($request->hasFile('image')) {
        //     $filename = $request->file('image')->getClientOriginalName();
        //     $path = $request->file('image')->storeAs('public/images', $filename);
        //     $movie->image = $filename;
        // }


        $product = Product::create($productData);

        return response()->json(['data' => $product, 'message' => 'Product created successfully'], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return response()->json(['data' => $product]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validatedData = $request->validate([
            'prod_name' => 'required|string|max:255',
            'prod_description' => 'required|string',
            'prod_price' => 'required|numeric',
            'prod_stock' => 'required|integer',
            'prod_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|exists:category,id',

        ]);

        $product->update($validatedData);

        return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
    }



    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
    public function search(string $categoryName)
    {


        // if ($categoryName) {
        //     // Make sure 'category' is the correct relationship name on the Product model
        //     // and that 'name' is the correct column on the 'categories' table
        //     $productsQuery->whereHas('category', function ($query) use ($categoryName) {
        //         $query->where('name', $categoryName);
        //     });
        // }

        // // If no search term and no category name is provided, you may decide to return all products
        // // or perhaps an empty array or error message, depending on your application's needs.
        // if (!$searchTerm && !$categoryName) {
        //     return response()->json(['error' => 'No search term or category provided'], 400);
        // }

        // return response()->json($productsQuery->get());

        $product = Product::where('prod_name', 'like', '%' . $categoryName . '%')->get();

        if (!$product) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json($product);
    }
}
