<?php

namespace App\Infra\SaleItem\Persistence\Eloquent;

use App\Infra\Product\Persistence\Eloquent\Product;
use App\Infra\Sale\Persistence\Eloquent\Sale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'product_name',
        'price',
        'quantity',
        'subtotal',
        'discount',
        'total',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Boot do model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->subtotal = $item->price * $item->quantity;
            $item->total = $item->subtotal - ($item->discount ?? 0);
        });
    }

    /**
     * Relacionamento com Sale
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Relacionamento com Product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}