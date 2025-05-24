<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $table = "collections";
    protected $fillable = [
         'brand_id', 'collection_name', 'slug', 'description', 'collection_image', 'banner',
    ];
    public function brand(){
         return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }
}
