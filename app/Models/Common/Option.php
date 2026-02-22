<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
	protected $table = 'options';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    	'slug',
        'title',
        'value'
    ];         
}