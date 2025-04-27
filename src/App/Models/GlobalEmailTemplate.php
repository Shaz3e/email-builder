<?php

namespace Shaz3e\EmailBuilder\App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalEmailTemplate extends Model
{
    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'header_image',
        'header_text',
        'header_text_color',
        'header_background_color',

        'footer_image',
        'footer_text',
        'footer_text_color',
        'footer_background_color',
        'footer_bottom_image',
    ];
}
