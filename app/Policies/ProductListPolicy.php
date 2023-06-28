<?php

namespace App\Policies;

use App\Models\ProductList;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductListPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->user_level === 1;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->user_level === 1;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ProductList $productList): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ProductList $productList): bool
    {
        //
    }
}
