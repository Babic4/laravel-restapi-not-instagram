<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $table = "gallery";

    public $timestamps = false;

    protected $fillable = [
        'id',
        'ownerPhoto',
        'datetimeAdd',
        'photo',
    ];
}
