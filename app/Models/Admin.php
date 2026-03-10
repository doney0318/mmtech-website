<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'mm_admin';

    protected $fillable = [
        'username',
        'password',
        'email',
        'nickname',
        'status',
        'last_login_ip',
        'last_login_time',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function setPasswordAttribute(string $value): void
    {
        if (str_starts_with($value, '$2y$') || str_starts_with($value, '$argon2')) {
            $this->attributes['password'] = $value;
            return;
        }

        $this->attributes['password'] = bcrypt($value);
    }
}
