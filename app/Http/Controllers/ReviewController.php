<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::all();

        return response()->json(['data' => $reviews]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product,id',
            'review_rating' => 'required|integer|min:1|max:5',
            'review_comment' => 'nullable|string',
        ]);

        // You can customize the logic to associate the review with the authenticated user
        $user = Auth::user();

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $request->input('product_id'),
            'review_rating' => $request->input('review_rating'),
            'review_comment' => $request->input('review_comment'),
        ]);

        return response()->json($review, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $review = Review::findOrFail($id);

        return response()->json($review);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }
}
