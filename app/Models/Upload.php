<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'status',
        'total_records',
        'processed_records',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function logs()
    {
        return $this->hasMany(ImportLog::class);
    }
}
