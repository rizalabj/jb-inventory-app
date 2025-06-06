<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $fillable =[
        'transaction_id',
        'product_id',
        'product_price_id',
        'price',
        'qty',
        'discount'
    ];

    protected static function booted()
    {
        static::created(function ($transactionDetail) {
            $transaction = $transactionDetail->transaction;
            $product_price = $transactionDetail->productprice;
            $unit_conversion_value = $product_price->unit_conversion_value;
            $base_quantity = $transactionDetail->qty / $unit_conversion_value;
            $stock = new Stock([
                'transaction_id' => $transactionDetail->transaction_id,
                'transaction_detail_id' => $transactionDetail->id,
                'type' => $transaction->type,
                'product_id' => $transactionDetail->product_id,
                'quantity' => $transactionDetail->qty,
                'base_quantity' => $base_quantity,
                'product_price_id' => $transactionDetail->product_price_id,
                'notes' => $transaction->type == "in" ? "Transaksi Pembelian" : "Transaksi Penjulan",
                'user_by' => $transaction->user_by,
            ]);
            $stock->save();
        });

        static::deleting(function ($transactionDetail) {
            foreach ($transactionDetail->stocks as $stock) {
                $stock->delete(); // This will trigger the Stock's deleted event
            }
        });
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class,'transaction_id','id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function stocks():HasMany
    {
        return $this->hasMany(Stock::class);
    }


    public function productprice():BelongsTo
    {
        return $this->belongsTo(ProductPrice::class,'product_price_id','id');
    }

    
}
