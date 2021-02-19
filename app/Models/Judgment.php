<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;

class Judgment extends Model
{
    use HasFactory;

    protected $fillable = ['judgment', 'observation', 'untie', 'user_id', 'query_id', 'document_id'];
    
    // Possible judgments: Very Relevant, Relevant, Marginally Relevant, Not Relevant

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

    public static function judgmentsChartData()
    {
        $judgments = Judgment::select(
                DB::raw("judgment, count(*) as total")) 
            ->groupBy("judgment")
            ->orderBy(DB::raw("FIELD(judgment, 'Very Relevant','Relevant','Marginally Relevant','Not Relevant')"))
            ->get();
  
        $results[] = ['Judgment','Total'];
        foreach ($judgments as $key => $value) {
            $results[++$key] = [
                ($value->judgment == "Relevant")?"Fairly Relevant":$value->judgment, 
                (int)$value->total
            ];
        }

        return $results;
    }
}
