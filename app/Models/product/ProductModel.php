<?php

namespace App\Models\product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProductModel extends Model
{
    use HasFactory;
    protected $table = 'product';
    protected $primaryKey = 'product_id';
    protected $guarded = [];
    public $timestamps = false;

    public function brand()
    {
        return $this->belongsTo('App\Models\BrandModel', 'brand_id');
    }

  
    public function subCategory()
    {
        return $this->belongsTo('App\Models\SubCategoryModel', 'sub_category_id');
    }
}
