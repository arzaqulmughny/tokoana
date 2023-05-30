<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\StockOut;
use App\Models\Supplier;
use App\Models\ProductList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockOutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = ProductList::with('category', 'unit')
        ->when($request->search, function ($query, string $searchQuery) {
            $query->where('name', 'LIKE', '%' . $searchQuery . '%');
        })
        ->where('stock', '>', 0);

        return Inertia::render('Transaction/StockOut', [
            'user' => Auth::user(),
            'data' => $data->paginate(5),
            'suppliers' => Supplier::all(),
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
        $rules = [
            'user_id' => 'required',
            'note' => 'required',
        ];

        $validated = $request->validate($rules);
        $created = StockOut::create($validated);
        return response()->json($created);
    }

    /**
     * Display the specified resource.
     */
    public function show(StockOut $stockOut)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockOut $stockOut)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockOut $stockOut)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockOut $stockOut)
    {
        //
    }
}
