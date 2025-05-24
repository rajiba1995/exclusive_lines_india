<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
   
     protected $table = "products";
    protected $fillable = [
        'brand_id', 'collection_id', 'name', 'slug', 'subheading', 'mrp', 'new_arrival', 'best_seller', 'offer_price', 'sku', 'quantity', 'brochure', 'image', 'short_description', 'long_description', 'badge', 'sequence', 'status', 'created_at', 'updated_at'
    ];
    public function brand(){
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }
    public function collection(){
        return $this->belongsTo(Collection::class, 'collection_id', 'id');
    }

    public function fixedSpecifications() {
        return $this->hasMany(ProductSpecification::class)->where('type', 'fixed');
    }
    public function galleyImges() {
        return $this->hasMany(ProductGalleryImage::class);
    }

    public function otherSpecifications() {
        return $this->hasMany(ProductSpecification::class)->where('type', 'other');
    }
        
}
