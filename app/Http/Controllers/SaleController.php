<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Inertia\Inertia;
use App\Models\ProductList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
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

        return Inertia::render('Transaction/Sales', [
            'user' => Auth::user(),
            'data' => $data->paginate(5),
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
            'amount' => 'required',
            'cash' => 'required',
            'change' => 'required',
            'user_id' => 'required',
        ];

        $validated = $request->validate($rules);
        $created = Sale::create($validated);
        return response()->json($created);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Sale::destroy($id);
    }
}
