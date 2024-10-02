<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    /**
     * Get cart items
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get cart total
     */
    public function total()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += ($item->amount * $item->quantity);
        }
        return $total;
    }
}
