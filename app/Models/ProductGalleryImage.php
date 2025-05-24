<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGalleryImage extends Model
{
    //
    protected $table = "product_gallery_images";
    protected $fillable = [
        'product_id','image'
    ];
    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
