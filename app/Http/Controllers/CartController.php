<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $cartItems = Cart::where('user_id', $userId)->with('product')->get();

        return response()->json($cartItems);
    }

    public function store(Request $request)
    {

        try {

            $validated = $request->validate([
                'product_id' => 'required',
                'quantity' => 'required|numeric|min:1',
            ]);

            $userId = $request->userId;
            $cartItem = Cart::create([
                'user_id' => $userId,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
            ]);

            return response()->json($cartItem, 201);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // public function update(Request $request, Cart $cartItem)
    // {
    //     try {
    //         $user = $request->user();
    //         $id = $request->id;
    //         $cartItem = Cart::where('user_id', $user->id)->where('id', $id)->firstOrFail();

    //         $validated = $request->validate([
    //             'quantity' => 'required|numeric|min:1',
    //             'prod_price' => 'sometimes|numeric',
    //         ]);

    //         $cartItem->quantity = $validated['quantity'];
    //         if (isset($validated['prod_price'])) {
    //             // Assuming you have a `price` field in your `carts` table
    //             $cartItem->price = $validated['prod_price'];
    //         }
    //         $cartItem->save();

    //         return response()->json($cartItem);
    //     } catch (\Exception $e) {
    //         \Log::error($e->getMessage());
    //         return response()->json(['error' => 'An error occurred while updating the cart item'], 500);
    //     }

    // }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:1',
            // Include other fields that may need updating
        ]);

        try {
            // Find the cart item by ID for the authenticated user
            $cartItem = Cart::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Update the quantity
            $cartItem->quantity = $validated['quantity'];
            $cartItem->save();

            return response()->json([
                'message' => 'Cart item updated successfully',
                'cartItem' => $cartItem
            ], 200);
        } catch (\Throwable $th) {
            // Handle the exception
            return response()->json(['error' => $th->getMessage()], 400);
        }
    }

    public function destroy($id) // Ensure the parameter name matches the route definition
    {
        $userId = Auth::id(); // Get the authenticated user's ID

        $cartItem = Cart::where('user_id', $userId)->where('id', $id)->first();

        if ($cartItem) {
            $cartItem->delete();
            return response()->json(['message' => 'Cart item deleted successfully'], 200);
        }

        return response()->json(['error' => 'Cart item not found'], 404);
    }


    public function getAllProductInCart(string $user_id)
    {
        $cartItems = Cart::where('user_id', $user_id)->with('product')->get();

        if (!$cartItems) {
            return response()->json([
                'message' => 'Cart is empty'
            ]);
        }
        return response()->json($cartItems);
    }
}
