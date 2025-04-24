<?php

namespace Shaz3e\EmailBuilder\App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalEmailTemplate extends Model
{
    protected $fillable = [
        'header',
        'default_header',
        'footer',
        'default_footer',
    ];
}
