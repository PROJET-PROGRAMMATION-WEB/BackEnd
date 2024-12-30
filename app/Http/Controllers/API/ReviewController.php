<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['productReviews', 'stylistReviews']]);
    }

    public function productReviews($product_id)
    {
        $product = Product::find($product_id);
        if(!$product) {
            return response()->json(['message' => 'product dont exist'], 404);
        }
        $reviews = Review::where('product_id', $product_id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        return response()->json(['message' => 'product reviews', 'reviews' => $reviews]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'rating' => 'required|decimal:2',
            'comment' => 'required|string',
        ]);
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $product = Product::find($request->product_id);
        if(!$product) {
            return response()->json(['message' => 'product dont exist'], 404);
        }
        $review = Review::create([
            'user_id' => auth()->user()->id,
            'product_id' => $request->product_id,
            'stylist_id' => $product->stylist_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Review created successfully', 'review' => $review], 201);
    }

    /**
     * Display the specified resource.
     */
    public function stylistReviews(string $stylist_id)
    {
        $stylist = Stylist::find(Stylist_id);
        if(!$stylist) {
            return response()->json(['message' => 'stylist dont exist'], 404);
        }
        $reviews = Review::where('stylist_id', $stylist_id)
            ->OrderBy('created_at', 'desc')
            ->paginate(12);
        return response()->json(['message' => 'stylist reviews', 'reviews' => $reviews]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $review = Review::find($id);
        if(!$review) {
            return response()->json(['message' => 'review dont exist'], 404);
        }
        $validator = Validator::make($request->all(), [
            'rating' => 'required|decimal:2',
            'comment'=> 'required|string',
            'is_verified_purchase' => 'nullable|boolean',
            'is_approved' => 'nullable|boolean',
        ]);
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if(auth()->user()->id !== $review->user_id) {
            return response()->json(['message' => 'you dont have permission to update this review'],403);
        }
        $updateData = [
            'rating' => $request->rating,
            'comment' => $request->comment,
        ];
        
        if (!is_null($request->is_verified_purchase)) {
            $updateData['is_verified_purchase'] = $request->is_verified_purchase;
        }
        
        if (!is_null($request->is_approved)) {
            $updateData['is_approved'] = $request->is_approved;
        }
        
        $review->update($updateData);
        
        return response()->json([
            'message' => 'review updated successfully', 
            'review' => $review
        ],202);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = Review::find($id);
        if(!$review) {
            return response()->json(['message' => 'Review not found'], 404);
        }
        if(auth()->user()->id !== $review->user_id) {
            return response()->json(['message' => 'you dont have permission to delete this review'],403);
        }
        $review->delete();
    }
}
