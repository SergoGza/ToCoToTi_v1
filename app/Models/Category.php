<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'icon'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
