<?php
namespace App\Http\Controllers\Web;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductsController extends Controller
{
    use ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth:web')->except('list');
    }

    public function list(Request $request)
    {
        $query = Product::select("products.*");

        $query->when($request->keywords, 
            fn($q) => $q->where("name", "like", "%$request->keywords%"));

        $query->when($request->min_price, 
            fn($q) => $q->where("price", ">=", $request->min_price));
        
        $query->when($request->max_price, 
            fn($q) => $q->where("price", "<=", $request->max_price));
        
        $query->when($request->order_by, 
            fn($q) => $q->orderBy($request->order_by, $request->order_direction ?? "ASC"));

        $products = $query->get();

        return view('products.list', compact('products'));
    }

    public function edit(Request $request, Product $product = null)
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Employee'])) {
            abort(403);
        }
        
        $product = $product ?? new Product();

        return view('products.edit', compact('product'));
    }

    public function save(Request $request, Product $product = null)
    {
        $this->validate($request, [
            'code' => ['required', 'string', 'max:32'],
            'name' => ['required', 'string', 'max:128'],
            'model' => ['required', 'string', 'max:256'],
            'description' => ['required', 'string', 'max:1024'],
            'price' => ['required', 'numeric'],
            'stock' => ['required', 'integer', 'min:0'],
            'photo' => ['nullable', 'string', 'max:128'], // Add validation for photo
        ]);

        if (!auth()->user()->hasAnyRole(['Admin', 'Employee'])) {
            abort(403);
        }        

        $product = $product ?? new Product();
        $product->fill($request->all());
        $product->save();

        return redirect()->route('products_list');
    }

    public function delete(Request $request, Product $product)
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Employee'])) {
            abort(403);
        }
        
        $product->delete();

        return redirect()->route('products_list');
    }

    public function buy(Request $request, Product $product)
    {
        $user = auth()->user();

        // Check if user is a Customer
        if (!$user->hasRole('Customer')) {
            abort(403, 'Only customers can purchase products.');
        }

        // Check if product is in stock
        if ($product->stock <= 0) {
            return redirect()->back()->withErrors('Product is out of stock.');
        }

        // Check if user has sufficient credit
        if ($user->credit < $product->price) {
            return view('products.insufficient_credit', compact('product'));
        }

        // Deduct credit and reduce stock
        $user->credit -= $product->price;
        $user->save();

        $product->stock -= 1;
        $product->save();

        // Record the purchase
        try {
            $user->purchases()->attach($product->id, ['quantity' => 1]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('Failed to record the purchase. Please try again.');
        }

        return redirect()->route('products_list')->with('success', 'Product purchased successfully!');
    }

    public function purchasedProducts(Request $request)
    {
        $user = auth()->user();

        if (!$user->hasRole('Customer')) {
            abort(403);
        }

        $purchasedProducts = $user->purchases;

        return view('products.purchased', compact('purchasedProducts'));
    }

    public function review(Request $request, Product $product)
    {
        $user = auth()->user();

        // Only check if user is a Customer
        if (!$user->hasRole('Customer')) {
            abort(403, 'Only customers can review products.');
        }

        $this->validate($request, [
            'review' => ['required', 'string', 'max:1000'],
        ]);

        $product->review = $request->review;
        $product->reviewed_by = $user->id;
        $product->reviewed_at = \Carbon\Carbon::now();
        $product->save();

        return redirect()->route('products_list')->with('success', 'Review submitted successfully!');
    }
}