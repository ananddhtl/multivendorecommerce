<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\product\ProductModel;
class CartItem extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $fillable = [
        'cart_id', 
         'product_id',
        'quantity',
    ];
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductModel::class);
    }
}
