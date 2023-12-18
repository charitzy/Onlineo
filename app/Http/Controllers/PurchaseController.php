<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Retrieve the authenticated user using the provided bearer token
            $user = $request->user();

            // Debugging statement to check the user
            // dd($user);

            // Retrieve all purchases with related data
            $purchases = Purchase::with(['user', 'product', 'order', 'orderDetail'])
                ->where('user_id', $user->id) // Filter by the authenticated user
                ->get();

            // Debugging statement to check the purchases
            // dd($purchases);

            // Transform the data to include the required information
            $formattedPurchases = $purchases->map(function ($purchase) {
                return [
                    'user_name' => $purchase->user->name,
                    'product_name' => $purchase->product->prod_name,
                    'order_quantity' => $purchase->orderDetail->order_qty,
                    'product_price' => $purchase->product->prod_price,
                    'order_status' => $purchase->order->order_status,
                    // Add other fields as needed
                ];
            });

            // Return a JSON response with the formatted purchases
            return response()->json(['purchases' => $formattedPurchases]);
        } catch (\Exception $e) {
            // Handle and log any exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
