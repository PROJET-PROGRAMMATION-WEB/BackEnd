<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Stylist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StylistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show', 'products']]);
    }

    public function index()
    {
        $stylists = Stylist::with('user')
            ->orderBy('average_rating', 'desc')
            ->paginate(10);
        return response()->json($stylists);
    }

    public function show($id)
    {
        $stylist = Stylist::with(['user', 'products'])->find($id);
        
        if (!$stylist) {
            return response()->json(['message' => 'Stylist not found'], 404);
        }

        return response()->json($stylist);
    }

    public function update(Request $request, $id)
    {
        $stylist = Stylist::find($id);
        
        if (!$stylist) {
            return response()->json(['message' => 'Stylist not found'], 404);
        }

        // Vérifier si l'utilisateur actuel est le propriétaire
        if (auth()->user()->id !== $stylist->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'biography' => 'nullable|string',
            'specialty' => 'nullable|string',
            'availability' => 'nullable|array',
            'is_available' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $stylist->update($request->all());

        return response()->json([
            'message' => 'Stylist profile updated successfully',
            'stylist' => $stylist
        ]);
    }

    public function products($id)
    {
        $stylist = Stylist::find($id);
        
        if (!$stylist) {
            return response()->json(['message' => 'Stylist not found'], 404);
        }

        $products = $stylist->products()
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json($products);
    }

    public function updateAvailability(Request $request, $id)
    {
        $stylist = Stylist::find($id);
        
        if (!$stylist) {
            return response()->json(['message' => 'Stylist not found'], 404);
        }

        if (auth()->user()->id !== $stylist->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'availability' => 'required|array',
            'is_available' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $stylist->update($request->all());

        return response()->json([
            'message' => 'Availability updated successfully',
            'availability' => $stylist->availability
        ]);
    }

    public function statistics($id)
    {
        $stylist = Stylist::find($id);
        
        if (!$stylist) {
            return response()->json(['message' => 'Stylist not found'], 404);
        }

        $statistics = [
            'total_products' => $stylist->products()->count(),
            'total_orders' => $stylist->orders()->count(),
            'average_rating' => $stylist->average_rating,
            'total_reviews' => $stylist->total_reviews,
            'completed_orders' => $stylist->orders()->where('status', 'completed')->count()
        ];

        return response()->json($statistics);
    }
}
