<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreLocation extends Model
{
   use HasFactory;
    protected $fillable = [
        'name', 
        'address', 
        'contact_numbers', 
        'operating_time', 
        'brands', 
        'outlet_type', 
        'uploaded_by'
    ];

    public function brands(){
        return $this->belongsToMany(Brand::class);
    }
}
