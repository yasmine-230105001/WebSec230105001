<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'model',
        'description',
        'price',
        'stock',
        'photo',
        'review',
        'reviewed_by',
        'reviewed_at'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'reviewed_at'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}