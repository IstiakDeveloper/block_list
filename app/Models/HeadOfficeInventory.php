<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeadOfficeInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_stock',
        'total_stock_in',
        'total_stock_out'
    ];
}
