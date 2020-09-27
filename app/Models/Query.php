<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    use HasFactory;

    public function judgments()
    {
        return $this->hasMany(Judgment::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class);
    }
}