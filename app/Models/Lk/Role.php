<?php

namespace App\Models\Lk;

use Illuminate\Database\Eloquent\Model;
use App\Models\Lk\User;

class Role extends Model
{
	protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    	'slug',
        'title'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_vs_user');
    }          
}