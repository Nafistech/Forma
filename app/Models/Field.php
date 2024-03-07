<?php

namespace App\Models;

use App\Models\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Field extends Model
{
    use HasFactory;

    protected $primaryKey = 'field_id'; // Define the primary key

    protected $keyType = 'string'; // Specify the key type

    public $incrementing = false;

    protected $fillable = [
        'field_id',
        'form_id',
        'field_label',
        'field_type',
        'field_header',
        'more_options',
        'isRequired',
        'field_placeholder',
        'field_instructions',
        'field_order',
        'value'
    ];

    protected $casts = [
        'more_options' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id', 'form_id');
    }
}
