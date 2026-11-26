<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BulkOrder extends Model
{
    protected $fillable = [
        'full_name',
        'mobile',
        'email',
        'address',
        'shop_name',
        'category_id',
        'category_title',
        'quantity',
        'message',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}