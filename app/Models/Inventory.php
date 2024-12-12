<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    // Protect the 'id' field from mass assignment
    protected $guarded = ['id'];

    /**
     * Relationship with InventoryItem
     * An Inventory can have many InventoryItems.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'inventory_id', 'id');
    }
}
