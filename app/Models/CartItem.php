<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CartItem extends Pivot
{
    /**
     * Get cart
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
