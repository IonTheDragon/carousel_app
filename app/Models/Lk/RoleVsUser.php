<?php

namespace App\Models\Lk;

use Illuminate\Database\Eloquent\Model;

class RoleVsUser extends Model
{
	protected $table = 'role_vs_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'role_id'
    ];      
}