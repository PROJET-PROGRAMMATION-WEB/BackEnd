<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $orders = Order::with(['user', 'product.stylist.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return response()->json($orders);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'measurements' => 'required|json',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $product = Product::find($request->product_id);
        $order = Order::create([
            'user_id' => auth()->user()->id,
            'product_id' => $request->product_id,
            'stylist_id' => $product->stylist_id,
            'measurements' => $request->measurements,
            'total_price' => $product->price,
        ]);

        return response()->json([
            'message' => 'order created successfully',
            'order' => $order,
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::find($id);
        if(!$order) {
            return response()->json([
                'message' => ' order not found',
            ],404);
        }
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::find($id);
        if(!$order) {
            return response()-json(['message' => 'order not found'], 404);
        }
        if($order->user_id === auth()->user()->id && $order->status === 'pending') {
            $validator = Validator::make($request->all(), [
                'measurements' => 'json',
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $order->update($request->all());
            return response()->json(['message' => 'your update has been saved', 'order' => $order]);
        }
        if($order->stylist_id === auth()->user()->id && $order->status === 'pending') {
            $validator = Validator::make($request->all(), [
                'expected_delivery_date' => 'required|date',
            ]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $order->expected_delivery_date = $request->expected_delivery_date;
            $order->status = 'confirmed';
            $order->save();
            return response()->json(['message' => 'your accept order', 'order' => $order]);
        }

        return response()->json(['message' => 'you dont have permission'], 422);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function updateStatus(Request $request, string $id)
    {
        $order = Order::find(id);
        if(!$order) {
            return response()->json(['message' => 'order not found'], 404);
        }
        if($order->stylist_id === auth()->user()->id) {
            $validator = Validator::make($request->all(), ['status' => 'required|in:in_progress,completed',]);
            if($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $order->update($request->all());
            return response()->json(['message' => 'your order status has been updated', 'order' => $order]);
        }
    }
}
