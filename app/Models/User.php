<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        return $this->belongsToMany(Query::class);
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

    public function getQueriesJudged()
    {
        return Judgment::where('user_id', $this->id)
            ->pluck('query_id')->all();
    }

    public function documentsJudgedByQuery($query_id = NULL)
    {
        if(!$query_id)
            $query_id = $this->current_query;

        return Judgment::where('user_id', $this->id)
            ->where('query_id', $query_id)
            ->pluck('document_id')->all();
    }
}
