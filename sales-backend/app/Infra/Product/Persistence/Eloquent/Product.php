<?php

namespace App\Infra\Product\Persistence\Eloquent;

use App\Infra\Persistence\Eloquent\Traits\BelongsToTenant;
use App\Infra\SaleItem\Persistence\Eloquent\SaleItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected static function newFactory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return \Database\Factories\ProductFactory::new();
    }

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'sku',
        'price',
        'cost',
        'stock',
        'min_stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relacionamento com SaleItems
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Verificar se há estoque suficiente
     */
    public function hasStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Decrementar estoque
     */
    public function decrementStock(int $quantity): bool
    {
        if (!$this->hasStock($quantity)) {
            return false;
        }

        $this->decrement('stock', $quantity);
        return true;
    }

    /**
     * Incrementar estoque
     */
    public function incrementStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }

    /**
     * Verificar se está abaixo do estoque mínimo
     */
    public function isBelowMinStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }
}