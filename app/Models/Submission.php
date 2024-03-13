<?php

namespace App\Models;

use App\Models\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Submission extends Model
{
    use HasFactory;

    protected $primaryKey = 'submission_id';
    public $incrementing = true;

    protected $fillable = ['form_id'];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id', 'form_id');
    }
    public function submissionData()
    {
        return $this->hasMany(SubmissionData::class, 'submission_id', 'submission_id');
    }
}
