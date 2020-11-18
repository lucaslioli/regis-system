<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    use HasFactory;

    protected $fillable = ['qry_id', 'title', 'description', 'narrative', 'status', 'annotators'];

    // Possible status: Incomplete, Semi Complete, Complete
    // Possible document_query status: review, agreed, tiebreak, solved

    public function judgments()
    {
        return $this->hasMany(Judgment::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['skip'])
            ->withTimestamps();
    }

    public function documents()
    {
        return $this->belongsToMany(Document::class)
            ->withPivot(['judgments', 'status'])
            ->withTimestamps();
    }

    // Judgments counter for the document-query pair
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

    public function decreaseAnnotators()
    {
        $this->annotators--;
        $this->save();
    }

    public function setStatus(String $status)
    {
        $this->status = $status;
        $this->save();
    }

    public function countSkipped()
    {
        $count = 0;
        foreach ($this->users as $user)
            $count += $user->pivot->skip;

        return $count;
    }

    public function judgmentsByClass($class = NULL)
    {
        $count['Very Relevant'] = 0;
        $count['Relevant'] = 0;
        $count['Marginally Relevant'] = 0;
        $count['Not Relevant'] = 0;

        foreach ($this->judgments as $judgment)
            $count[$judgment->judgment]++;

        if($class)
            return $count[$class];

        return $count;
    }
}
