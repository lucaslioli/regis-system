<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Judgment extends Model
{
    use HasFactory;

    protected $fillable = ['judgment', 'observation', 'untie', 'user_id', 'query_id', 'document_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function document()
    {
        return $this->belongsto(Document::class);
    }

    public function queryy()
    {
        return $this->belongsTo(Query::class, 'query_id');
    }
}
