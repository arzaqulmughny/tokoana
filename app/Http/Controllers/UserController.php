<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        $data = User::latest()
            ->when($request->search, function ($query, string $searchQuery) {
                $query->where('name', 'LIKE', '%'. $searchQuery .'%')->orWhere('username', 'LIKE', '%'. $searchQuery .'%');
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

        return Inertia::render('Employee', [
            'user' => Auth::user(),
            'data' => $data->paginate(10),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        $validated = $request->validate([
            'username' => 'required|unique:users',
            'name' => 'required',
            'password' => 'required|min:8',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        User::create($validated);
        return redirect()->back()->with('add-employee-success', "New employee has been added");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('view', User::class);
        $data = User::all()->find($id);
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
        $this->authorize('update', User::class);
        $rules = [];

        if ($request->name !== DB::table('users')->where('id', $id)->first()->name) {
            $rules['name'] = 'required|unique:users';
        }
        if ($request->username !== DB::table('users')->where('id', $id)->first()->username) {
            $rules['username'] = 'required';
        }

        $validated = $request->validate($rules);
        User::find($id)->update($validated);
    }

    public function updatePassword(Request $request, string $id) {
        $this->authorize('update', User::class);
        $validated = $request->validate([
            'password' => 'required|min:8'
        ]);

        if ($validated) {
            User::find($id)->update(['password' => bcrypt($request->password)]);
        };
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete', User::class);
        User::destroy($id);
    }}
