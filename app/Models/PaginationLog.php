<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaginationLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'table_name',
        'job_name',
        'at_page',
        'success_code',
    ];
}
