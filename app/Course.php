<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';

    public function products() {
        return $this->hasMany('App\Product');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
