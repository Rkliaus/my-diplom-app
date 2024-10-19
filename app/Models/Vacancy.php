<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'employer_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
