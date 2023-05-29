<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\StockIn;
use App\Models\ProductList;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockInController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = ProductList::with('category', 'unit')->when($request->search, function ($query, string $searchQuery) {
            $query->where('name', 'LIKE', '%' . $searchQuery . '%');
        });

        return Inertia::render('Transaction/StockIn', [
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
            'supplier_id' => 'nullable',
            'note' => 'nullable'
        ];

        if (!$request->input('supplier_id')) {
            $rules['note'] = 'required';
        };

        $validated = $request->validate($rules);
        $created = StockIn::create($validated);
        return response()->json($created);
    }

    /**
     * Display the specified resource.
     */
    public function show(StockIn $stockIn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockIn $stockIn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StockIn $stockIn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        StockIn::destroy($id);
    }
}
