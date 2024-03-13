<?php

namespace App\Models;

use App\Models\Field;
use App\Models\Setting;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Form extends Model
{
    use HasFactory;

    protected $primaryKey = 'form_id'; // Define the primary key

    protected $keyType = 'string'; // Specify the key type

    public $incrementing = false; // Disable auto-incrementing for the primary key

    protected $fillable = [
        'form_id', 'form_title', 'form_description', 'user_id' , 'google_sheet_id'
    ];

    public function fields()
    {
        return $this->hasMany(Field::class, 'form_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'form_id');
    }

    public function settings()
    {
        return $this->belongsTo(Setting::class, 'form_id' , 'form_id');
    }

    public function sheet()
    {
        return $this->belongsTo(GoogleSheets::class , 'form_id' , 'form_id');
    }

    public function resetForm()
    {
        // Delete related fields
        $this->fields()->delete();

        // Delete related settings
        $this->settings()->delete();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
