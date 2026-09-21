<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'birthday',
        'gender',
        'age',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birthday' => 'date',
        ];
    }

    // 我所屬的 admin（會員身分）
    public function admins()
    {
        return $this->belongsToMany(User::class, 'admin_user', 'user_id', 'admin_id')
            ->withPivot('invite_code_id')
            ->withTimestamps();
    }

    // 我管理的會員
    public function members()
    {
        return $this->belongsToMany(User::class, 'admin_user', 'admin_id', 'user_id')
            ->withPivot('invite_code_id')
            ->withTimestamps();
    }
}
