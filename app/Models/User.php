<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    const ADMIN_ROLE = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function judgments()
    {
        return $this->hasMany(Judgment::class);
    }

    public function queries()
    {
        return $this->belongsToMany(Query::class)
            ->withPivot(['skip'])
            ->withTimestamps();
    }

    // Return if a query were skipped by the user
    public function querySkipped($query_id)
    {
        foreach ($this->queries as $query)
            if($query->id == $query_id)
                return $query->pivot->skip;
    }

    // Return queries skipped by the user
    public function queriesSkipped($counter=True)
    {
        $skipped = [];
        foreach ($this->queries as $query)
            if($query->pivot->skip)
                array_push($skipped, $query->id);

        if($counter)
            return count($skipped);
        return $skipped;
    }

    // Return queries completed by the user
    public function queriesCompleted($counter=True)
    {
        $queries = Query::whereIn('id', $this->queries->map->id)
            ->whereNotIn('id', $this->queriesSkipped(False))
            ->where('id', '!=', $this->current_query)
            ->get();

        if($counter)
            return count($queries);
        return $queries->map->id;
    }

    public function isAdmin()
    {
        return $this->role === self::ADMIN_ROLE;
    }

    public function setCurrentQuery($query_id = NULL)
    {
        $this->current_query = $query_id;
        $this->save();
    }

    public function getCurrentQuery()
    {
        return Query::where('id', $this->current_query)->first();
    }

    // Documents judged by the user for a query
    public function documentsJudgedByQuery($query_id = NULL)
    {
        if(!$query_id)
            $query_id = $this->current_query;

        return Judgment::where('user_id', $this->id)
            ->where('query_id', $query_id)
            ->pluck('document_id')->all();
    }

    public static function userStatsData($onlyJudges = True)
    {
        $users = User::all();
  
        $results = [];
        foreach ($users as $key => $user) {
            $judgments = $user->judgments()->count();
            $queries = $user->queriesCompleted();

            if($onlyJudges && $judgments == 0 || $user->name == 'admin')
                continue;

            $results[++$key] = ['name' => $user->name, 'judgments' => $judgments, 'queries' => $queries];
        }

        usort($results, function ($item1, $item2) {
            return $item2['judgments'] <=> $item1['judgments'];
        });

        return $results;
    }

    /**
     * Delete user judgments for a specific query
     *
     * @param  \App\Query  $query
     * @return \Illuminate\Http\Response
     */
    public function eraseJudgments(Query $query)
    {
        // Documents judged by the user for the query
        $documents_judged = $this->documentsJudgedByQuery($query->id);

        $documents = Document::whereIn('id', $documents_judged)->get();

        // Before delete judgments, update doc-query pairs pivot columns
        foreach ($documents as $document) {
            $pivot_judgments = $query->documentJudgments($document->id);
            $query->documents()->updateExistingPivot(
                $document->id, [
                    'judgments' => $pivot_judgments-1, 
                    'status' => 'review']);
        }

        // Delete tiebreaker judgments for the query
        Judgment::where('query_id', $query->id)
            ->where('untie', True)
            ->delete();

        // Delete user judgments for the query
        Judgment::where('query_id', $query->id)
            ->where('user_id', $this->id)
            ->delete();
    }
}
