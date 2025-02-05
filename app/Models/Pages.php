<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{

    protected $fillable = [
        'URL_ID',
        'URL_Path',
        'Plain_Text',
    ];
}
