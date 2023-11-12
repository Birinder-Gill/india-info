<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryName extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'link',
        'at_page',
    ];

}