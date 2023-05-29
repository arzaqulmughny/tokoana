<?php

namespace App\Http\Controllers;

use App\Models\StockInItem;
use Illuminate\Http\Request;

class StockInItemController extends Controller
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
            'barcode' => 'required',
            'history_id' => 'required',
            'product_id' => 'required',
            'unit_id' => 'required',
            'quantity' => 'required',
        ];

        $validated = $request->validate($rules);
        StockInItem::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(StockInItem $stockInItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockInItem $stockInItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockInItem $stockInItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockInItem $stockInItem)
    {
        //
    }
}
