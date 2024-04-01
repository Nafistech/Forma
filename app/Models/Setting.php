<?php

namespace App\Models;

use App\Models\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'page_header',
        'page_outro',
        'logo',
        'fb_link',
        'instagram_link',
        'twitter_link',
        'bg_color',
        'text_color',
        'primary_color',
        'rating',

    ];


    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id', 'form_id');
    }


}
