<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['doc_id', 'file_name', 'file_type', 'text_file'];

    // Possible file types: txt, pdf
    // Possible document_query status: review, agreed, tiebreak, solved

    public function judgments()
    {
        return $this->hasMany(Judgment::class);
    }

    public function queries()
    {
        return $this->belongsToMany(Query::class)
            ->withPivot(['judgments', 'status'])
            ->withTimestamps();
    }

    public function judgmentsByQuery($query_id, $badges = False)
    {
        $judgs = '';

        foreach ($this->judgments as $judgment)
            if($judgment->queryy->id == $query_id)
                $judgs .= $judgment->judgment . ", ";

        if($badges){
            $judgs = str_replace('Very Relevant', '<span class="badge badge-pill badge-success">Very Relevant</span>', $judgs);
            $judgs = str_replace('Marginally Relevant', '<span class="badge badge-pill badge-advise">Marginally Relevant</span>', $judgs);
            $judgs = str_replace('Not Relevant', '<span class="badge badge-pill badge-danger">Not Relevant</span>', $judgs);
            $judgs = str_replace('Relevant,', '<span class="badge badge-pill badge-primary">Fairly Relevant</span>,', $judgs);
        }

        return substr($judgs, 0, -2);
    }

    // Status of the document-query pair
    public function statusByQueryPair($query_id)
    {
        foreach ($this->queries as $query)
            if($query->id == $query_id)
                return ucfirst($query->pivot->status);
    }
}
