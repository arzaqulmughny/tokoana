<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = Sale::with('items', 'user')
            ->when($request->search, function ($query, string $searchQuery) {
                $query->whereRelation('user', 'name', 'LIKE', '%'. $searchQuery .'%');
            })
            ->when($request->sort, function ($query, string $sortQuery) {
                switch($sortQuery) {
                    case 'latest':
                        $query->latest();
                        break;
                    case 'oldest':
                        $query->oldest();
                        break;
                    default:
                        $query->latest();
                };
            }, function ($query) {
                $query->latest();
            });

        if ($request->from && $request->to) {
            $data->whereBetween('created_at', [$request->from, $request->to]);
        };

        if ($request->mode === 'print') {
            return response()->json($data->get());
        };

        return Inertia::render('History/Sales', [
            'user' => Auth::user(),
            'data' => $data->paginate(10),
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
    public function show(string $id)
    {
        $data = Sale::all()->find($id)->load('user','items');
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
