<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionData extends Model
{
    use HasFactory;

    protected $primaryKey = 'submission_data_id';
    public $timestamps = true;

    protected $fillable = [
        'submission_id',
        'field_id',
        'field_value',
        'field_name',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'submission_id', 'submission_id');
    }
    public function field()
    {
        return $this->belongsTo(Field::class , 'field_id' , 'field_id');
    }
}
