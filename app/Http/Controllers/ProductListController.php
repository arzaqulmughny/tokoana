<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\ProductList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Product/List', [
            'user' => Auth::user(),
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductList $productList)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductList $productList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductList $productList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductList $productList)
    {
        //
    }
}
