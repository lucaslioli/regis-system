<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    use HasFactory;

    protected $fillable = ['qry_id', 'title', 'description', 'narrative', 'status', 'annotators'];

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
        return $this->belongsToMany(Document::class)
            ->withPivot(['judgments', 'status'])
            ->withTimestamps();
    }

    public function documentJudgments($document_id)
    {
        foreach ($this->documents as $document)
            if($document->id == $document_id)
                return $document->pivot->judgments;
    }

    public function increaseAnnotators()
    {
        $this->annotators++;
        $this->save();
    }

    public function setStatus(String $status)
    {
        $this->status = $status;
        $this->save();
    }
}
