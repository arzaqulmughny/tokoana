<?php

namespace App\Http\Controllers;

use App\Models\StockOutItem;
use Illuminate\Http\Request;

class StockOutItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $rules = [
            'product_id' => 'required',
            'quantity' => 'required',
            'history_id' => 'required'
        ];

        $validated = $request->validate($rules);
        StockOutItem::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(StockOutItem $stockOutItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockOutItem $stockOutItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockOutItem $stockOutItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockOutItem $stockOutItem)
    {
        //
    }
}
