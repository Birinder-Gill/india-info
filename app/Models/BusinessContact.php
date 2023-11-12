<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessContact extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "link",
        "address",
        "contact",
        "cat_id",
        "cat_name",
        "real_page",
        "web_page"
    ];
}

//2047, 2056, 3569
