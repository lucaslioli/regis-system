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

    // Return if a query were skiped by the user
    public function querySkipped($query_id)
    {
        foreach ($this->queries as $query)
            if($query->id == $query_id)
                return $query->pivot->skip;
    }

    // Return the number of queries completed
    public function queriesCompleted()
    {
        if($this->current_query != NULL)
            return $this->queries->count()-1;

        return $this->queries->count();
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

    public static function userStatsData()
    {
        $users = User::all();
  
        $results = [];
        foreach ($users as $key => $user) {
            $judgments = $user->judgments()->count();
            $queries = ($user->current_query)?$user->queries()->count()-1:$user->queries()->count();

            if($judgments == 0)
                continue;

            $results[++$key] = ['name' => $user->name, 'judgments' => $judgments, 'queries' => $queries];
        }

        usort($results, function ($item1, $item2) {
            return $item2['judgments'] <=> $item1['judgments'];
        });

        return $results;
    }
}
