<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    public const STATUS_CREATED = 'CREATED';
    public const STATUS_PAYED = 'PAYED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_PENDING = 'PENDING';

    protected $fillable = [ 
        'code',
        'product_id',
        'product_quantity',
        'product_price',
        'customer_email',
        'customer_name',
        'customer_mobile',
        'total',
        'status',
    ];

    
    public function product(): BelongsTo
    {        
        return $this->belongsTo(Product::class);
    }
    public function payments(): HasMany
    {        
        return $this->hasMany(Payment::class);
    }

    public static function generate(array $data, Product $product): Order
    {
        $total = $data['product_quantity'] * $product->price;
        
        return new self(array_merge($data, [
            'code' => str_replace('-', '', Str::uuid()),
            'product_id' => $product->id,
            'product_price' => $product->price,
            'total' => $total,
            'status' => self::STATUS_CREATED,
        ]));
    }

    public function isCreated(): bool
    {
        return $this->status == self::STATUS_CREATED;
    }

    public function isRejected(): bool
    {
        return $this->status == self::STATUS_REJECTED;
    }

    public function lastPayment(): Payment
    {
        return $this->payments()->latest()->first();;
    }

    public function isPayed(): bool
    {
        return $this->status == self::STATUS_PAYED;
    }

    public function isPending(): bool
    {
        return $this->status == self::STATUS_PENDING;
    }

    public function scopeCustomer(Builder $query, string $customerEmail): Builder
    {   
        return $query->where('customer_email', $customerEmail)->orderBy('id','desc');
    }
}
