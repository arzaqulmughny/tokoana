<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Inertia\Inertia;
use App\Models\ProductList;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = ProductList::with('category', 'unit')->when($request->search, function ($query, string $searchQuery) {
            $query->where('name', 'LIKE', '%' . $searchQuery . '%');
        })
        ->when(!$request->sort, function ($query, string $sortQuery) {
            $query->orderBy('name');
        })
        ->when($request->sort, function ($query, string $sortQuery) {
            switch ($sortQuery) {
                case "name":
                    $query->orderBy('name');
                    break;
                case "latest":
                    $query->latest();
                    break;
                case "oldest":
                    $query->oldest();
                    break;
                default:
                    $query->orderBy('name');
            }
        });

        return Inertia::render('Product/List', [
            'user' => Auth::user(),
            'data' => $data->paginate(5),
            'product_categories' => ProductCategory::all(),
            'product_units' => ProductUnit::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:product_lists',
            'unit_id' => 'required',
            'category_id' => 'required',
            'barcode' => 'required|unique:product_lists',
            'price' => 'required'
        ]);

        ProductList::create($validated);
        return redirect()->back()->with('add-product-success', "New product unit has been added");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $data = ProductList::all()->find($id)->load('category', 'unit');
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductList $productCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        ProductList::find($id)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        ProductList::destroy($id);
    }
}
