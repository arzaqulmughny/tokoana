<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = DB::table('suppliers')
        ->when($request->search, function ($query, string $searchQuery) {
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

        return Inertia::render('Suppliers', [
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
        $this->authorize('create', Supplier::class);
       $validated = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'description' => ''
        ]);

        Supplier::create($validated);
        return redirect()->back()->with('add-supplier-success', "New supplier has been added");
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $this->authorize('update', Supplier::class);
        $validated = $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'description' => ''
        ]);

        $supplier->update($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $this->authorize('delete', Supplier::class);
        Supplier::destroy($supplier->id);
    }
}
