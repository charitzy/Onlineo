<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;
use App\Models\Cart;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payment = Payment::with('order')->get();
        return response()->json(['payments' => $payment]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:order,id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:cash,gcash,paymaya',
        ]);

        $payment = Payment::create($request->all());
        return response()->json(['message' => 'Payment created successfully', 'payment' => $payment]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'order_id' => 'exists:order,id',
            'amount' => 'numeric',
            'payment_method' => 'in:cash,gcash,paymaya',
        ]);

        $payment = Payment::findOrFail($id);
        $payment->update($request->all());

        return response()->json(['message' => 'Payment updated successfully', 'payment' => $payment]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully']);
    }
    public function payViaGcash(string $user_id)
    {
        $productsInsideMyCart = Cart::where('carts.user_id', $user_id)
            ->join('product', 'product.id', '=', 'carts.product_id') // Updated the join condition to match the correct columns
            ->get();

        $lineItems = [];
        foreach ($productsInsideMyCart as $product) {
            $lineItems[] = [
                'name' => $product->prod_name,
                'quantity' => $product->quantity,
                'amount' => (int) $product->prod_price * 100,
                'currency' => 'PHP',
                'description' => $product->prod_description,
            ];
        }


        $require = [
            'data' => [
                'type' => 'checkout_session',
                'description' => 'Online Shopping Centre',
                'attributes' => [
                    'line_items' => $lineItems,
                    'statement_descriptor' => 'Payment',
                    'payment_method_types' => ['gcash'],
                    'payment_method_allowed' => ['gcash'],
                    'metadata' => [
                        'product-id' => $product->id, // Moved inside the loop to get the correct product ID
                    ],
                ],
            ],
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'accept' => 'application/json',
            'authorization' => 'Basic ' . base64_encode(env('PAYMONGO_SECRET_KEY')),
        ])->post('https://api.paymongo.com/v1/checkout_sessions', $require);

        if ($response->successful()) {  // Check for successful response using the 'successful' method
            return response()->json([
                'checkout_url' => $response->json()['data']['attributes']['checkout_url'],
                'items' => $productsInsideMyCart,
            ], 200);
        }
    }
}
