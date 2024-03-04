<?php

namespace App\Models;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Submission_data extends Model
{
    use HasFactory;

    protected $primaryKey = 'submission_data_id';
    public $timestamps = true;

    protected $fillable = [
        'submission_id',
        'field_id',
        'field_value',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'submission_id', 'submission_id');
    }
}
