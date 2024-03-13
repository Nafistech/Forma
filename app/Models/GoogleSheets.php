<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleSheets extends Model
{
    use HasFactory;
    protected $fillable = [
        'document_url', 'document_id' , 'form_id'
    ];

}
