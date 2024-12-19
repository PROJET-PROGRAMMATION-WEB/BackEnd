<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show', 'search', 'byCategory']]);
    }

    public function index()
    {
        $products = Product::with(['stylist.user'])
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'categories' => 'required|array',
            'estimated_delivery_days' => 'required|integer|min:1',
            'photos' => 'required|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'materials' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Gérer le téléchargement des photos
        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('products', 'public');
                $photos[] = $path;
            }
        }

        $product = Product::create([
            'stylist_id' => auth()->user()->stylist->id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'categories' => $request->categories,
            'estimated_delivery_days' => $request->estimated_delivery_days,
            'photos' => $photos,
            'materials' => $request->materials,
            'is_available' => true
        ]);

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product
        ], 201);
    }

    public function show($id)
    {
        $product = Product::with(['stylist.user', 'reviews.user'])
            ->find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Incrémenter le nombre de vues
        $product->increment('views');

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        if (auth()->user()->stylist->id !== $product->stylist_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric|min:0',
            'categories' => 'array',
            'estimated_delivery_days' => 'integer|min:1',
            'photos' => 'array',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'materials' => 'array',
            'is_available' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Gérer les nouvelles photos si présentes
        if ($request->hasFile('photos')) {
            // Supprimer les anciennes photos
            foreach ($product->photos as $photo) {
                Storage::disk('public')->delete($photo);
            }

            $photos = [];
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('products', 'public');
                $photos[] = $path;
            }
            $request->merge(['photos' => $photos]);
        }

        $product->update($request->all());

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        if (auth()->user()->stylist->id !== $product->stylist_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Supprimer les photos
        foreach ($product->photos as $photo) {
            Storage::disk('public')->delete($photo);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function search(Request $request)
    {
        $query = Product::query()->with(['stylist.user']);

        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->has('category')) {
            $query->whereJsonContains('categories', $request->category);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json($products);
    }

    public function byCategory($category)
    {
        $products = Product::with(['stylist.user'])
            ->where('is_available', true)
            ->whereJsonContains('categories', $category)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return response()->json($products);
    }
}
