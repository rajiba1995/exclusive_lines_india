<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductSpecification extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'type',
        'spec_name',
        'spec_value',
        'spec_category',
        'sequence',
    ];

    // Relationship to Product (assumes Product model exists)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
