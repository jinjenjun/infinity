<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'birthday',
        'gender',
        'age',
        'managed_by',
        'invite_code_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'birthday'          => 'date',
        ];
    }

    public function managedBy()
    {
        return $this->belongsTo(User::class, 'managed_by');
    }

    public function managedUsers()
    {
        return $this->hasMany(User::class, 'managed_by');
    }

    public function inviteCode()
    {
        return $this->belongsTo(InviteCode::class, 'invite_code_id');
    }
}