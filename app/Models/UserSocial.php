<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSocial extends Model
{
    protected $table = 'user_social';
    protected $fillable = [
        'id','name', 'email', 'avatar', 'user_id'
    ];
    protected $primaryKey = 'id';

    public $incrementing = false;

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
