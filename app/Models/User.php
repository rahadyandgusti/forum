<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $guard = ['id'];
    protected $primaryKey = 'id';

    protected $fillable = [
        'name', 'email', 'password', 'username', 'avatar', 'role_id', 
    ];

    public function social() {
        return $this->hasMany('App\Models\UserSocial', 'user_id', 'id');
    }
}
