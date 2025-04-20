<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'model', 'description', 'price', 'stock', 'photo'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}