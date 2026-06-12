<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'upload_id',
        'sku',
        'handle',
        'title',
        'vendor',
        'product_type',
        'description',
        'tags',
        'shopify_product_id',
        'status',
        'error_message',
    ];

    public function upload()
    {
        return $this->belongsTo(Upload::class);
    }

    public function logs()
    {
        return $this->hasMany(ImportLog::class);
    }
}
